<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NfcTag;
use App\Repositories\NfcTagsRepository;
use App\Repositories\TimeClockRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimeClockApiController extends Controller
{
    use ApiResponser;
    protected $timeClock;

    // Inject the repository via constructor
    public function __construct(TimeClockRepository $timeClock)
    {
        $this->timeClock = $timeClock;
    }

    /**
     * @OA\Get(
     *     path="/api/time-clock",
     *     summary="Get user time clock entries",
     *     tags={"TimeClock"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Time clock entries retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Time clock entries retrieved successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Time clock entries retrieved successfully"),
     *                 @OA\Property(property="time_clocks", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $timeClocks = $this->timeClock->getUserTimeClocks();
        return $this->successResponse($timeClocks, 'Time clock entries retrieved successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/time-clock/store",
     *     summary="Store time clock entry (Check-in/Check-out)",
     *     tags={"TimeClock"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"uid"},
     *             @OA\Property(property="uid", type="string", example="NFC_TAG_UID_123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Time clock entry stored successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Time clock entry stored successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Time clock entry stored successfully"),
     *                 @OA\Property(property="time_clock", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required|exists:nfc_tags,uid',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Use the repository to store the time clock entry
        $timeClock = $this->timeClock->storeTimeClock($request);
        return $this->successResponse($timeClock, 'Time clock entry stored successfully.');
    }
}
