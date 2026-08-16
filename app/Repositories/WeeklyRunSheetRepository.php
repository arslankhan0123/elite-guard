<?php

namespace App\Repositories;

use App\Models\WeeklyRunSheet;
use App\Models\User;

class WeeklyRunSheetRepository
{
    public function getAllWeeklyRunSheets(): array
    {
        $runSheets = WeeklyRunSheet::with([
            'entries.site.company',
            'entries.site.nfcTags',
        ])
            ->orderByDesc('week_start_date')
            ->get();

        return [
            'status' => true,
            'message' => 'Weekly runsheets retrieved successfully',
            'run_sheets' => $runSheets,
        ];
    }

    public function getUserAssignedWeeklyRunSheets(User $user): array
    {
        $runSheets = $user->weeklyRunSheets()
            ->with([
                'entries.site.company',
                'entries.site.nfcTags',
            ])
            ->orderByDesc('week_start_date')
            ->get();

        return [
            'status' => true,
            'message' => 'Assigned weekly runsheets retrieved successfully',
            'run_sheets' => $runSheets,
        ];
    }
}
