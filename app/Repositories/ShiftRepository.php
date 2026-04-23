<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Models\Shift;
use Carbon\Carbon;

class ShiftRepository
{
    public function getShiftData($id)
    {
        $shift = Shift::with(['schedule.user', 'site.company'])->find($id);
        if (!$shift) {
            return [
                'status' => false,
                'message' => 'Shift not found.',
                'shift' => null
            ];
        }

        return [
            'status' => true,
            'message' => 'Shift data fetched successfully.',
            'shift' => $shift
        ];
    }
}
