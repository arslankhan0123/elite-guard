<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\AttendanceRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceApiController extends Controller
{
    use ApiResponser;

    protected $attendanceRepo;

    public function __construct(AttendanceRepository $attendanceRepo)
    {
        $this->attendanceRepo = $attendanceRepo;
    }

    /**
     * @OA\Post(
     *     path="/api/attendance/clock-in",
     *     summary="Clock-in for a shift",
     *     description="User clocks in for a shift with geofencing verification.",
     *     tags={"Attendance"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"shift_id", "latitude", "longitude"},
     *             @OA\Property(property="shift_id", type="integer", example=1),
     *             @OA\Property(property="latitude", type="number", format="float", example=51.5074),
     *             @OA\Property(property="longitude", type="number", format="float", example=-0.1278)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Clock-in status.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function clockIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shift_id'  => 'required|exists:shifts,id',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 401);
        }

        $result = $this->attendanceRepo->clockIn(
            $request->shift_id,
            $request->latitude,
            $request->longitude
        );

        if (!$result['status']) {
            return $this->errorResponse($result['message'], $result, 422);
        }

        return $this->successResponse($result['data'], $result['message']);
    }

    /**
     * @OA\Post(
     *     path="/api/attendance/clock-out",
     *     summary="Clock-out for a shift",
     *     description="User clocks out from an active shift with geofencing verification.",
     *     tags={"Attendance"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"shift_id", "latitude", "longitude"},
     *             @OA\Property(property="shift_id", type="integer", example=1),
     *             @OA\Property(property="latitude", type="number", format="float", example=51.5074),
     *             @OA\Property(property="longitude", type="number", format="float", example=-0.1278)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Clock-out status.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function clockOut(Request $request)
    {
        $request->validate([
            'shift_id'  => 'required|exists:shifts,id',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $result = $this->attendanceRepo->clockOut(
            $request->shift_id,
            $request->latitude,
            $request->longitude
        );

        if (!$result['status']) {
            return $this->errorResponse($result['message'], $result, 422);
        }

        return $this->successResponse($result['data'], $result['message']);
    }
}
