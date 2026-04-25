<?php

namespace App\Repositories;

use App\Models\Shift;
use App\Models\ShiftAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceRepository
{
    /**
     * Handle Clock-In for a shift.
     */
    public function clockIn($shiftId, $lat, $long)
    {
        $user = Auth::user();
        $shift = Shift::with('site')->find($shiftId);

        if (!$shift) {
            return [
                'status' => false,
                'message' => 'Shift not found.'
            ];
        }

        // Verify if user is assigned to this shift
        // Assuming shift -> schedule -> user relationship
        if ($shift->schedule->user_id !== $user->id) {
            return [
                'status' => false,
                'message' => 'You are not authorized to clock in for this shift.'
            ];
        }

        // Geofencing Check
        $site = $shift->site;
        if (!$site->latitude || !$site->longitude) {
            return [
                'status' => false,
                'message' => 'Site coordinates are not set. Contact admin.'
            ];
        }

        $distance = $this->calculateDistance($lat, $long, $site->latitude, $site->longitude);

        if ($distance > 100) { // 100 meters
            return [
                'status' => false,
                'message' => 'You are too far from the site. Distance: ' . round($distance, 2) . 'm',
                'distance' => $distance
            ];
        }

        // Check if already clocked in
        $existing = ShiftAttendance::where('shift_id', $shiftId)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return [
                'status' => false,
                'message' => 'You are already clocked in for this shift.'
            ];
        }

        $attendance = ShiftAttendance::create([
            'user_id' => $user->id,
            'shift_id' => $shiftId,
            'clock_in_at' => Carbon::now(),
            'clock_in_latitude' => $lat,
            'clock_in_longitude' => $long,
            'status' => 'active',
        ]);

        return [
            'status' => true,
            'message' => 'Clock-in successful!',
            'data' => $attendance
        ];
    }

    /**
     * Handle Clock-Out for a shift.
     */
    public function clockOut($shiftId, $lat, $long)
    {
        $user = Auth::user();
        
        $attendance = ShiftAttendance::where('shift_id', $shiftId)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$attendance) {
            return [
                'status' => false,
                'message' => 'No active clock-in found for this shift.'
            ];
        }

        // Geofencing Check for Clock-Out (Optional but good practice)
        $shift = Shift::with('site')->find($shiftId);
        $site = $shift->site;
        
        $distance = $this->calculateDistance($lat, $long, $site->latitude, $site->longitude);

        if ($distance > 100) {
            return [
                'status' => false,
                'message' => 'You are too far from the site to clock out. Distance: ' . round($distance, 2) . 'm',
                'distance' => $distance
            ];
        }

        $attendance->update([
            'clock_out_at' => Carbon::now(),
            'clock_out_latitude' => $lat,
            'clock_out_longitude' => $long,
            'status' => 'completed',
        ]);

        return [
            'status' => true,
            'message' => 'Clock-out successful!',
            'data' => $attendance
        ];
    }

    /**
     * Calculate distance using Haversine formula.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
