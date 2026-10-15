<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Repositories\AttendanceRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Traits\CommonTrait;

class AttendanceApiController extends Controller
{
    use ApiResponser, CommonTrait;

    protected $attendanceRepo;

    public function __construct(AttendanceRepository $attendanceRepo)
    {
        $this->attendanceRepo = $attendanceRepo;
    }

    /**
     * @OA\Post(
     *     path="/api/attendance/clock-in",
     *     summary="Clock-in for a shift",
     *     description="User clocks in for a shift with geofencing verification and optional selfie image.",
     *     tags={"Attendance"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"shift_id", "latitude", "longitude"},
     *                 @OA\Property(property="shift_id", type="integer", example=1),
     *                 @OA\Property(property="latitude", type="number", format="float", example=51.5074),
     *                 @OA\Property(property="longitude", type="number", format="float", example=-0.1278),
     *                 @OA\Property(property="image", type="string", format="binary", description="Clock-in selfie (jpg/jpeg/png, max 5MB)")
     *             )
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
            'image'     => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()
            ], 401);
        }

        // Upload clock-in image if provided
        $clockInImageUrl = null;
        if ($request->hasFile('image')) {
            $userId    = Auth::id() ?? 'guest';
            $file      = $request->file('image');
            $extension = $file->extension() === 'jpeg' ? 'jpg' : $file->extension();
            $fileName  = $userId . '_clockin_' . time() . '_' . Str::random(10) . '.' . $extension;
            $directory = storage_path('app/public/attendance/clock_in');
            File::ensureDirectoryExists($directory);
            $file->move($directory, $fileName);
            $clockInImageUrl = url('storage/attendance/clock_in/' . $fileName);
        }

        DB::beginTransaction();

        try {
            $result = $this->attendanceRepo->clockIn(
                $request->shift_id,
                $request->latitude,
                $request->longitude,
                $clockInImageUrl
            );

            if (!$result['status']) {
                DB::rollBack();

                return $this->errorResponse($result['message'], $result, 422);
            }

            $shift = Shift::with(['site.nfcTags', 'weeklyRunSheet.entries'])->findOrFail($request->shift_id);
            if ($shift->weekly_run_sheet_id || $shift->type === 'runsheet') {
                $this->syncRunSheetForShift($shift, (int) Auth::id());
            }
            if ($shift->site_id) {
                $this->syncSiteTourForShift($shift, (int) Auth::id());
            }

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $this->successResponse($result['data'], $result['message']);
    }

    /**
     * @OA\Post(
     *     path="/api/attendance/clock-out",
     *     summary="Clock-out for a shift",
     *     description="User clocks out from an active shift with geofencing verification and optional selfie image.",
     *     tags={"Attendance"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"shift_id", "latitude", "longitude"},
     *                 @OA\Property(property="shift_id", type="integer", example=1),
     *                 @OA\Property(property="latitude", type="number", format="float", example=51.5074),
     *                 @OA\Property(property="longitude", type="number", format="float", example=-0.1278),
     *                 @OA\Property(property="image", type="string", format="binary", description="Clock-out selfie (jpg/jpeg/png, max 5MB)")
     *             )
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
        $validator = Validator::make($request->all(), [
            'shift_id'  => 'required|exists:shifts,id',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()
            ], 401);
        }

        // Upload clock-out image if provided
        $clockOutImageUrl = null;
        if ($request->hasFile('image')) {
            $userId    = Auth::id() ?? 'guest';
            $file      = $request->file('image');
            $extension = $file->extension() === 'jpeg' ? 'jpg' : $file->extension();
            $fileName  = $userId . '_clockout_' . time() . '_' . Str::random(10) . '.' . $extension;
            $directory = storage_path('app/public/attendance/clock_out');
            File::ensureDirectoryExists($directory);
            $file->move($directory, $fileName);
            $clockOutImageUrl = url('storage/attendance/clock_out/' . $fileName);
        }

        $result = $this->attendanceRepo->clockOut(
            $request->shift_id,
            $request->latitude,
            $request->longitude,
            $clockOutImageUrl
        );

        if (!$result['status']) {
            return $this->errorResponse($result['message'], $result, 422);
        }

        return $this->successResponse($result['data'], $result['message']);
    }
}
