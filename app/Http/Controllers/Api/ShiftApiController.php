<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\PolicyRepository;
use App\Repositories\ShiftRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Shift",
 *     description="API Endpoints for Shift Management"
 * )
 */
class ShiftApiController extends Controller
{
    use ApiResponser;

    protected $shiftRepo;

    public function __construct(ShiftRepository $shiftRepo)
    {
        $this->shiftRepo = $shiftRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/shifts/{id}",
     *     summary="Get shift details",
     *     tags={"Shift"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Shift ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Shift data fetched successfully."),
     *             @OA\Property(property="shift", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shift not found"
     *     )
     * )
     */
    public function index($id)
    {
        $shift = $this->shiftRepo->getShiftData($id);

        return $this->successResponse($shift, 'Shift data fetched successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/shifts",
     *     summary="Get all shifts for the logged-in user",
     *     description="Returns a list of shifts for the given date range assigned to the authenticated user. If no dates are provided, it returns shifts for the current week (starting Monday).",
     *     tags={"Shift"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=false,
     *         description="Start date for the shifts filter (Y-m-d)",
     *         @OA\Schema(type="string", example="2024-04-22")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=false,
     *         description="End date for the shifts filter (Y-m-d)",
     *         @OA\Schema(type="string", example="2024-04-28")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Shift data fetched successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Shifts data fetched successfully."),
     *                 @OA\Property(property="total", type="integer", example=1),
     *                 @OA\Property(property="open_shifts_count", type="integer", example=2),
     *                 @OA\Property(property="startOfWeek", type="string", example="2024-04-22"),
     *                 @OA\Property(property="endOfWeek", type="string", example="2024-04-28"),
     *                 @OA\Property(property="totalDuration", type="string", example="40h 0m"),
     *                 @OA\Property(property="shifts", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function userShifts(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        $shifts = $this->shiftRepo->getUserShiftData($startDate, $endDate);

        return $this->successResponse($shifts, 'Shift data fetched successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/shifts/active",
     *     summary="Get the current active shift for the logged-in user",
     *     description="Returns the first active shift for today whose end time has not passed yet.",
     *     tags={"Shift"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Active shift data fetched successfully."),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function activeShift()
    {
        $shift = $this->shiftRepo->getActiveShift();

        if (!$shift) {
            return $this->successResponse(null, 'No active shift found.');
        }

        return $this->successResponse($shift, 'Active shift data fetched successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/shifts/{id}/reject",
     *     summary="Reject an assigned shift",
     *     description="Allows a user to reject a shift assigned to them. The shift will be removed from their schedule and moved to open shifts.",
     *     tags={"Shift"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Shift ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Shift rejected successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shift not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error (e.g. shift already started)"
     *     )
     * )
     */
    public function rejectShift($id)
    {
        $result = $this->shiftRepo->rejectShift($id);

        if (!$result['status']) {
            return $this->errorResponse($result['message'], null, $result['code'] ?? 422);
        }

        return $this->successResponse(null, $result['message']);
    }
}
