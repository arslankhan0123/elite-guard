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

        $schedules = Schedule::with(['shifts.site.company', 'shifts.attendances' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
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

                        // Attendance Logic
                        $attendance = $shift->attendances->first(); // Since we filtered by user_id in with()
                        $shift->checked_in = $attendance ? true : false;
                        $shift->checked_out = ($attendance && $attendance->clock_out_at) ? true : false;
                        
                        if (!$shift->checked_in) {
                            $shift->Next = "Check In";
                        } elseif (!$shift->checked_out) {
                            $shift->Next = "Check Out";
                        } else {
                            $shift->Next = "Completed";
                        }

                        $shift->attendance = $attendance;
                        unset($shift->attendances); // Remove the collection and keep the single object
                    }
                    
                    $totalHours = floor($totalMinutes / 60);
                    $remainingMinutes = $totalMinutes % 60;
                    $formattedDuration = $totalHours . "h" . ($remainingMinutes > 0 ? " " . $remainingMinutes . "m" : "");

                    $days[] = [
                        'date' => $dateStr,
                        'day' => $dayName,
                        'total_duration' => $formattedDuration,
                        'total_minutes' => $totalMinutes,
                        'total_shifts' => $dayShifts->count(),
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
