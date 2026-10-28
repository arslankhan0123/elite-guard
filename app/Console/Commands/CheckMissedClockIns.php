<?php

namespace App\Console\Commands;

use App\Models\Master\Tenant;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\User;
use App\Notifications\MissedClockInNotification;
use App\Services\TenantMailSender;
use App\Services\TenantService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckMissedClockIns extends Command
{
    protected $signature = 'shifts:check-missed-clockins';
    protected $description = 'For each tenant, resolve each user\'s active shift (like the API) and, from the shift start time for 1 hour, push a clock-in reminder to the guard + email all SuperAdmins.';

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
        $tz  = Setting::get('timezone', config('app.timezone', 'UTC'));
        $now = Carbon::now($tz);
        $yesterday = $now->copy()->subDay()->toDateString();

        $this->logBoth('--------------------------------------------------');
        $this->logBoth("DATABASE: {$tenant->db_name}  (Tenant: {$tenant->name})");
        $this->logBoth("Now: {$now->toDateTimeString()} ({$tz})");

        // SuperAdmins of this tenant (for email alerts) — fetched once
        $superAdmins = User::where('role', 'SuperAdmin')->whereNotNull('email')->get();
        $this->logBoth("SuperAdmins with email in this tenant: {$superAdmins->count()}");

        // Shifts from yesterday onwards (overnight + upcoming), BOTH types, assigned only
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

        $byUser    = $shifts->groupBy(fn ($s) => $s->schedule->user->id);
        $pushCount = 0;
        $mailCount = 0;

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

            // 1. Currently active shift (skip if completed)
            $active = $mapped->first(function ($shift) use ($now, $userId) {
                if (! $now->between($shift->start_datetime, $shift->end_datetime)) {
                    return false;
                }
                $att = $shift->attendances->firstWhere('user_id', $userId);
                return ! ($att && $att->clock_out_at);
            });

            // 2. Else nearest upcoming (for display/log)
            if (! $active) {
                $active = $mapped->filter(fn ($s) => $s->start_datetime->gt($now))
                    ->sortBy('start_datetime')->first();
            }

            if (! $active) {
                $this->logBoth("User '{$user->name}' (id={$userId}) => active shift: NULL (koi active ya upcoming shift nahi).");
                continue;
            }

            $locationName = $active->type === 'runsheet'
                ? ($active->weeklyRunSheet?->name ?? 'Run Sheet')
                : ($active->site?->name ?? 'N/A');

            $this->logBoth("User '{$user->name}' (id={$userId}) => active shift #{$active->id} | type: {$active->type} | {$active->start_datetime->toDateTimeString()} -> {$active->end_datetime->toDateTimeString()} | location: {$locationName}");

            // Notification window: START .. START + 1 hour
            $windowStart = $active->start_datetime->copy();
            $windowEnd   = $active->start_datetime->copy()->addHour();

            if (! $now->between($windowStart, $windowEnd)) {
                $this->logBoth("   => NOT in notify window ({$windowStart->format('g:i A')} - {$windowEnd->format('g:i A')}). No push now.");
                continue;
            }

            // Already clocked in?
            $att = $active->attendances->firstWhere('user_id', $userId);
            if ($att && $att->clock_in_at) {
                $this->logBoth("   => In window but user already CLOCKED IN. No reminder needed.");
                continue;
            }

            $minutesLate    = (int) $active->start_datetime->diffInMinutes($now);
            $startFormatted = $active->start_datetime->format('g:i A');
            $dateFormatted  = $active->start_datetime->format('d M Y');
            $shiftName      = $active->shift_name ?: 'Regular Shift';
            $uniqueTag      = 'clock_in_' . $active->id . '_' . $now->timestamp;

            // ── Push to the guard (their own reminder) ─────────────────────
            if (! empty($user->fcm_token)) {
                $this->logBoth("   => IN WINDOW. Sending push to '{$user->name}' (tag: {$uniqueTag}, {$minutesLate} min after start)...");
                $ok = MissedClockInNotification::pushTo(
                    $user, $user->name, $locationName, $startFormatted, $minutesLate, true, $uniqueTag
                );
                if ($ok) {
                    $pushCount++;
                    $this->logBoth("   => PUSH SUCCESS for '{$user->name}'.");
                } else {
                    $this->logBoth("   => PUSH FAILED for '{$user->name}' (see error log line above).", 'warn');
                }
            } else {
                $this->logBoth("   => In window but user has NO fcm_token. Push skipped.", 'warn');
            }

            // ── Email ALL SuperAdmins: "<guard> has not clocked in" ────────
            $mailCount += $this->emailSuperAdmins(
                $superAdmins, $user->name, $locationName, $shiftName, $dateFormatted, $startFormatted, $minutesLate
            );
        }

        $this->logBoth("SUMMARY [{$tenant->db_name}]: users checked = {$byUser->count()}, pushes sent = {$pushCount}, emails sent = {$mailCount}");
    }

    /** Email every SuperAdmin that a guard has not clocked in. Returns number of emails sent. */
    private function emailSuperAdmins($superAdmins, string $guardName, string $locationName, string $shiftName, string $dateFormatted, string $startFormatted, int $minutesLate): int
    {
        if ($superAdmins->isEmpty()) {
            return 0;
        }

        $sent = 0;
        $dashboardUrl = url('/dashboard');

        foreach ($superAdmins as $admin) {
            try {
                $html = view('emails.missed-clock-in', [
                    'guardName'    => $guardName,
                    'siteName'     => $locationName,
                    'shiftName'    => $shiftName,
                    'shiftDate'    => $dateFormatted,
                    'startTime'    => $startFormatted,
                    'minutesLate'  => $minutesLate,
                    'dashboardUrl' => $dashboardUrl,
                ])->render();

                TenantMailSender::sendHtml(
                    $admin->email,
                    $admin->name,
                    "⚠️ Missed Clock-In: {$guardName} at {$locationName}",
                    $html
                );

                $sent++;
                $this->logBoth("      -> Email sent to SuperAdmin: {$admin->email}");
            } catch (\Throwable $e) {
                $this->logBoth("      -> Email FAILED to SuperAdmin {$admin->email}: " . $e->getMessage(), 'warn');
            }
        }

        return $sent;
    }
}
