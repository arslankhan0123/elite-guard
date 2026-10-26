<?php

namespace App\Console\Commands;

use App\Models\AdminNotification;
use App\Models\Master\Tenant;
use App\Models\Shift;
use App\Models\ShiftAttendance;
use App\Models\User;
use App\Notifications\MissedClockInNotification;
use App\Services\TenantService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class CheckMissedClockIns extends Command
{
    protected $signature = 'shifts:check-missed-clockins';
    protected $description = 'Check active shifts (site & runsheet) where the guard has not clocked in within 1 hour of start time, and notify the guard + admins';

    public function handle(): int
    {
        $this->info('Checking missed clock-ins across all tenants...');

        $tenants = Tenant::on('master')->where('is_active', true)->get();

        foreach ($tenants as $tenant) {
            try {
                TenantService::setTenant($tenant);
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
        $tz = config('app.timezone', 'Asia/Karachi');
        $now = Carbon::now($tz);
        $today = $now->toDateString();
        // Include yesterday so overnight shifts starting before midnight are caught
        $yesterday = $now->copy()->subDay()->toDateString();

        // Fetch today's + yesterday's shifts — BOTH site & runsheet types.
        // (runsheet shifts have site_id = NULL, so we must NOT filter on site_id)
        $shifts = Shift::with(['schedule.user', 'site', 'weeklyRunSheet'])
            ->whereIn('date', [$today, $yesterday])
            ->get();

        foreach ($shifts as $shift) {
            // ── shift → schedule → user ────────────────────────────────────
            $user = $shift->schedule?->user;
            if (!$user) {
                continue; // unassigned shift — skip
            }

            // Build shift start datetime (handle overnight shifts)
            $startDt = Carbon::parse($shift->date . ' ' . $shift->start_time, $tz);
            $endDt = Carbon::parse($shift->date . ' ' . $shift->end_time, $tz);
            if ($endDt->lte($startDt)) {
                $endDt->addDay();
            }

            // ── Window check ───────────────────────────────────────────────
            // Notify ONLY if: startDt <= now <= startDt + 60 min
            // i.e., shift has started but not more than 1 hour ago
            if ($now->lt($startDt)) {
                continue; // shift hasn't started yet
            }
            if ($now->gt($startDt->copy()->addMinutes(60))) {
                continue; // more than 1 hour past start — window closed
            }

            // ── Already clocked in? ────────────────────────────────────────
            $hasClockedIn = ShiftAttendance::where('shift_id', $shift->id)
                ->where('user_id', $user->id)
                ->whereNotNull('clock_in_at')
                ->exists();

            if ($hasClockedIn) {
                continue; // guard clocked in — all good
            }

            // ── Location name (site OR runsheet) ───────────────────────────
            if ($shift->type === 'runsheet') {
                $locationName = $shift->weeklyRunSheet?->name ?? 'Run Sheet';
            } else {
                $locationName = $shift->site?->name ?? 'N/A';
            }

            // ── Create DB notification ─────────────────────────────────────
            // No dedup — notify on every cron run while window is open and guard hasn't clocked in
            $minutesLate = (int) $startDt->diffInMinutes($now);
            $shiftName = $shift->shift_name ?: 'Regular Shift';
            $startFormatted = $startDt->format('g:i A');
            $dateFormatted = $startDt->format('d M Y');

            AdminNotification::create([
                'type' => 'missed_clock_in',
                'title' => "Missed Clock-In: {$user->name}",
                'message' => "{$user->name} has not clocked in for their shift at {$locationName}. Shift started at {$startFormatted} ({$minutesLate} min ago).",
                'shift_id' => $shift->id,
                'user_id' => $user->id,
            ]);

            $this->info("  [{$tenant->name}] Notification created — Guard: {$user->name}, Shift: {$shiftName}, Location: {$locationName}");

            // ── Push notification → THE GUARD (guard-worded message) ───────
            if (!empty($user->fcm_token)) {
                try {
                    Notification::send($user, new MissedClockInNotification(
                        $user->name,
                        $locationName,
                        $startFormatted,
                        $minutesLate,
                        true // forGuard → "You haven't clocked in..."
                    ));
                    $this->info("    Push sent to Guard: {$user->name}");
                } catch (\Throwable $e) {
                    Log::error("CheckMissedClockIns: guard push failed for [{$user->email}] — " . $e->getMessage());
                    $this->warn("    Guard push failed for {$user->name}: " . $e->getMessage());
                }
            } else {
                $this->warn("    Guard {$user->name} has no fcm_token — skipping guard push");
            }

            // ── Push notification → SuperAdmins (admin-worded message) ─────
            $allSuperAdmins = User::where('role', 'SuperAdmin')->get();
            $superAdmins    = $allSuperAdmins->filter(fn ($a) => !empty($a->fcm_token));

            // Diagnostic: how many super-admins exist vs. how many have a usable token
            $diag = "SuperAdmins total={$allSuperAdmins->count()}, with_token={$superAdmins->count()}";
            $this->info("    {$diag}");
            Log::info("CheckMissedClockIns [{$tenant->name}] shift#{$shift->id} guard={$user->name}: {$diag}");
            if ($superAdmins->isEmpty()) {
                $this->warn("    No SuperAdmin with fcm_token in this tenant — nothing to push to admins.");
                Log::warning("CheckMissedClockIns [{$tenant->name}]: no SuperAdmin has an fcm_token.");
            }

            foreach ($superAdmins as $superAdmin) {
                try {
                    Notification::send($superAdmin, new MissedClockInNotification(
                        $user->name,
                        $locationName,
                        $startFormatted,
                        $minutesLate,
                        false // admin-worded → "Guard X has not clocked in..."
                    ));
                    $this->info("    Push sent to SuperAdmin: {$superAdmin->name} (id={$superAdmin->id})");
                    Log::info("CheckMissedClockIns [{$tenant->name}]: push OK → SuperAdmin {$superAdmin->name} (id={$superAdmin->id})");
                } catch (\Throwable $e) {
                    Log::error("CheckMissedClockIns [{$tenant->name}]: push FAILED → SuperAdmin {$superAdmin->name} (id={$superAdmin->id}, token=" . substr((string) $superAdmin->fcm_token, 0, 12) . "...) — " . $e->getMessage());
                    $this->warn("    Push FAILED for SuperAdmin {$superAdmin->name}: " . $e->getMessage());
                }
            }

            // ── Send email to all SuperAdmin users of this tenant ──────────
            $admins = User::where('role', 'SuperAdmin')
                ->whereNotNull('email')
                ->get();

            $dashboardUrl = url('/dashboard');

            foreach ($admins as $admin) {
                try {
                    $html = view('emails.missed-clock-in', [
                        'guardName' => $user->name,
                        'siteName' => $locationName,
                        'shiftName' => $shiftName,
                        'shiftDate' => $dateFormatted,
                        'startTime' => $startFormatted,
                        'minutesLate' => $minutesLate,
                        'dashboardUrl' => $dashboardUrl,
                    ])->render();

                    \App\Services\TenantMailSender::sendHtml(
                        $admin->email,
                        $admin->name,
                        "⚠️ Missed Clock-In: {$user->name} at {$locationName}",
                        $html
                    );

                    $this->info("    Email sent to: {$admin->email}");
                } catch (\Throwable $e) {
                    Log::error("CheckMissedClockIns: email failed for admin [{$admin->email}] — " . $e->getMessage());
                    $this->warn("    Email failed for {$admin->email}: " . $e->getMessage());
                }
            }
        }
    }
}
