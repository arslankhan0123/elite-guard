<?php

namespace App\Console\Commands;

use App\Models\AdminNotification;
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
    protected $description = 'For each tenant, resolve each user\'s active shift (like the API) and, from the shift start time for 1 hour: push a clock-in reminder to the guard + push + email ALL OTHER SuperAdmins, and save a DB notification.';

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

        // All SuperAdmins of this tenant (for admin push + email)
        $superAdmins = User::where('role', 'SuperAdmin')->get();
        $withToken = $superAdmins->filter(fn ($a) => ! empty($a->fcm_token))->count();
        $withEmail = $superAdmins->filter(fn ($a) => ! empty($a->email))->count();
        $this->logBoth("SuperAdmins: total={$superAdmins->count()}, with_token={$withToken}, with_email={$withEmail}");

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

        $byUser      = $shifts->groupBy(fn ($s) => $s->schedule->user->id);
        $guardPushes = 0;
        $adminPushes = 0;
        $emails      = 0;

        foreach ($byUser as $userId => $userShifts) {
            $user = $userShifts->first()->schedule->user;

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

            $active = $mapped->first(function ($shift) use ($now, $userId) {
                if (! $now->between($shift->start_datetime, $shift->end_datetime)) {
                    return false;
                }
                $att = $shift->attendances->firstWhere('user_id', $userId);
                return ! ($att && $att->clock_out_at);
            });

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

            $att = $active->attendances->firstWhere('user_id', $userId);
            if ($att && $att->clock_in_at) {
                $this->logBoth("   => In window but user already CLOCKED IN. No reminder needed.");
                continue;
            }

            $minutesLate    = (int) $active->start_datetime->diffInMinutes($now);
            $startFormatted = $active->start_datetime->format('g:i A');
            $dateFormatted  = $active->start_datetime->format('d M Y');
            $shiftName      = $active->shift_name ?: 'Regular Shift';

            // ── Save DB notification (web bell) ────────────────────────────
            AdminNotification::create([
                'type'     => 'missed_clock_in',
                'title'    => "Missed Clock-In: {$user->name}",
                'message'  => "{$user->name} has not clocked in for their shift at {$locationName}. Shift started at {$startFormatted} ({$minutesLate} min ago).",
                'shift_id' => $active->id,
                'user_id'  => $user->id,
            ]);
            $this->logBoth("   => DB notification saved.");

            // ── 1) Push to the GUARD (their own reminder) — 1 only ─────────
            if (! empty($user->fcm_token)) {
                $guardTag = 'clock_in_guard_' . $active->id . '_' . $now->timestamp;
                $this->logBoth("   => IN WINDOW. Guard push to '{$user->name}' (tag: {$guardTag}, {$minutesLate} min after start)...");
                $ok = MissedClockInNotification::pushTo(
                    $user, $user->name, $locationName, $startFormatted, $minutesLate, true, $guardTag
                );
                if ($ok) {
                    $guardPushes++;
                    $this->logBoth("   => GUARD PUSH SUCCESS for '{$user->name}'.");
                } else {
                    $this->logBoth("   => GUARD PUSH FAILED for '{$user->name}' (see error log line above).", 'warn');
                }
            } else {
                $this->logBoth("   => In window but guard has NO fcm_token. Guard push skipped.", 'warn');
            }

            // ── 2) Admin push to OTHER SuperAdmins (skip the guard himself) ─
            $adminPushes += $this->pushSuperAdmins(
                $superAdmins, (int) $userId, $active->id, $user->name, $locationName, $startFormatted, $minutesLate, $now
            );

            // ── 3) Email SuperAdmins (skip the guard himself) ──────────────
            $emails += $this->emailSuperAdmins(
                $superAdmins, (int) $userId, $user->name, $locationName, $shiftName, $dateFormatted, $startFormatted, $minutesLate
            );
        }

        $this->logBoth("SUMMARY [{$tenant->db_name}]: users checked = {$byUser->count()}, guard pushes = {$guardPushes}, admin pushes = {$adminPushes}, emails = {$emails}");
    }

    /** Push the admin-worded alert to every SuperAdmin EXCEPT the guard himself. */
    private function pushSuperAdmins($superAdmins, int $skipUserId, int $shiftId, string $guardName, string $locationName, string $startFormatted, int $minutesLate, Carbon $now): int
    {
        $sent = 0;
        foreach ($superAdmins as $admin) {
            if ((int) $admin->id === $skipUserId) {
                continue; // this super-admin IS the guard — he already got his own guard push
            }
            if (empty($admin->fcm_token)) {
                continue;
            }
            $adminTag = 'clock_in_admin_' . $shiftId . '_' . $admin->id . '_' . $now->timestamp;
            $ok = MissedClockInNotification::pushTo(
                $admin, $guardName, $locationName, $startFormatted, $minutesLate, false, $adminTag
            );
            if ($ok) {
                $sent++;
                $this->logBoth("      -> Admin push sent to SuperAdmin: {$admin->name} (id={$admin->id})");
            } else {
                $this->logBoth("      -> Admin push FAILED to SuperAdmin: {$admin->name} (id={$admin->id}, see error log).", 'warn');
            }
        }
        return $sent;
    }

    /** Email every SuperAdmin EXCEPT the guard himself. Returns number of emails sent. */
    private function emailSuperAdmins($superAdmins, int $skipUserId, string $guardName, string $locationName, string $shiftName, string $dateFormatted, string $startFormatted, int $minutesLate): int
    {
        $sent = 0;
        $dashboardUrl = url('/dashboard');

        foreach ($superAdmins as $admin) {
            if ((int) $admin->id === $skipUserId) {
                continue;
            }
            if (empty($admin->email)) {
                continue;
            }
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
