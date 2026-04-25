<?php

namespace App\Repositories;

use App\Models\OpenShift;
use App\Models\OpenShiftClaim;
use App\Models\PaySlip;
use Illuminate\Support\Facades\Auth;

class OpenShiftRepository
{
    public function getOpenShifts()
    {
        $user = Auth::user();

        $openShifts = OpenShift::with('site.company')
            ->where('status', 'open')
            ->whereDoesntHave('claims', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->whereIn('status', ['approved', 'rejected']);
            })
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

    public function claimShift($id)
    {
        $user = Auth::user();
        $openShift = OpenShift::find($id);

        if (!$openShift) {
            return [
                'status' => false,
                'message' => 'Shift not found.',
                'code' => 404
            ];
        }

        if ($openShift->status !== 'open') {
            return [
                'status' => false,
                'message' => 'This shift is no longer available.',
                'code' => 422
            ];
        }

        if ($openShift->is_full) {
            return [
                'status' => false,
                'message' => 'All slots for this shift are filled.',
                'code' => 422
            ];
        }

        $existing = OpenShiftClaim::where('open_shift_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return [
                'status' => false,
                'message' => 'You have already claimed this shift. Current status: ' . $existing->status,
                'data' => [
                    'id' => $existing->id,
                    'status' => $existing->status,
                ],
                'code' => 422
            ];
        }

        $claim = OpenShiftClaim::create([
            'open_shift_id' => $id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        return [
            'status' => true,
            'message' => 'Shift claimed successfully',
            'claim' => [
                'id' => $claim->id,
                'status' => $claim->status,
            ]
        ];
    }

    public function getMyClaims()
    {
        $user = Auth::user();
        $claims = OpenShiftClaim::with('openShift.site')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($claim) {
                return [
                    'id' => $claim->id,
                    'status' => $claim->status,
                    'admin_note' => $claim->admin_note,
                    'claimed_at' => $claim->created_at->toDateTimeString(),
                    'shift' => [
                        'id' => $claim->openShift->id,
                        'site' => $claim->openShift->site->name,
                        'date' => $claim->openShift->date,
                        'shift_name' => $claim->openShift->shift_name,
                        'start_time' => $claim->openShift->start_time,
                        'end_time' => $claim->openShift->end_time,
                    ],
                ];
            });

        return [
            'status' => true,
            'message' => 'Claims retrieved successfully',
            'claims' => $claims
        ];
    }
}
