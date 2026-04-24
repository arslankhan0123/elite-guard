<?php

namespace App\Repositories;

use App\Models\OpenShift;
use App\Models\PaySlip;
use Illuminate\Support\Facades\Auth;

class OpenShiftRepository
{
    public function getOpenShifts()
    {
        $user = Auth::user();

        $openShifts = OpenShift::with('site.company')
            ->where('status', 'open')
            ->orderBy('date')
            ->get()
            ->map(function ($shift) use ($user) {
                $userClaim = $shift->claims()->where('user_id', $user->id)->first();

                return [
                    'id' => $shift->id,
                    'site' => [
                        'id' => $shift->site->id,
                        'name' => $shift->site->name,
                        'address' => $shift->site->address,
                        'city' => $shift->site->city,
                    ],
                    'date' => $shift->date,
                    'shift_name' => $shift->shift_name,
                    'start_time' => $shift->start_time,
                    'end_time' => $shift->end_time,
                    'slots' => $shift->slots,
                    'approved_count' => $shift->approved_count,
                    'slots_remaining' => max(0, $shift->slots - $shift->approved_count),
                    'notes' => $shift->notes,
                    'is_full' => $shift->is_full,
                    'my_claim' => $userClaim ? [
                        'id' => $userClaim->id,
                        'status' => $userClaim->status,
                    ] : null,
                ];
            });

        return [
            'status' => true,
            'message' => 'Open shifts retrieved successfully',
            'openShifts' => $openShifts
        ];
    }
}
