<?php

namespace App\Repositories;

use App\Models\NfcTag;
use App\Models\Site;
use App\Models\TimeClock;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TimeClockRepository
{
    public function getUserTimeClocks()
    {
        $user = Auth::user();
        $timeClocks = TimeClock::where('user_id', $user->id)->with('nfcTag')->get();
        $data = [
            'status' => true,
            'message' => 'Time clock entries retrieved successfully',
            'time_clocks' => $timeClocks
        ];
        return $data;
    }

    // Store time clock entry
    public function storeTimeClock($request)
    {
        $user = Auth::user();
        $nfcTag = NfcTag::where('uid', $request->uid)->first();

        if (!$nfcTag) {
            return null; // Or throw an exception
        }

        // Check if the user is currently checked in
        $lastEntry = TimeClock::where('user_id', $user->id)->latest()->first();

        if ($lastEntry && !$lastEntry->check_out_time) {
            // User is checking out
            $lastEntry->check_out_time = now();
            $lastEntry->status = 'checked_out';
            $lastEntry->save();
        } else {
            // User is checking in
            $lastEntry = TimeClock::create([
                'user_id' => $user->id,
                'nfc_tag_id' => $nfcTag->id,
                'check_in_time' => now(),
                'status' => 'checked_in',
            ]);
        }
        $data = [
            'status' => true,
            'message' => 'Time clock entry stored successfully',
            'time_clock' => $lastEntry
        ];
        return $data;
    }
}