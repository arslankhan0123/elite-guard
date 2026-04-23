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
     *     path="/api/shift/{id}",
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
}
