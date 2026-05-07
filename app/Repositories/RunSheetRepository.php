<?php

namespace App\Repositories;

use App\Models\RunSheet;
use Carbon\Carbon;

class RunSheetRepository
{
    /**
     * Get run sheets assigned to a specific user.
     * Optionally filter by date.
     */
    public function getUserRunSheets($user, $date = null)
    {
        $query = RunSheet::with('site.nfcTags', 'site.company')
            ->where('user_id', $user->id);

        if ($date) {
            $query->where('date', $date);
        } else {
            // Default to today's date if not provided
            $query->where('date', Carbon::today()->format('Y-m-d'));
        }

        $query->orderBy('start_time', 'asc');

        $runSheets = $query->get();

        return [
            'status' => true,
            'message' => 'Run sheets retrieved successfully',
            'total_run_sheets' => $runSheets->count(),
            'run_sheets' => $runSheets
        ];
    }

    /**
     * Get run sheets for the authenticated user for today.
     */
    public function getTodayRunSheets($user)
    {
        return $this->getUserRunSheets($user, Carbon::today()->format('Y-m-d'));
    }
}
