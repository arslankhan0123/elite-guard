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

        $now = Carbon::now('Asia/Karachi');
        $shiftDate = Carbon::parse($shift->date);

        // 1. Check if the date matches
        if (!$now->isSameDay($shiftDate)) {
            return [
                'status' => false,
                'message' => 'You can only clock in on the date assigned to this shift: ' . $shift->date
            ];
        }

        // 2. Check if it's too early (allowed only from 30 mins before start_time)
        $startTime = Carbon::parse($shift->date . ' ' . $shift->start_time);
        $earliestAllowed = $startTime->copy()->subMinutes(30);

        if ($now->lt($earliestAllowed)) {
            return [
                'status' => false,
                'message' => 'You can only clock in starting from 30 minutes before the shift start time (' . $shift->start_time . ').'
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
            'clock_in_at' => Carbon::now('Asia/Karachi'),
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
            'clock_out_at' => Carbon::now('Asia/Karachi'),
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
     * Get attendance report for admin panel with filters.
     */
    public function getAttendanceReport($request)
    {
        $query = ShiftAttendance::with(['user', 'shift.site.company']);

        // Filter by User
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Site
        if ($request->has('site_id') && $request->site_id) {
            $query->whereHas('shift', function ($q) use ($request) {
                $q->where('site_id', $request->site_id);
            });
        }

        // Filter by Status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by Date Range
        if ($request->has('date_filter') && $request->date_filter) {
            $range = $this->getDateRange($request->date_filter);
            if ($range['start'] && $range['end']) {
                $query->whereBetween('clock_in_at', [$range['start'], $range['end']]);
            }
        }

        return $query->orderBy('clock_in_at', 'desc')->get();
    }

    /**
     * Helper to get date range based on filter string.
     */
    private function getDateRange($filter)
    {
        $now = Carbon::now('Asia/Karachi');
        $start = null;
        $end = null;

        switch ($filter) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'yesterday':
                $start = $now->copy()->subDay()->startOfDay();
                $end = $now->copy()->subDay()->endOfDay();
                break;
            case 'current_week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'last_week':
                $start = $now->copy()->subWeek()->startOfWeek();
                $end = $now->copy()->subWeek()->endOfWeek();
                break;
            case 'current_month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $start = $now->copy()->subMonth()->startOfMonth();
                $end = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'current_year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            case 'last_year':
                $start = $now->copy()->subYear()->startOfYear();
                $end = $now->copy()->subYear()->endOfYear();
                break;
        }

        return ['start' => $start, 'end' => $end];
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
