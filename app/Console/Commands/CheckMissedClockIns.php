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
    protected $description = 'Check active shifts where the guard has not clocked in within 1 hour of start time, and notify admins';

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

        // Fetch today's + yesterday's shifts that have a site assigned
        $shifts = Shift::with(['schedule.user', 'site'])
            ->whereNotNull('site_id')
            ->whereIn('date', [$today, $yesterday])
            ->get();

        foreach ($shifts as $shift) {
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

            // ── Create DB notification ─────────────────────────────────────
            // No dedup — notify on every cron run while window is open and guard hasn't clocked in
            $minutesLate = (int) $startDt->diffInMinutes($now);
            $siteName = $shift->site?->name ?? 'N/A';
            $shiftName = $shift->shift_name ?: 'Regular Shift';
            $startFormatted = $startDt->format('g:i A');
            $dateFormatted = $startDt->format('d M Y');

            AdminNotification::create([
                'type' => 'missed_clock_in',
                'title' => "Missed Clock-In: {$user->name}",
                'message' => "{$user->name} has not clocked in for their shift at {$siteName}. Shift started at {$startFormatted} ({$minutesLate} min ago).",
                'shift_id' => $shift->id,
                'user_id' => $user->id,
            ]);

            $this->info("  [{$tenant->name}] Notification created — Guard: {$user->name}, Shift: {$shiftName}, Site: {$siteName}");

            // ── Push notification → SuperAdmin only ────────────────────────
            $superAdmins = User::where('role', 'SuperAdmin')
                ->whereNotNull('fcm_token')
                ->get();

            foreach ($superAdmins as $superAdmin) {
                try {
                    Notification::send($superAdmin, new MissedClockInNotification(
                        $user->name,
                        $siteName,
                        $startFormatted,
                        $minutesLate
                    ));
                    $this->info("    Push sent to SuperAdmin: {$superAdmin->name}");
                } catch (\Throwable $e) {
                    Log::error("CheckMissedClockIns: push failed for [{$superAdmin->email}] — " . $e->getMessage());
                    $this->warn("    Push failed for {$superAdmin->name}: " . $e->getMessage());
                }
            }

            // ── Send email to all admin users of this tenant ───────────────
            $admins = User::whereIn('role', ['SuperAdmin', 'Admin'])
                ->whereNotNull('email')
                ->get();

            $dashboardUrl = url('/dashboard');

            foreach ($admins as $admin) {
                try {
                    $html = view('emails.missed-clock-in', [
                        'guardName' => $user->name,
                        'siteName' => $siteName,
                        'shiftName' => $shiftName,
                        'shiftDate' => $dateFormatted,
                        'startTime' => $startFormatted,
                        'minutesLate' => $minutesLate,
                        'dashboardUrl' => $dashboardUrl,
                    ])->render();

                    Mail::html($html, function ($message) use ($admin, $user, $siteName) {
                        $message->to($admin->email, $admin->name)
                            ->subject("⚠️ Missed Clock-In: {$user->name} at {$siteName}")
                            ->from(
                                config('mail.from.address', 'info@eliteguardinc.com'),
                                config('mail.from.name', 'Elite Guard')
                            );
                    });

                    $this->info("    Email sent to: {$admin->email}");
                } catch (\Throwable $e) {
                    Log::error("CheckMissedClockIns: email failed for admin [{$admin->email}] — " . $e->getMessage());
                    $this->warn("    Email failed for {$admin->email}: " . $e->getMessage());
                }
            }
        }
    }
}
