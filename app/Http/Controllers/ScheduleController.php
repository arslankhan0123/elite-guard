<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Site;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        return back()->with('success', 'Assignments created successfully.');
    }

    /**
     * Update/Sync assignments for a specific user and week.
     */
    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'site_ids' => 'nullable|array',
            'site_ids.*' => 'exists:sites,id',
            'week_start_date' => 'required|date',
        ]);

        $weekStart = Carbon::parse($request->week_start_date)->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $newSiteIds = $request->site_ids ?? [];

        // Delete assignments that are not in the new list
        Schedule::where('user_id', $request->user_id)
            ->where('week_start_date', $weekStart)
            ->whereNotIn('site_id', $newSiteIds)
            ->delete();

        // Add assignments that are in the new list but don't exist yet
        foreach ($newSiteIds as $site_id) {
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

        return back()->with('success', 'Assignments updated successfully.');
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
}
