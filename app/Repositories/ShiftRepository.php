<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ShiftRepository
{
    public function getUserShiftData()
    {
        $userId = Auth::id();
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $shifts = Shift::with(['schedule.user', 'site.company', 'attendances' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])
        ->whereHas('schedule', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->whereBetween('date', [$startOfWeek, $endOfWeek])
        ->orderBy('date', 'asc')
        ->orderBy('start_time', 'asc')
        ->get();

        foreach ($shifts as $shift) {
            // Attendance Logic
            $attendance = $shift->attendances->first();
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
            unset($shift->attendances);
        }

        return [
            'status' => true,
            'message' => 'Shifts data fetched successfully.',
            'total' => $shifts->count(),
            'shifts' => $shifts
        ];
    }

    public function getShiftData($id)
    {
        $userId = Auth::id();
        $shift = Shift::with(['schedule.user', 'site.company', 'attendances' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])->find($id);

        if (!$shift) {
            return [
                'status' => false,
                'message' => 'Shift not found.',
                'shift' => null
            ];
        }

        // Attendance Logic
        $attendance = $shift->attendances->first();
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
        unset($shift->attendances);

        return [
            'status' => true,
            'message' => 'Shift data fetched successfully.',
            'shift' => $shift
        ];
    }
}
