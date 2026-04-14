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
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');

        $schedules = Schedule::with(['site.company'])
            ->where('user_id', $userId)
            ->where('week_start_date', $weekStart)
            ->get();

        return [
            'status' => true,
            'message' => 'Schedules retrieved successfully',
            'week_start_date' => $weekStart,
            'schedules' => $schedules
        ];
    }
}
