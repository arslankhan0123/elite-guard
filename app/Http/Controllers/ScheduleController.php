<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Site;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyScheduleMail;
use Illuminate\Support\Facades\Log;

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
        $schedules = Schedule::with(['user', 'site'])
            ->where('week_start_date', $weekStart->format('Y-m-d'))
            ->get();

        // Fetch employees and sites for the creation form
        $employees = User::whereHas('employee')->orderBy('name')->get();
        $sites = Site::orderBy('name')->get();

        return view('admin.schedules.index', compact(
            'schedules',
            'employees',
            'sites',
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

        foreach ($request->site_ids as $site_id) {
            // Check if already assigned
            $exists = Schedule::where([
                'user_id' => $request->user_id,
                'site_id' => $site_id,
                'week_start_date' => $weekStart
            ])->exists();

            if (!$exists) {
                Schedule::create([
                    'user_id' => $request->user_id,
                    'site_id' => $site_id,
                    'week_start_date' => $weekStart,
                    'notes' => $request->notes,
                ]);
            }
        }

        // Send Notification Email
        $user = User::findOrFail($request->user_id);
        $allSchedules = Schedule::with('site.company')
            ->where('user_id', $user->id)
            ->where('week_start_date', $weekStart)
            ->get();

        if ($allSchedules->count() > 0) {
            Mail::to($user->email)->send(new WeeklyScheduleMail($user, $weekStart, $allSchedules));
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
            'shifts.*.site_id' => 'required|exists:sites,id',
            'shifts.*.shift_name' => 'nullable|string',
            'shifts.*.start_time' => 'required',
            'shifts.*.end_time' => 'required',
            'shifts.*.dates' => 'required|array',
        ]);

        $weekStart = Carbon::parse($request->week_start_date)->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        
        DB::beginTransaction();
        try {
            // Delete all existing schedules for this user and week to sync
            Schedule::where('user_id', $request->user_id)
                ->where('week_start_date', $weekStart)
                ->delete();

            if ($request->has('shifts')) {
                foreach ($request->shifts as $shiftData) {
                    foreach ($shiftData['dates'] as $date) {
                        Schedule::create([
                            'user_id' => $request->user_id,
                            'site_id' => $shiftData['site_id'],
                            'date' => $date,
                            'shift_name' => $shiftData['shift_name'] ?? 'Regular Shift',
                            'start_time' => $shiftData['start_time'],
                            'end_time' => $shiftData['end_time'],
                            'week_start_date' => $weekStart,
                            'notes' => $request->notes,
                        ]);
                    }
                }
            }

            DB::commit();

            // Send Notification Email
            $user = User::findOrFail($request->user_id);
            $allSchedules = Schedule::with('site.company')
                ->where('user_id', $user->id)
                ->where('week_start_date', $weekStart)
                ->get();

            if ($allSchedules->count() > 0) {
                try {
                    Mail::to($user->email)->send(new WeeklyScheduleMail($user, $weekStart, $allSchedules));
                } catch (\Exception $e) {
                    // Log error but don't fail the request
                    Log::error("Failed to send schedule email: " . $e->getMessage());
                }
            }

            return back()->with('success', 'Shifts updated and employee notified.');
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
}
