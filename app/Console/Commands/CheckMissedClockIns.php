<?php

namespace App\Console\Commands;

use App\Models\Master\Tenant;
use App\Models\Shift;
use App\Models\ShiftAttendance;
use App\Notifications\MissedClockInNotification;
use App\Services\TenantService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CheckMissedClockIns extends Command
{
    protected $signature = 'shifts:check-missed-clockins';
    protected $description = 'For each tenant, find currently ACTIVE shifts and push a clock-in reminder to the assigned guard (user) only.';

    public function handle(): int
    {
        $this->info('Checking active shifts across all tenants...');

        // 1. Every tenant has its own database
        $tenants = Tenant::on('master')->where('is_active', true)->get();

        foreach ($tenants as $tenant) {
            try {
                TenantService::setTenant($tenant); // switch to this tenant's DB
                $this->processTenant($tenant);
            } catch (\Throwable $e) {
                Log::error("CheckMissedClockIns: tenant [{$tenant->id}] error — " . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
                $this->warn("Tenant [{$tenant->name}] failed: " . $e->getMessage());
            }
        }

        TenantService::resetTenant();
        $this->info('Done.');
        return self::SUCCESS;
    }

    private function processTenant(Tenant $tenant): void
    {
        $tz  = config('app.timezone', 'Asia/Karachi');
        $now = Carbon::now($tz);

        $today     = $now->toDateString();
        $yesterday = $now->copy()->subDay()->toDateString();

        // 2. This tenant's shifts (today + yesterday for overnight), BOTH types (site & runsheet)
        $shifts = Shift::with(['schedule.user', 'site', 'weeklyRunSheet'])
            ->whereIn('date', [$today, $yesterday])
            ->get();

        foreach ($shifts as $shift) {
            // Build the shift's real start/end datetime (overnight aware)
            $startDt = Carbon::parse($shift->date . ' ' . $shift->start_time, $tz);
            $endDt   = Carbon::parse($shift->date . ' ' . $shift->end_time, $tz);
            if ($endDt->lte($startDt)) {
                $endDt->addDay();
            }

            // Only CURRENTLY ACTIVE shifts (now is between start and end)
            if (! $now->between($startDt, $endDt)) {
                continue;
            }

            // 3. Get the assigned user (guard): shift -> schedule -> user
            $user = $shift->schedule?->user;
            if (! $user || empty($user->fcm_token)) {
                continue; // no user or no device token -> nothing to push
            }

            // Only remind if the guard has NOT clocked in yet for this shift
            $hasClockedIn = ShiftAttendance::where('shift_id', $shift->id)
                ->where('user_id', $user->id)
                ->whereNotNull('clock_in_at')
                ->exists();
            if ($hasClockedIn) {
                continue;
            }

            // 4. Location name based on shift type
            if ($shift->type === 'runsheet') {
                $locationName = $shift->weeklyRunSheet?->name ?? 'Run Sheet';
            } else {
                $locationName = $shift->site?->name ?? 'N/A';
            }

            $minutesLate    = (int) $startDt->diffInMinutes($now);
            $startFormatted = $startDt->format('g:i A');

            // Unique tag per push => every notification shows separately (never overrides the previous one)
            $uniqueTag = 'clock_in_' . $shift->id . '_' . $now->timestamp;

            try {
                Notification::send($user, new MissedClockInNotification(
                    $user->name,
                    $locationName,
                    $startFormatted,
                    $minutesLate,
                    true,        // guard-worded message
                    $uniqueTag   // unique tag => no override
                ));
                $this->info("  [{$tenant->name}] Push sent to guard: {$user->name} ({$locationName})");
            } catch (\Throwable $e) {
                Log::error("CheckMissedClockIns [{$tenant->name}]: push failed for guard [{$user->email}] — " . $e->getMessage());
                $this->warn("  Push failed for {$user->name}: " . $e->getMessage());
            }
        }
    }
}
