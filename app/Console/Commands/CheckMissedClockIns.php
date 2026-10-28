<?php

namespace App\Console\Commands;

use App\Models\Master\Tenant;
use App\Models\Setting;
use App\Models\Shift;
use App\Notifications\MissedClockInNotification;
use App\Services\TenantService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckMissedClockIns extends Command
{
    protected $signature = 'shifts:check-missed-clockins';
    protected $description = 'For each tenant, resolve each user\'s active shift (same as the API) and push a clock-in reminder from the shift start time for 1 hour (every run).';

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

        $tenants = Tenant::on('master')->where('is_active', true)->get();
        $this->logBoth('Active tenants found: ' . $tenants->count());

        foreach ($tenants as $tenant) {
            try {
                TenantService::setTenant($tenant);
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
        $tz  = Setting::get('timezone', config('app.timezone', 'UTC'));
        $now = Carbon::now($tz);
        $yesterday = $now->copy()->subDay()->toDateString();

        $this->logBoth('--------------------------------------------------');
        $this->logBoth("DATABASE: {$tenant->db_name}  (Tenant: {$tenant->name})");
        $this->logBoth("Now: {$now->toDateTimeString()} ({$tz})");

        // Same scope as the API getActiveShift: shifts from yesterday onwards (overnight + upcoming),
        // BOTH types (site & runsheet), only those assigned to a user.
        $shifts = Shift::with(['schedule.user', 'site', 'weeklyRunSheet', 'attendances'])
            ->whereDate('date', '>=', $yesterday)
            ->whereHas('schedule', fn ($q) => $q->whereNotNull('user_id'))
            ->get()
            ->filter(fn ($s) => $s->schedule && $s->schedule->user);

        $this->logBoth("Shifts (from {$yesterday} onwards, assigned): {$shifts->count()}");

        if ($shifts->isEmpty()) {
            $this->logBoth('=> No assigned shifts in this database. Nothing to check.');
            return;
        }

        // Group by user, then resolve ONE active shift per user (exactly like the API).
        $byUser = $shifts->groupBy(fn ($s) => $s->schedule->user->id);
        $pushCount = 0;

        foreach ($byUser as $userId => $userShifts) {
            $user = $userShifts->first()->schedule->user;

            // Map real start/end datetimes (overnight aware)
            $mapped = $userShifts->map(function ($shift) use ($tz) {
                $startDt = Carbon::parse($shift->date . ' ' . $shift->start_time, $tz);
                $endDt   = Carbon::parse($shift->date . ' ' . $shift->end_time, $tz);
                if ($endDt->lt($startDt)) {
                    $endDt->addDay();
                }
                $shift->start_datetime = $startDt;
                $shift->end_datetime   = $endDt;
                return $shift;
            });

            // 1. Currently active shift (now between start/end), skip if already completed (clocked out)
            $active = $mapped->first(function ($shift) use ($now, $userId) {
                if (! $now->between($shift->start_datetime, $shift->end_datetime)) {
                    return false;
                }
                $att = $shift->attendances->firstWhere('user_id', $userId);
                $isCompleted = $att && $att->clock_out_at;
                return ! $isCompleted;
            });

            // 2. Else nearest upcoming shift (same as API) — for display/logging
            if (! $active) {
                $active = $mapped->filter(fn ($s) => $s->start_datetime->gt($now))
                    ->sortBy('start_datetime')
                    ->first();
            }

            if (! $active) {
                $this->logBoth("User '{$user->name}' (id={$userId}) => active shift: NULL (koi active ya upcoming shift nahi).");
                continue;
            }

            $locationName = $active->type === 'runsheet'
                ? ($active->weeklyRunSheet?->name ?? 'Run Sheet')
                : ($active->site?->name ?? 'N/A');

            $this->logBoth("User '{$user->name}' (id={$userId}) => active shift #{$active->id} | type: {$active->type} | {$active->start_datetime->toDateTimeString()} -> {$active->end_datetime->toDateTimeString()} | location: {$locationName}");

            // Notification window: shift START time .. START + 1 hour
            $windowStart = $active->start_datetime->copy();
            $windowEnd   = $active->start_datetime->copy()->addHour();

            if (! $now->between($windowStart, $windowEnd)) {
                $this->logBoth("   => NOT in notify window ({$windowStart->format('g:i A')} - {$windowEnd->format('g:i A')}). No push now.");
                continue;
            }

            // Already clocked in? then no reminder needed
            $att = $active->attendances->firstWhere('user_id', $userId);
            if ($att && $att->clock_in_at) {
                $this->logBoth("   => In window but user already CLOCKED IN. No reminder needed.");
                continue;
            }

            if (empty($user->fcm_token)) {
                $this->logBoth("   => In window but user has NO fcm_token. Push skipped.", 'warn');
                continue;
            }

            $minutesLate    = (int) $active->start_datetime->diffInMinutes($now);
            $startFormatted = $active->start_datetime->format('g:i A');
            $uniqueTag      = 'clock_in_' . $active->id . '_' . $now->timestamp;

            $this->logBoth("   => IN WINDOW. Sending push to '{$user->name}' (tag: {$uniqueTag}, {$minutesLate} min after start)...");

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
                $this->logBoth("   => PUSH FAILED for '{$user->name}' (see the error log line above).", 'warn');
            }
        }

        $this->logBoth("SUMMARY [{$tenant->db_name}]: users checked = {$byUser->count()}, pushes sent = {$pushCount}");
    }
}
