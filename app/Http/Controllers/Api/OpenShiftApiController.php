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

    /**
     * @OA\Get(
     *     path="/api/open-shifts",
     *     summary="List all available open shifts",
     *     description="Fetches a list of open shifts that the authenticated employee can claim.",
     *     tags={"Open Shifts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Open shifts retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Open shifts fetched."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Open shifts retrieved successfully"),
     *                 @OA\Property(property="open_shifts", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="site", type="object",
     *                             @OA\Property(property="id", type="integer", example=2),
     *                             @OA\Property(property="name", type="string", example="Main Site"),
     *                             @OA\Property(property="address", type="string", example="123 Street"),
     *                             @OA\Property(property="city", type="string", example="London")
     *                         ),
     *                         @OA\Property(property="date", type="string", example="2026-04-24"),
     *                         @OA\Property(property="shift_name", type="string", example="Night Shift"),
     *                         @OA\Property(property="start_time", type="string", example="22:00:00"),
     *                         @OA\Property(property="end_time", type="string", example="06:00:00"),
     *                         @OA\Property(property="slots", type="integer", example=5),
     *                         @OA\Property(property="approved_count", type="integer", example=1),
     *                         @OA\Property(property="slots_remaining", type="integer", example=4),
     *                         @OA\Property(property="notes", type="string", example="Uniform required"),
     *                         @OA\Property(property="is_full", type="boolean", example=false),
     *                         @OA\Property(property="my_claim", type="object", nullable=true)
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $result = $this->openShiftRepo->getOpenShifts();
        return $this->successResponse($result, 'Open shifts fetched.');
    }

    /**
     * @OA\Post(
     *     path="/api/open-shifts/{id}/claim",
     *     summary="Claim an open shift",
     *     description="Employee sends a request to claim a specific open shift. Subject to admin approval.",
     *     tags={"Open Shifts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the open shift",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shift claimed successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Shift claimed successfully! ..."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Shift claimed successfully"),
     *                 @OA\Property(property="claim", type="object",
     *                     @OA\Property(property="id", type="integer", example=10),
     *                     @OA\Property(property="status", type="string", example="pending")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error (e.g. shift full or already claimed)."
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/open-shifts/my-claims",
     *     summary="Get personal claim history",
     *     description="Fetches a list of all claims made by the authenticated employee, including their status.",
     *     tags={"Open Shifts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Claims retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Claims fetched."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Claims retrieved successfully"),
     *                 @OA\Property(property="claims", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="status", type="string", example="approved"),
     *                         @OA\Property(property="admin_note", type="string", example="Good job"),
     *                         @OA\Property(property="claimed_at", type="string", example="2026-04-24 10:00:00"),
     *                         @OA\Property(property="shift", type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="site", type="string", example="Main Site"),
     *                             @OA\Property(property="date", type="string", example="2026-04-24"),
     *                             @OA\Property(property="shift_name", type="string", example="Night Shift"),
     *                             @OA\Property(property="start_time", type="string", example="22:00:00"),
     *                             @OA\Property(property="end_time", type="string", example="06:00:00")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function myClaims(Request $request)
    {
        $result = $this->openShiftRepo->getMyClaims();
        return $this->successResponse($result, 'Claims fetched.');
    }
}
