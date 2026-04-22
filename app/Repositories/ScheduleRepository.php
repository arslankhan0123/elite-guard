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
                $days = [];
                for ($i = 0; $i < 7; $i++) {
                    $currentDate = $weekStart->copy()->addDays($i);
                    $dateStr = $currentDate->format('Y-m-d');
                    $dayName = $currentDate->format('l');
                    $dayShifts = $schedule->shifts->where('date', $dateStr)->values();
                    
                    $totalMinutes = 0;
                    foreach ($dayShifts as $shift) {
                        $start = Carbon::parse($shift->start_time);
                        $end = Carbon::parse($shift->end_time);
                        
                        // If end time is before start time, assume it ends the next day
                        if ($end->lessThan($start)) {
                            $end->addDay();
                        }
                        
                        $totalMinutes += $start->diffInMinutes($end);
                    }
                    
                    $totalHours = floor($totalMinutes / 60);
                    $remainingMinutes = $totalMinutes % 60;
                    $formattedDuration = $totalHours . "h" . ($remainingMinutes > 0 ? " " . $remainingMinutes . "m" : "");

                    $days[] = [
                        'date' => $dateStr,
                        'day' => $dayName,
                        'total_duration' => $formattedDuration,
                        'total_minutes' => $totalMinutes,
                        'shifts' => $dayShifts
                    ];
                }
                $schedule->days = $days;
                unset($schedule->shifts); // Optional: remove flat list to keep response clean
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
