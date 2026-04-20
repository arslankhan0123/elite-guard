<?php

namespace App\Repositories;

use App\Models\PaySlip;
use Illuminate\Support\Facades\Auth;

class PaySlipRepository
{
    /**
     * Get all PaySlips with optional month and year filters.
     */
    public function getUserPaySlips($request)
    {
        $user = Auth::user();
        
        $paySlips = PaySlip::where('user_id', $user->id)
            ->when($request->filled('month'), function ($query) use ($request) {
                return $query->where('month', $request->month);
            })
            ->when($request->filled('year'), function ($query) use ($request) {
                return $query->where('year', $request->year);
            })
            ->latest()
            ->get();

        return [
            'status' => true,
            'message' => 'Pay slips retrieved successfully',
            'paySlips' => $paySlips
        ];
    }
}
