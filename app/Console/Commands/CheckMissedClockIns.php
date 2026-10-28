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

class CheckMissedClockIns extends Command
{
    protected $signature = 'shifts:check-missed-clockins';
    protected $description = 'For each tenant, find currently ACTIVE shifts and push a clock-in reminder to the assigned guard (user) only.';

    /** Write a line to BOTH the console and storage/logs/laravel.log */
    private function logBoth(string $message, string $level = 'info'): void
    {
        $prefix = '[CheckMissedClockIns] ';
        if ($level === 'warn') {
            $this->warn($message);
            Log::warning($prefix . $message);
        } else {
            $this->line($message);
            Log::info($prefix . $message);
        }
    }

    public function handle(): int
    {
        $this->logBoth('==================================================');
        $this->logBoth('START: Checking active shifts across all tenants...');

        // 1. Every tenant has its own database
        $tenants = Tenant::on('master')->where('is_active', true)->get();
        $this->logBoth('Active tenants found: ' . $tenants->count());

        foreach ($tenants as $tenant) {
            try {
                TenantService::setTenant($tenant); // switch to this tenant's DB
                $this->processTenant($tenant);
            } catch (\Throwable $e) {
                $this->logBoth("Tenant [{$tenant->name}] FAILED: " . $e->getMessage() . " (line {$e->getLine()})", 'warn');
            }
        }

        TenantService::resetTenant();
        $this->logBoth('END: Done.');
        $this->logBoth('==================================================');
        return self::SUCCESS;
    }

    private function processTenant(Tenant $tenant): void
    {
        // Per-tenant timezone from Timezone Settings; fall back to app config
        $tz  = \App\Models\Setting::get('timezone', config('app.timezone', 'UTC'));
        $now = Carbon::now($tz);

        $today     = $now->toDateString();
        $yesterday = $now->copy()->subDay()->toDateString();

        $this->logBoth('--------------------------------------------------');
        $this->logBoth("DATABASE: {$tenant->db_name}  (Tenant: {$tenant->name})");
        $this->logBoth("Now: {$now->toDateTimeString()} ({$tz}) | checking shift dates: {$yesterday}, {$today}");

        // 2. This tenant's shifts (today + yesterday for overnight), BOTH types (site & runsheet)
        $shifts = Shift::with(['schedule.user', 'site', 'weeklyRunSheet'])
            ->whereIn('date', [$today, $yesterday])
            ->get();

        $this->logBoth("Total shifts (today + yesterday): {$shifts->count()}");

        if ($shifts->isEmpty()) {
            $this->logBoth("=> No shifts in this database. Nothing to check.");
            return;
        }

        $activeCount = 0;
        $pushCount   = 0;

        foreach ($shifts as $shift) {
            $userName = $shift->schedule?->user?->name ?? 'NO USER (unassigned)';

            // Build the shift's real start/end datetime (overnight aware)
            $startDt = Carbon::parse($shift->date . ' ' . $shift->start_time, $tz);
            $endDt   = Carbon::parse($shift->date . ' ' . $shift->end_time, $tz);
            $overnight = false;
            if ($endDt->lte($startDt)) {
                $endDt->addDay();
                $overnight = true;
            }

            $isActive = $now->between($startDt, $endDt);
            $window   = "{$startDt->toDateTimeString()} -> {$endDt->toDateTimeString()}" . ($overnight ? ' (overnight)' : '');

            $this->logBoth("Shift #{$shift->id} | User: {$userName} | type: {$shift->type} | {$window} | ACTIVE NOW? " . ($isActive ? 'YES' : 'NO'));

            if (! $isActive) {
                $this->logBoth("   => User '{$userName}' ki ye shift active nahi hai right now. Skipping.");
                continue;
            }

            $activeCount++;

            // 3. Get the assigned user (guard): shift -> schedule -> user
            $user = $shift->schedule?->user;
            if (! $user) {
                $this->logBoth("   => ACTIVE shift #{$shift->id} but NO user assigned (schedule/user null). Skipping.", 'warn');
                continue;
            }

            // Location name based on shift type
            if ($shift->type === 'runsheet') {
                $locationName = $shift->weeklyRunSheet?->name ?? 'Run Sheet';
            } else {
                $locationName = $shift->site?->name ?? 'N/A';
            }

            $this->logBoth("   => ACTIVE shift of user: {$user->name} | location: {$locationName} | fcm_token: " . (!empty($user->fcm_token) ? 'present' : 'MISSING'));

            if (empty($user->fcm_token)) {
                $this->logBoth("   => User '{$user->name}' ka fcm_token nahi hai. Push skip.", 'warn');
                continue;
            }

            // Only remind if the guard has NOT clocked in yet for this shift
            $hasClockedIn = ShiftAttendance::where('shift_id', $shift->id)
                ->where('user_id', $user->id)
                ->whereNotNull('clock_in_at')
                ->exists();

            if ($hasClockedIn) {
                $this->logBoth("   => User '{$user->name}' already CLOCKED IN for shift #{$shift->id}. No reminder needed.");
                continue;
            }

            $minutesLate    = (int) $startDt->diffInMinutes($now);
            $startFormatted = $startDt->format('g:i A');

            // Unique tag per push => every notification shows separately (never overrides the previous one)
            $uniqueTag = 'clock_in_' . $shift->id . '_' . $now->timestamp;

            // Send directly and get the REAL result (success / actual FCM error logged inside pushTo)
            $this->logBoth("   => Sending push to '{$user->name}' (tag: {$uniqueTag}, {$minutesLate} min late)...");
            $ok = MissedClockInNotification::pushTo(
                $user,
                $user->name,
                $locationName,
                $startFormatted,
                $minutesLate,
                true,        // guard-worded message
                $uniqueTag   // unique tag => no override
            );
            if ($ok) {
                $pushCount++;
                $this->logBoth("   => PUSH SUCCESS for '{$user->name}'.");
            } else {
                $this->logBoth("   => PUSH FAILED for '{$user->name}' (see error log line above).", 'warn');
            }
        }

        $this->logBoth("SUMMARY [{$tenant->db_name}]: active shifts = {$activeCount}, pushes sent = {$pushCount}");
    }
}
