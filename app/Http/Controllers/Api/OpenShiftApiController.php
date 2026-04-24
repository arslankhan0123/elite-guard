<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OpenShift;
use App\Models\OpenShiftClaim;
use App\Repositories\OpenShiftRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponser;

class OpenShiftApiController extends Controller
{
    use ApiResponser;
    /**
     * List all open shifts available for employees to claim.
     */

    protected $openShiftRepo;

    public function __construct(OpenShiftRepository $openShiftRepo)
    {
        $this->openShiftRepo = $openShiftRepo;
    }

    public function index(Request $request)
    {
        $result = $this->openShiftRepo->getOpenShifts();
        return $this->successResponse($result, 'Open shifts retrieved successfully');
        // return $this->successResponse([
        //     'status' => true,
        //     'message' => 'Open shifts retrieved successfully',
        //     'open_shifts' => $result
        // ], 'Open shifts fetched.');
    }

    /**
     * Employee claims an open shift.
     */
    public function claim(Request $request, $id)
    {
        $user = Auth::user();
        $openShift = OpenShift::findOrFail($id);

        if ($openShift->status !== 'open') {
            return $this->errorResponse('This shift is no longer available.', null, 422);
        }

        if ($openShift->is_full) {
            return $this->errorResponse('All slots for this shift are filled.', null, 422);
        }

        // Check if already claimed
        $existing = OpenShiftClaim::where('open_shift_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return $this->errorResponse('You have already claimed this shift. Current status: ' . $existing->status, [
                'id' => $existing->id,
                'status' => $existing->status,
            ], 422);
        }

        $claim = OpenShiftClaim::create([
            'open_shift_id' => $id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        return $this->successResponse([
            'status' => true,
            'message' => 'Shift claimed successfully',
            'claim' => [
                'id' => $claim->id,
                'status' => $claim->status,
            ]
        ], 'Shift claimed successfully! Your request is pending admin approval.');
    }

    /**
     * Get the authenticated employee's own claim history.
     */
    public function myClaims(Request $request)
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

        return $this->successResponse([
            'status' => true,
            'message' => 'Claims retrieved successfully',
            'claims' => $claims
        ], 'Claims fetched.');
    }
}
