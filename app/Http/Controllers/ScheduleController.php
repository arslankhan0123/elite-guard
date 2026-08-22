<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Shift;
use App\Models\User;
use App\Models\Site;
use App\Models\Employee;
use App\Models\SiteTour;
use App\Models\SiteTourItem;
use App\Models\WeeklyRunSheet;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyScheduleMail;
use App\Notifications\ScheduleUpdatedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ScheduleController extends Controller
{
    /**
     * Display a listing of assignments for the selected week.
     */
    public function index(Request $request)
    {
        // Get the requested date or default to now
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();

        // Find the start of the week (Monday)
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // Fetch schedules for this week
        $schedules = Schedule::with(['user', 'shifts.site.company', 'shifts.weeklyRunSheet'])
            ->where('week_start_date', $weekStart->format('Y-m-d'))
            ->get();

        // Fetch employees, sites, and weekly runsheets for the creation form
        $employees = User::whereHas('employee')->orderBy('name')->get();
        $sites = Site::orderBy('name')->get();
        $weeklyRunSheets = WeeklyRunSheet::with(['entries.site'])->withCount('entries')->orderByDesc('week_start_date')->get();

        return view('admin.schedules.index', compact(
            'schedules',
            'employees',
            'sites',
            'weeklyRunSheets',
            'weekStart',
            'weekEnd'
        ));
    }

    /**
     * Store a new assignment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'site_ids' => 'required|array',
            'site_ids.*' => 'exists:sites,id',
            'week_start_date' => 'required|date',
        ]);

        $weekStart = Carbon::parse($request->week_start_date)->startOfWeek(Carbon::MONDAY)->format('Y-m-d');

        $schedule = Schedule::firstOrCreate([
            'user_id' => $request->user_id,
            'week_start_date' => $weekStart,
        ]);

        // Update notes if provided
        if ($request->notes) {
            $schedule->update(['notes' => $request->notes]);
        }

        foreach ($request->site_ids as $site_id) {
            // Check if this site is already assigned as a shift for this week
            $exists = $schedule->shifts()->where('site_id', $site_id)->exists();

            if (!$exists) {
                $schedule->shifts()->create([
                    'site_id' => $site_id,
                    'type' => 'site',
                    'date' => $weekStart, // Default to Monday if no specific date is given in simple store
                    'shift_name' => 'Regular Shift',
                    'start_time' => '08:00:00',
                    'end_time' => '16:00:00',
                ]);
            }
        }

        // Send Notification Email
        $user = User::findOrFail($request->user_id);
        $schedule->load('shifts.site.company');

        if ($schedule->shifts->count() > 0) {
            Mail::to($user->email)->send(new WeeklyScheduleMail($user, $weekStart, $schedule));
        }

        return back()->with('success', 'Assignments created and employee notified.');
    }

    /**
     * Update/Sync assignments for a specific user and week.
     */
    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'week_start_date' => 'required|date',
            'shifts' => 'nullable|array',
            'shifts.*.id' => 'nullable|exists:shifts,id',
            'shifts.*.site_id' => 'nullable|exists:sites,id',
            'shifts.*.type' => 'nullable|string|in:site,runsheet',
            'shifts.*.weekly_run_sheet_id' => 'nullable|exists:weekly_run_sheets,id',
            'shifts.*.shift_name' => 'nullable|string',
            'shifts.*.start_time' => 'required',
            'shifts.*.end_time' => 'required',
            'shifts.*.date' => 'nullable|date',
            'shifts.*.dates' => 'nullable|array',
        ]);

        $weekStart = Carbon::parse($request->week_start_date)->startOfWeek(Carbon::MONDAY)->format('Y-m-d');

        DB::beginTransaction();
        try {
            // Find or create the schedule for this user and week
            $schedule = Schedule::firstOrCreate([
                'user_id' => $request->user_id,
                'week_start_date' => $weekStart,
            ]);

            // Update notes, email status, and notification status
            $schedule->update([
                'notes' => $request->notes,
                'is_email_sent' => $request->has('send_email') ? true : false,
                'is_notification_sent' => $request->has('send_notification') ? true : false
            ]);

            // Get all IDs provided in the request
            $providedIds = collect($request->shifts)->pluck('id')->filter()->toArray();

            // Clear existing shifts that were NOT provided in the request
            $schedule->shifts()->whereNotIn('id', $providedIds)->delete();

            if ($request->has('shifts')) {
                foreach ($request->shifts as $shiftData) {
                    $shiftType = $shiftData['type'] ?? 'site';
                    $siteId = ($shiftType === 'site') ? ($shiftData['site_id'] ?? null) : null;
                    $weeklyRunSheetId = ($shiftType === 'runsheet' && !empty($shiftData['weekly_run_sheet_id'])) ? $shiftData['weekly_run_sheet_id'] : null;

                    if ($shiftType === 'site' && !$siteId) {
                        $siteId = Site::first()?->id;
                    }

                    $baseData = [
                        'site_id' => $siteId,
                        'type' => $shiftType,
                        'weekly_run_sheet_id' => $weeklyRunSheetId,
                        'shift_name' => $shiftData['shift_name'] ?? 'Regular Shift',
                        'start_time' => $shiftData['start_time'],
                        'end_time' => $shiftData['end_time'],
                    ];

                    if (!empty($shiftData['id'])) {
                        // Update existing
                        $schedule->shifts()->where('id', $shiftData['id'])->update($baseData);
                    } elseif (isset($shiftData['date'])) {
                        // Create new day-based assignment
                        $schedule->shifts()->create(array_merge($baseData, ['date' => $shiftData['date']]));
                    } elseif (isset($shiftData['dates']) && is_array($shiftData['dates'])) {
                        // Create new pattern-based assignments
                        foreach ($shiftData['dates'] as $date) {
                            $schedule->shifts()->create(array_merge($baseData, ['date' => $date]));
                        }
                    }
                }
            }

            $schedule->load('shifts.site.nfcTags');
            $scheduleUser = User::findOrFail($request->user_id);
            $assignedSiteIds = $scheduleUser->sites()
                ->pluck('sites.id')
                ->mapWithKeys(fn ($siteId) => [(int) $siteId => true]);

            foreach ($schedule->shifts as $shift) {
                if (!$assignedSiteIds->has((int) $shift->site_id)) {
                    $scheduleUser->sites()->attach($shift->site_id, [
                        'assigned_at' => now(),
                    ]);
                    $assignedSiteIds->put((int) $shift->site_id, true);
                }

                if ($shift->weekly_run_sheet_id) {
                    $scheduleUser->weeklyRunSheets()->syncWithoutDetaching([
                        $shift->weekly_run_sheet_id => ['assigned_at' => now()]
                    ]);
                }
            }

            DB::commit();

            // Send Notification Email if requested
            if ($request->has('send_email') && $schedule->shifts->count() > 0) {
                $user = User::findOrFail($request->user_id);
                $schedule->load('shifts.site.company');
                try {
                    Mail::to($user->email)->send(new WeeklyScheduleMail($user, $weekStart, $schedule));
                } catch (\Exception $e) {
                    Log::error("Failed to send schedule email: " . $e->getMessage());
                }
            }

            // Send FCM Notification if requested
            $user = User::findOrFail($request->user_id);
            if ($request->has('send_notification') && $user->fcm_token && $schedule->shifts->count() > 0) {
                try {
                    $weekEnd = Carbon::parse($weekStart)->endOfWeek(Carbon::SUNDAY)->format('d M, Y');
                    $formattedWeekDates = Carbon::parse($weekStart)->format('d M') . " - " . $weekEnd;
                    
                    Notification::send($user, new ScheduleUpdatedNotification($formattedWeekDates, !$schedule->wasRecentlyCreated));
                } catch (\Exception $e) {
                    Log::error("Failed to send schedule FCM: " . $e->getMessage());
                }
            }

            return back()->with('success', 'Shifts updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update shifts: ' . $e->getMessage());
        }
    }

    /**
     * Remove an assignment.
     */
    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return back()->with('success', 'Assignment removed successfully.');
    }

    /**
     * Remove all assignments for a user in a specific week.
     */
    public function destroyByUserWeek(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'week_start_date' => 'required|date',
        ]);

        Schedule::where('user_id', $request->user_id)
            ->where('week_start_date', $request->week_start_date)
            ->delete();

        return back()->with('success', 'All assignments for the employee this week have been removed.');
    }

    /**
     * Get assignments for a specific user and week via AJAX.
     */
    public function getAjaxSchedule(Request $request, $user_id)
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');

        $schedule = Schedule::with('shifts')->where('user_id', $user_id)
            ->where('week_start_date', $weekStart)
            ->first();

        return response()->json([
            'schedule' => $schedule
        ]);
    }

    /**
     * Create or update the automatic site tour belonging to one employee shift.
     */
    private function syncSiteTourForShift(Shift $shift, int $userId): void
    {
        $site = $shift->site;

        if (!$site || !$shift->date) {
            return;
        }

        $interval = (int) $site->interval;
        $tour = SiteTour::updateOrCreate(
            ['shift_id' => $shift->id],
            [
                'site_id' => $shift->site_id,
                'user_id' => $userId,
                'name' => $shift->shift_name ?: 'Regular Shift',
                'description' => 'This is the site tour for shift: ' . ($shift->shift_name ?: 'Regular Shift') . '.',
                'scheduled_days' => [Carbon::parse($shift->date)->format('l')],
                'is_continuous' => false,
                'schedule_type' => 'Repeat',
                'specific_times' => [],
                'max_duration' => [],
                'tag_type' => 'nfc',
                'tags' => $site->nfcTags->pluck('id')->values()->all(),
                'assigned_guards' => [$userId],
                'interval' => $interval,
                'open_time' => (int) $site->open_time,
                'grace_time' => (int) $site->grace_time,
            ]
        );

        $intendedItems = [];

        if ($interval > 0 && $shift->start_time && $shift->end_time) {
            $startTime = Carbon::parse($shift->start_time);
            $endTime = Carbon::parse($shift->end_time);

            if ($endTime->lt($startTime)) {
                $endTime->addDay();
            }

            $itemNumber = 1;

            while ($startTime->copy()->addMinutes(($itemNumber - 1) * $interval)->lt($endTime)) {
                $itemStart = $startTime->copy()->addMinutes(($itemNumber - 1) * $interval);

                if ($itemNumber > 1) {
                    $itemStart->addMinute();
                }

                $itemEnd = $startTime->copy()->addMinutes($itemNumber * $interval);

                if ($itemEnd->gt($endTime)) {
                    $itemEnd = $endTime->copy();
                }

                if ($itemStart->lt($itemEnd)) {
                    $key = $itemStart->format('H:i:s') . '|' . $itemEnd->format('H:i:s');
                    $intendedItems[$key] = [
                        'site_tour_id' => $tour->id,
                        'user_id' => $userId,
                        'site_id' => $shift->site_id,
                        'type' => null,
                        'status' => false,
                        'date' => Carbon::parse($shift->date)->format('Y-m-d'),
                        'start_time' => $itemStart->format('H:i:s'),
                        'end_time' => $itemEnd->format('H:i:s'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $itemNumber++;
            }
        }

        foreach ($tour->items()->get() as $existingItem) {
            $key = Carbon::parse($existingItem->start_time)->format('H:i:s')
                . '|' . Carbon::parse($existingItem->end_time)->format('H:i:s');

            if (
                isset($intendedItems[$key])
                && $existingItem->date?->format('Y-m-d') === Carbon::parse($shift->date)->format('Y-m-d')
                && (int) $existingItem->site_id === (int) $shift->site_id
            ) {
                unset($intendedItems[$key]);
            } else {
                $existingItem->delete();
            }
        }

        if ($intendedItems) {
            SiteTourItem::insert(array_values($intendedItems));
        }
    }
}
