<?php

namespace App\Http\Controllers;

use App\Models\RunSheet;
use App\Models\User;
use App\Models\Site;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RunSheetController extends Controller
{
    /**
     * Update/Sync run sheets for a specific user and week.
     */
    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'week_start_date' => 'required|date',
            'run_sheets' => 'nullable|array',
            'run_sheets.*.id' => 'nullable|exists:run_sheets,id',
            'run_sheets.*.site_id' => 'required|exists:sites,id',
            'run_sheets.*.date' => 'required|date',
            'run_sheets.*.run_sheet_name' => 'nullable|string|max:255',
            'run_sheets.*.start_time' => 'required',
            'run_sheets.*.end_time' => 'required',
            'run_sheets.*.duration' => 'nullable|string|max:255',
            'run_sheets.*.job_type' => 'nullable|string|max:255',
            'run_sheets.*.sequence' => 'nullable|string|max:255',
        ]);

        $weekStart = Carbon::parse($request->week_start_date)->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $weekEnd = Carbon::parse($weekStart)->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');

        DB::beginTransaction();
        try {
            // Get all IDs provided in the request
            $providedIds = collect($request->run_sheets)->pluck('id')->filter()->toArray();

            // Delete run sheets for this user in this week that were NOT provided in the request
            RunSheet::where('user_id', $request->user_id)
                ->whereBetween('date', [$weekStart, $weekEnd])
                ->whereNotIn('id', $providedIds)
                ->delete();

            if ($request->has('run_sheets')) {
                foreach ($request->run_sheets as $rsData) {
                    $data = [
                        'user_id' => $request->user_id,
                        'site_id' => $rsData['site_id'],
                        'date' => $rsData['date'],
                        'run_sheet_name' => $rsData['run_sheet_name'],
                        'start_time' => $rsData['start_time'],
                        'end_time' => $rsData['end_time'],
                        'duration' => $rsData['duration'],
                        'job_type' => $rsData['job_type'],
                        'sequence' => $rsData['sequence'],
                    ];

                    if (!empty($rsData['id'])) {
                        // Update existing
                        RunSheet::where('id', $rsData['id'])->update($data);
                    } else {
                        // Create new
                        RunSheet::create($data);
                    }
                }
            }

            DB::commit();
            return back()->with('success', 'Run sheets updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update run sheets: ' . $e->getMessage());
        }
    }
}
