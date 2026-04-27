<?php

namespace App\Repositories;

use App\Models\OpenShift;
use App\Models\Schedule;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function rejectShift($id)
    {
        $userId = Auth::id();
        $shift = Shift::with(['schedule', 'attendances'])->find($id);

        if (!$shift) {
            return [
                'status' => false,
                'message' => 'Shift not found.',
                'code' => 404
            ];
        }

        // Verify shift belongs to the user
        if ($shift->schedule->user_id !== $userId) {
            return [
                'status' => false,
                'message' => 'You are not authorized to reject this shift.',
                'code' => 403
            ];
        }

        // Check if shift has attendance records (checked in)
        if ($shift->attendances->count() > 0) {
            return [
                'status' => false,
                'message' => 'You cannot reject a shift that has already started or been checked into.',
                'code' => 422
            ];
        }

        DB::beginTransaction();
        try {
            // Create OpenShift record
            OpenShift::create([
                'site_id'    => $shift->site_id,
                'date'       => $shift->date,
                'shift_name' => $shift->shift_name,
                'start_time' => $shift->start_time,
                'end_time'   => $shift->end_time,
                'slots'      => 1,
                'status'     => 'open',
                'notes'      => 'Shift rejected by user ' . Auth::user()->name,
            ]);

            // Delete the assigned shift
            $shift->delete();

            DB::commit();

            return [
                'status' => true,
                'message' => 'Shift rejected successfully and moved to open shifts.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Failed to reject shift: ' . $e->getMessage(),
                'code' => 500
            ];
        }
    }
}
