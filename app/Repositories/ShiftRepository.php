<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ShiftRepository
{
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
