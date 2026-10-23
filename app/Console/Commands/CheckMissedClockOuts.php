<?php

namespace App\Console\Commands;

use App\Models\AdminNotification;
use App\Models\Master\Tenant;
use App\Models\Shift;
use App\Models\ShiftAttendance;
use App\Models\User;
use App\Notifications\MissedClockOutNotification;
use App\Services\TenantService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class CheckMissedClockOuts extends Command
{
    protected $signature = 'shifts:check-missed-clockouts';
    protected $description = 'Notify guards and admins when a shift end time is approaching (10 min before) and the guard has not clocked out';

    public function handle(): int
    {
        $this->info('Checking missed clock-outs across all tenants...');

        $tenants = Tenant::on('master')->where('is_active', true)->get();

        foreach ($tenants as $tenant) {
            try {
                TenantService::setTenant($tenant);
                $this->processTenant($tenant);
            } catch (\Throwable $e) {
                Log::error("CheckMissedClockOuts: tenant [{$tenant->id}] error — " . $e->getMessage(), [
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

        // Look at today's and yesterday's shifts to catch overnight shifts
        $today = $now->toDateString();
        $yesterday = $now->copy()->subDay()->toDateString();

        // BOTH site & runsheet types (runsheet shifts have site_id = NULL, so no site_id filter)
        $shifts = Shift::with(['schedule.user', 'site', 'weeklyRunSheet'])
            ->whereIn('date', [$today, $yesterday])
            ->get();

        foreach ($shifts as $shift) {
            $user = $shift->schedule?->user;
            if (!$user) {
                continue;
            }

            // Build shift end datetime — handle overnight shifts
            $startDt = Carbon::parse($shift->date . ' ' . $shift->start_time, $tz);
            $endDt = Carbon::parse($shift->date . ' ' . $shift->end_time, $tz);
            if ($endDt->lte($startDt)) {
                $endDt->addDay(); // overnight: end is next day
            }

            // ── Notification window ────────────────────────────────────────
            // Send from (endDt - 10 min) up to (endDt + 30 min)
            $windowStart = $endDt->copy()->subMinutes(10);
            $windowEnd = $endDt->copy()->addMinutes(30);

            if ($now->lt($windowStart) || $now->gt($windowEnd)) {
                continue; // outside notification window
            }

            // ── Check if guard has already clocked out ─────────────────────
            $hasClockedOut = ShiftAttendance::where('shift_id', $shift->id)
                ->where('user_id', $user->id)
                ->whereNotNull('clock_out_at')
                ->exists();

            if ($hasClockedOut) {
                continue; // already clocked out — all good
            }

            // ── Check if guard even clocked in ────────────────────────────
            $attendance = ShiftAttendance::where('shift_id', $shift->id)
                ->where('user_id', $user->id)
                ->whereNotNull('clock_in_at')
                ->first();

            if (!$attendance) {
                continue; // never clocked in — handled by missed-clockin job
            }

            // ── Build message vars ─────────────────────────────────────────
            // Location name: site OR runsheet depending on shift type
            if ($shift->type === 'runsheet') {
                $siteName = $shift->weeklyRunSheet?->name ?? 'Run Sheet';
            } else {
                $siteName = $shift->site?->name ?? 'N/A';
            }
            $shiftName = $shift->shift_name ?: 'Regular Shift';
            $endFormatted = $endDt->format('g:i A');
            $dateFormatted = $endDt->format('d M Y');
            $minutesPast = max(0, (int) $endDt->diffInMinutes($now, false));
            $minutesBefore = max(0, (int) $now->diffInMinutes($endDt, false));

            if ($now->lt($endDt)) {
                $timeContext = "Shift ends in {$minutesBefore} min (at {$endFormatted})";
            } else {
                $timeContext = "Shift ended {$minutesPast} min ago (at {$endFormatted})";
            }

            // ── Save DB notification ───────────────────────────────────────
            AdminNotification::create([
                'type' => 'missed_clock_out',
                'title' => "Missed Clock-Out: {$user->name}",
                'message' => "{$user->name} has not clocked out at {$siteName}. {$timeContext}.",
                'shift_id' => $shift->id,
                'user_id' => $user->id,
            ]);

            $this->info("  [{$tenant->name}] DB notification — Guard: {$user->name}, Site: {$siteName}, {$timeContext}");

            // ── Push notification → guard ──────────────────────────────────
            if ($user->fcm_token) {
                try {
                    Notification::send($user, new MissedClockOutNotification(
                        $user->name,
                        $siteName,
                        $endFormatted,
                        $minutesPast,
                        $minutesBefore,
                        $now->lt($endDt),
                        true // forGuard => "You haven't clocked out..."
                    ));
                    $this->info("    Push sent to guard: {$user->name}");
                } catch (\Throwable $e) {
                    Log::error("CheckMissedClockOuts: push to guard [{$user->email}] failed — " . $e->getMessage());
                    $this->warn("    Push to guard {$user->name} failed: " . $e->getMessage());
                }
            }

            // ── Push notification → SuperAdmins ───────────────────────────
            $allSuperAdmins = User::where('role', 'SuperAdmin')->get();
            $superAdmins    = $allSuperAdmins->filter(fn ($a) => !empty($a->fcm_token));

            // Diagnostic: how many super-admins exist vs. how many have a usable token
            $diag = "SuperAdmins total={$allSuperAdmins->count()}, with_token={$superAdmins->count()}";
            $this->info("    {$diag}");
            Log::info("CheckMissedClockOuts [{$tenant->name}] guard={$user->name}: {$diag}");
            if ($superAdmins->isEmpty()) {
                $this->warn("    No SuperAdmin with fcm_token in this tenant.");
            }

            foreach ($superAdmins as $superAdmin) {
                try {
                    Notification::send($superAdmin, new MissedClockOutNotification(
                        $user->name,
                        $siteName,
                        $endFormatted,
                        $minutesPast,
                        $minutesBefore,
                        $now->lt($endDt),
                        false // admin-worded => "Guard X has not clocked out..."
                    ));
                    $this->info("    Push sent to SuperAdmin: {$superAdmin->name} (id={$superAdmin->id})");
                    Log::info("CheckMissedClockOuts [{$tenant->name}]: push OK to SuperAdmin {$superAdmin->name} (id={$superAdmin->id})");
                } catch (\Throwable $e) {
                    Log::error("CheckMissedClockOuts [{$tenant->name}]: push FAILED to SuperAdmin {$superAdmin->name} (id={$superAdmin->id}, token=" . substr((string) $superAdmin->fcm_token, 0, 12) . "...) — " . $e->getMessage());
                    $this->warn("    Push to SuperAdmin {$superAdmin->name} failed: " . $e->getMessage());
                }
            }

            // ── Email → all admins ─────────────────────────────────────────
            $admins = User::whereIn('role', ['SuperAdmin'])
                ->whereNotNull('email')
                ->get();

            $dashboardUrl = url('/dashboard');

            foreach ($admins as $admin) {
                try {
                    $html = view('emails.missed-clock-out', [
                        'guardName' => $user->name,
                        'siteName' => $siteName,
                        'shiftName' => $shiftName,
                        'shiftDate' => $dateFormatted,
                        'endTime' => $endFormatted,
                        'timeContext' => $timeContext,
                        'dashboardUrl' => $dashboardUrl,
                    ])->render();

                    Mail::html($html, function ($message) use ($admin, $user, $siteName) {
                        $message->to($admin->email, $admin->name)
                            ->subject("⚠️ Missed Clock-Out: {$user->name} at {$siteName}")
                            ->from(
                                config('mail.from.address', 'info@eliteguardinc.com'),
                                config('mail.from.name', 'Elite Guard')
                            );
                    });

                    $this->info("    Email sent to: {$admin->email}");
                } catch (\Throwable $e) {
                    Log::error("CheckMissedClockOuts: email to admin [{$admin->email}] failed — " . $e->getMessage());
                    $this->warn("    Email to {$admin->email} failed: " . $e->getMessage());
                }
            }
        }
    }
}
