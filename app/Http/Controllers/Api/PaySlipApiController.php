<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\PaySlipRepository;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class PaySlipApiController extends Controller
{
    use ApiResponser;

    protected $paySlipRepo;

    public function __construct(PaySlipRepository $paySlipRepo)
    {
        $this->paySlipRepo = $paySlipRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/pay-slips/user",
     *     summary="Get pay slips for the authenticated user",
     *     tags={"Pay Slips"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="month",
     *         in="query",
     *         description="Filter by month (1-12)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="year",
     *         in="query",
     *         description="Filter by year (e.g. 2024)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pay slips fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Pay slips fetched successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Pay slips retrieved successfully"),
     *                 @OA\Property(property="paySlips", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=5),
     *                     @OA\Property(property="month", type="integer", example=4),
     *                     @OA\Property(property="year", type="integer", example=2024),
     *                     @OA\Property(property="file_path", type="string", example="http://localhost:8000/documents/pay_slips/user_5_1713600000.pdf"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function getUserPaySlips(Request $request)
    {
        $result = $this->paySlipRepo->getUserPaySlips($request);
        return $this->successResponse($result, 'Pay slips fetched successfully.');
    }
}
