<?php

namespace App\Repositories;

use App\Models\Schedule;
use Carbon\Carbon;

class ScheduleRepository
{
    /**
     * Get schedules for a user by week
     */
    public function getSchedulesByWeek($userId, $dateString = null)
    {
        $date = $dateString ? Carbon::parse($dateString) : Carbon::now();
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
        $weekStartStr = $weekStart->format('Y-m-d');

        $schedules = Schedule::with(['shifts.site.company'])
            ->where('user_id', $userId)
            ->where('week_start_date', $weekStartStr)
            ->get()
            ->map(function ($schedule) use ($weekStart) {
                $groupedShifts = [];
                for ($i = 0; $i < 7; $i++) {
                    $currentDate = $weekStart->copy()->addDays($i)->format('Y-m-d');
                    $groupedShifts[$currentDate] = $schedule->shifts->where('date', $currentDate)->values();
                }
                $schedule->grouped_shifts = $groupedShifts;
                return $schedule;
            });

        return [
            'status' => true,
            'message' => 'Schedules retrieved successfully',
            'week_start_date' => $weekStartStr,
            'schedules' => $schedules
        ];
    }
}
