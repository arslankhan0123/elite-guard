<?php

namespace App\Console\Commands;

use App\Models\AdminNotification;
use App\Models\Master\Tenant;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\User;
use App\Notifications\MissedClockOutNotification;
use App\Services\TenantMailSender;
use App\Services\TenantService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckMissedClockOuts extends Command
{
    protected $signature = 'shifts:check-missed-clockouts';
    protected $description = 'For each tenant, notify a guard (and all other SuperAdmins) from 10 min before shift END until 30 min after, when the guard clocked in but has not clocked out.';

    private function logBoth(string $message, string $level = 'info'): void
    {
        $prefix = '[CheckMissedClockOuts] ';
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
        $this->logBoth('START: Checking missed clock-outs across all tenants...');

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

        $superAdmins = User::where('role', 'SuperAdmin')->get();
        $withToken = $superAdmins->filter(fn ($a) => ! empty($a->fcm_token))->count();
        $withEmail = $superAdmins->filter(fn ($a) => ! empty($a->email))->count();
        $this->logBoth("SuperAdmins: total={$superAdmins->count()}, with_token={$withToken}, with_email={$withEmail}");

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

            // Target = the shift whose END window (end-10min .. end+30min) contains NOW
            $target = $mapped->filter(function ($shift) use ($now) {
                $ws = $shift->end_datetime->copy()->subMinutes(10);
                $we = $shift->end_datetime->copy()->addMinutes(30);
                return $now->between($ws, $we);
            })->sortBy('end_datetime')->first();

            if (! $target) {
                $this->logBoth("User '{$user->name}' (id={$userId}) => no shift in clock-out window (end-10min .. end+30min) right now.");
                continue;
            }

            $locationName = $target->type === 'runsheet'
                ? ($target->weeklyRunSheet?->name ?? 'Run Sheet')
                : ($target->site?->name ?? 'N/A');

            $this->logBoth("User '{$user->name}' (id={$userId}) => clock-out target shift #{$target->id} | type: {$target->type} | ends {$target->end_datetime->toDateTimeString()} | location: {$locationName}");

            // Must have clocked IN but NOT clocked OUT
            $att = $target->attendances->firstWhere('user_id', $userId);
            if (! $att || ! $att->clock_in_at) {
                $this->logBoth("   => In window but guard NEVER clocked in (missed-clockin job handles that). Skipping.");
                continue;
            }
            if ($att->clock_out_at) {
                $this->logBoth("   => In window but guard ALREADY clocked out. Nothing to remind.");
                continue;
            }

            $isBeforeEnd   = $now->lt($target->end_datetime);
            $minutesPast   = $isBeforeEnd ? 0 : (int) $target->end_datetime->diffInMinutes($now);
            $minutesBefore = $isBeforeEnd ? (int) $now->diffInMinutes($target->end_datetime) : 0;
            $endFormatted  = $target->end_datetime->format('g:i A');
            $dateFormatted = $target->end_datetime->format('d M Y');
            $shiftName     = $target->shift_name ?: 'Regular Shift';
            $timeContext   = $isBeforeEnd
                ? "Shift ends in {$minutesBefore} min (at {$endFormatted})"
                : "Shift ended {$minutesPast} min ago (at {$endFormatted})";

            // ── Save DB notification ───────────────────────────────────────
            AdminNotification::create([
                'type'     => 'missed_clock_out',
                'title'    => "Missed Clock-Out: {$user->name}",
                'message'  => "{$user->name} has not clocked out at {$locationName}. {$timeContext}.",
                'shift_id' => $target->id,
                'user_id'  => $user->id,
            ]);
            $this->logBoth("   => DB notification saved.");

            // ── 1) Push to the GUARD (their own reminder) — 1 only ─────────
            if (! empty($user->fcm_token)) {
                $guardTag = 'clock_out_guard_' . $target->id . '_' . $now->timestamp;
                $this->logBoth("   => IN WINDOW. Guard push to '{$user->name}' (tag: {$guardTag})...");
                $ok = MissedClockOutNotification::pushTo(
                    $user, $user->name, $locationName, $endFormatted, $minutesPast, $minutesBefore, $isBeforeEnd, true, $guardTag
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
                $superAdmins, (int) $userId, $target->id, $user->name, $locationName, $endFormatted, $minutesPast, $minutesBefore, $isBeforeEnd, $now
            );

            // ── 3) Email SuperAdmins (skip the guard himself) ──────────────
            $emails += $this->emailSuperAdmins(
                $superAdmins, (int) $userId, $user->name, $locationName, $shiftName, $dateFormatted, $endFormatted, $timeContext
            );
        }

        $this->logBoth("SUMMARY [{$tenant->db_name}]: users checked = {$byUser->count()}, guard pushes = {$guardPushes}, admin pushes = {$adminPushes}, emails = {$emails}");
    }

    private function pushSuperAdmins($superAdmins, int $skipUserId, int $shiftId, string $guardName, string $locationName, string $endFormatted, int $minutesPast, int $minutesBefore, bool $isBeforeEnd, Carbon $now): int
    {
        $sent = 0;
        foreach ($superAdmins as $admin) {
            if ((int) $admin->id === $skipUserId) {
                continue;
            }
            if (empty($admin->fcm_token)) {
                continue;
            }
            $adminTag = 'clock_out_admin_' . $shiftId . '_' . $admin->id . '_' . $now->timestamp;
            $ok = MissedClockOutNotification::pushTo(
                $admin, $guardName, $locationName, $endFormatted, $minutesPast, $minutesBefore, $isBeforeEnd, false, $adminTag
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

    private function emailSuperAdmins($superAdmins, int $skipUserId, string $guardName, string $locationName, string $shiftName, string $dateFormatted, string $endFormatted, string $timeContext): int
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
                $html = view('emails.missed-clock-out', [
                    'guardName'    => $guardName,
                    'siteName'     => $locationName,
                    'shiftName'    => $shiftName,
                    'shiftDate'    => $dateFormatted,
                    'endTime'      => $endFormatted,
                    'timeContext'  => $timeContext,
                    'dashboardUrl' => $dashboardUrl,
                ])->render();

                TenantMailSender::sendHtml(
                    $admin->email,
                    $admin->name,
                    "⚠️ Missed Clock-Out: {$guardName} at {$locationName}",
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
