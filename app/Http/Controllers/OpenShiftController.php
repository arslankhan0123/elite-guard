<?php

namespace App\Http\Controllers;

use App\Models\OpenShift;
use App\Models\OpenShiftClaim;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Site;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OpenShiftController extends Controller
{
    /**
     * List all open shifts posted by admin.
     */
    public function index()
    {
        $openShifts = OpenShift::with('site.company', 'claims')
            ->orderByDesc('date')
            ->get();

        $pendingClaimsCount = OpenShiftClaim::where('status', 'pending')->count();

        return view('admin.open-shifts.index', compact('openShifts', 'pendingClaimsCount'));
    }

    /**
     * Show the form to create a new open shift.
     */
    public function create()
    {
        $sites = Site::orderBy('name')->get();
        return view('admin.open-shifts.create', compact('sites'));
    }

    /**
     * Store a newly posted open shift.
     */
    public function store(Request $request)
    {
        $request->validate([
            'site_id'    => 'required|exists:sites,id',
            'date'       => 'required|date',
            'shift_name' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time'   => 'required',
            'slots'      => 'required|integer|min:1|max:50',
            'notes'      => 'nullable|string',
            'status'     => 'required|in:open,closed',
        ]);

        OpenShift::create($request->only([
            'site_id', 'date', 'shift_name', 'start_time', 'end_time', 'slots', 'notes', 'status'
        ]));

        return redirect()->route('open-shifts.index')
            ->with('success', 'Open shift posted successfully. Employees can now claim it.');
    }

    /**
     * Show the edit form for an open shift.
     */
    public function edit($id)
    {
        $openShift = OpenShift::findOrFail($id);
        $sites     = Site::orderBy('name')->get();
        return view('admin.open-shifts.edit', compact('openShift', 'sites'));
    }

    /**
     * Update an open shift.
     */
    public function update(Request $request, $id)
    {
        $openShift = OpenShift::findOrFail($id);

        $request->validate([
            'site_id'    => 'required|exists:sites,id',
            'date'       => 'required|date',
            'shift_name' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time'   => 'required',
            'slots'      => 'required|integer|min:1|max:50',
            'notes'      => 'nullable|string',
            'status'     => 'required|in:open,closed',
        ]);

        $openShift->update($request->only([
            'site_id', 'date', 'shift_name', 'start_time', 'end_time', 'slots', 'notes', 'status'
        ]));

        return redirect()->route('open-shifts.index')
            ->with('success', 'Open shift updated successfully.');
    }

    /**
     * Delete an open shift (and cascade delete all claims).
     */
    public function delete($id)
    {
        $openShift = OpenShift::findOrFail($id);
        $openShift->delete();

        return redirect()->route('open-shifts.index')
            ->with('success', 'Open shift deleted.');
    }

    /**
     * List all employee claim requests.
     */
    public function claims(Request $request)
    {
        $statusFilter = $request->input('status', 'all');
        $shiftId     = $request->shift_id;

        $claimsQuery = OpenShiftClaim::with('user.employee', 'openShift.site')->where('open_shift_id', $shiftId)
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderByDesc('created_at');

        if ($statusFilter !== 'all') {
            $claimsQuery->where('status', $statusFilter);
        }

        $claims             = $claimsQuery->get();
        $pendingClaimsCount = OpenShiftClaim::where('status', 'pending')->count();

        return view('admin.open-shifts.claims', compact('claims', 'pendingClaimsCount', 'statusFilter', 'shiftId'));
    }

    /**
     * Approve a claim — automatically insert the shift into the employee's schedule.
     */
    public function approveClaim(Request $request, $id)
    {
        $claim     = OpenShiftClaim::with('openShift')->findOrFail($id);
        $openShift = $claim->openShift;

        if ($claim->status !== 'pending') {
            return back()->with('error', 'This claim has already been processed.');
        }

        if ($openShift->is_full) {
            return back()->with('error', 'All slots for this open shift are already filled.');
        }

        DB::beginTransaction();
        try {
            // Determine the Monday of the open shift's week
            $weekStart = Carbon::parse($openShift->date)
                ->startOfWeek(Carbon::MONDAY)
                ->format('Y-m-d');

            // Find or create the employee's schedule for that week
            $schedule = Schedule::firstOrCreate([
                'user_id'         => $claim->user_id,
                'week_start_date' => $weekStart,
            ]);

            // Insert the shift into the schedule
            $schedule->shifts()->create([
                'site_id'    => $openShift->site_id,
                'date'       => $openShift->date,
                'shift_name' => $openShift->shift_name,
                'start_time' => $openShift->start_time,
                'end_time'   => $openShift->end_time,
            ]);

            // Mark claim as approved
            $claim->update([
                'status'     => 'approved',
                'admin_note' => $request->input('admin_note'),
            ]);

            // Close the open shift if all slots are now filled
            $approvedCount = $openShift->claims()->where('status', 'approved')->count();
            if ($approvedCount >= $openShift->slots) {
                $openShift->update(['status' => 'closed']);
            }

            DB::commit();

            // Send FCM notification if user has a token
            try {
                $user = User::find($claim->user_id);
                if ($user && $user->fcm_token) {
                    \Illuminate\Support\Facades\Notification::send(
                        $user,
                        new \App\Notifications\OpenShiftClaimApprovedNotification($openShift)
                    );
                }
            } catch (\Exception $e) {
                Log::error('FCM open shift notification failed: ' . $e->getMessage());
            }

            return back()->with('success', 'Claim approved! The shift has been added to the employee\'s schedule.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approve claim failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve claim: ' . $e->getMessage());
        }
    }

    /**
     * Reject a claim.
     */
    public function rejectClaim(Request $request, $id)
    {
        $claim = OpenShiftClaim::with('openShift')->findOrFail($id);

        if ($claim->status !== 'pending') {
            return back()->with('error', 'This claim has already been processed.');
        }

        $claim->update([
            'status'     => 'rejected',
            'admin_note' => $request->input('admin_note'),
        ]);

        // Send FCM notification
        try {
            $user = User::find($claim->user_id);
            if ($user && $user->fcm_token) {
                \Illuminate\Support\Facades\Notification::send(
                    $user,
                    new \App\Notifications\OpenShiftClaimRejectedNotification($claim->openShift, $request->input('admin_note'))
                );
            }
        } catch (\Exception $e) {
            Log::error('FCM open shift rejection notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Claim rejected.');
    }
}
