<?php

namespace App\Repositories;

use App\Models\WeeklyRunSheet;
use App\Models\User;
use Carbon\Carbon;

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
        $today = Carbon::now(config('app.timezone', 'UTC'));
        $dayOfWeek = $today->dayOfWeekIso;

        $runSheets = $user->weeklyRunSheets()
            ->whereHas('entries', fn ($query) => $query->where('day_of_week', $dayOfWeek))
            ->with(['entries' => fn ($query) => $query
                ->where('day_of_week', $dayOfWeek)
                ->with(['site.company', 'site.nfcTags'])
                ->orderBy('sequence')
                ->orderBy('start_time')])
            ->orderBy('weekly_run_sheets.name')
            ->get();

        return [
            'status' => true,
            'message' => 'Today assigned weekly runsheets retrieved successfully',
            'date' => $today->toDateString(),
            'day' => $today->format('l'),
            'total_run_sheets' => $runSheets->count(),
            'run_sheets' => $runSheets,
        ];
    }
}
