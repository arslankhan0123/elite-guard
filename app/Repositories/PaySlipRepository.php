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

    public function getPaySlips($request)
    {
        $paySlips = PaySlip::with('user')
            ->when($request->filled('user_id'), function ($query) use ($request) {
                return $query->where('user_id', $request->user_id);
            })
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
