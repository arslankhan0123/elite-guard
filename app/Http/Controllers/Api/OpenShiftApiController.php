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
        $result = $this->openShiftRepo->claimShift($id);

        if (!$result['status']) {
            $code = $result['code'] ?? 422;
            $data = $result['data'] ?? null;
            return $this->errorResponse($result['message'], $data, $code);
        }

        return $this->successResponse($result, 'Shift claimed successfully! Your request is pending admin approval.');
    }

    /**
     * Get the authenticated employee's own claim history.
     */
    public function myClaims(Request $request)
    {
        $result = $this->openShiftRepo->getMyClaims();
        return $this->successResponse($result, 'Claims fetched.');
    }
}
