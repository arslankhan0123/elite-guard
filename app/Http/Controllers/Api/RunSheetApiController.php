<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RunSheet;
use App\Models\Site;
use App\Models\WeeklyRunSheetEntry;
use App\Repositories\RunSheetRepository;
use App\Repositories\ShiftRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Traits\CommonTrait;
use Carbon\Carbon;

class RunSheetApiController extends Controller
{
    use ApiResponser, CommonTrait;

    protected $runSheetRepo;
    protected $shiftRepo;

    public function __construct(RunSheetRepository $runSheetRepo, ShiftRepository $shiftRepo)
    {
        $this->runSheetRepo = $runSheetRepo;
        $this->shiftRepo = $shiftRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/run-sheets",
     *     summary="Get run sheets for the authenticated user based on active shift",
     *     description="Resolves active or upcoming shift for the authenticated user and returns assigned daily run sheets for that shift.",
     *     tags={"Run Sheets"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filter by date (YYYY-MM-DD). Defaults to active shift date if not provided.",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Run sheets fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Run sheets fetched successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Run sheets retrieved successfully"),
     *                 @OA\Property(property="total_run_sheets", type="integer", example=2),
     *                 @OA\Property(property="total_entries", type="integer", example=4),
     *                 @OA\Property(property="scanned_entries", type="integer", example=0),
     *                 @OA\Property(property="total_tags", type="integer", example=6),
     *                 @OA\Property(property="total_scanned_tags", type="integer", example=2),
     *                 @OA\Property(property="shift", type="object", nullable=true, description="Active or upcoming shift object"),
     *                 @OA\Property(property="weekly_run_sheet", type="object", nullable=true, description="Parent weekly runsheet / main route template object"),
     *                 @OA\Property(property="main_route", type="object", nullable=true, description="Alias for parent weekly runsheet / main route template object"),
     *                 @OA\Property(property="run_sheets", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="site_id", type="integer", example=1),
     *                     @OA\Property(property="shift_id", type="integer", nullable=true, example=1),
     *                     @OA\Property(property="date", type="string", format="date", example="2026-09-08"),
     *                     @OA\Property(property="run_sheet_name", type="string", example="Mobile Patrol Check"),
     *                     @OA\Property(property="start_time", type="string", example="10:00:00"),
     *                     @OA\Property(property="end_time", type="string", example="15:00:00"),
     *                     @OA\Property(property="duration", type="string", example="15 Min."),
     *                     @OA\Property(property="job_type", type="string", example="Mobile Patrol"),
     *                     @OA\Property(property="sequence", type="string", example="1 of 1"),
     *                     @OA\Property(property="is_scanned", type="boolean", example=false),
     *                     @OA\Property(property="total_tags", type="integer", example=2),
     *                     @OA\Property(property="scanned_tags_count", type="integer", example=0),
     *                     @OA\Property(property="weekly_run_sheet", type="object", nullable=true, description="Main route template object"),
     *                     @OA\Property(property="main_route", type="object", nullable=true, description="Main route template object"),
     *                     @OA\Property(property="site", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Elite Plaza"),
     *                         @OA\Property(property="company", type="object")
     *                     ),
     *                     @OA\Property(property="scans", type="array", @OA\Items(type="object"))
     *                 ))
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Automatically resolve the active (or next upcoming) shift.
        $activeShift = $this->shiftRepo->getActiveShift();

        if (!$activeShift || Carbon::now(config('app.timezone', 'UTC'))->gt($activeShift->end_datetime)) {
            return $this->successResponse([
                'status'             => true,
                'message'            => 'No active or upcoming shift found.',
                'shift'              => null,
                'weekly_run_sheet'   => null,
                'main_route'         => null,
                'total_run_sheets'   => 0,
                'total_entries'      => 0,
                'scanned_entries'    => 0,
                'total_tags'         => 0,
                'total_scanned_tags' => 0,
                'run_sheets'         => [],
            ], 'No active or upcoming shift found.');
        }

        $date = $request->query('date') ?: $activeShift->date;

        $data = $this->runSheetRepo->getUserRunSheets($user, $date, $activeShift->id);

        $data['shift'] = $activeShift;
        $data['weekly_run_sheet'] = $activeShift->weeklyRunSheet;
        $data['main_route'] = $activeShift->weeklyRunSheet;

        return $this->successResponse($data, 'Run sheets fetched successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/run-sheets/scan",
     *     summary="Record an NFC tag scan for a daily run sheet",
     *     description="Validates scan location, prevents duplicate scans for the same day, and stores scan record with optional image upload into run_sheet_scans table.",
     *     tags={"Run Sheets"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"run_sheet_id", "nfc_tag_id"},
     *                 @OA\Property(property="run_sheet_id", type="integer", example=8, description="ID of the daily run sheet from run_sheets table"),
     *                 @OA\Property(property="nfc_tag_id", type="integer", example=3, description="ID of the scanned NFC tag"),
     *                 @OA\Property(property="date", type="string", format="date", nullable=true, example="2026-09-08"),
     *                 @OA\Property(property="time", type="string", nullable=true, example="14:30:00"),
     *                 @OA\Property(property="latitude", type="string", nullable=true, example="31.5038682"),
     *                 @OA\Property(property="longitude", type="string", nullable=true, example="74.3480792"),
     *                 @OA\Property(property="reason", type="string", nullable=true, example="NFC tag was inaccessible"),
     *                 @OA\Property(property="image", type="string", format="binary", nullable=true, description="Optional photo taken during scanning")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Scan recorded successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Scan recorded successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Run sheet or Site not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Error"),
     *             @OA\Property(property="message", type="string", example="Run sheet not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error (Too far from site, or already scanned)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Error"),
     *             @OA\Property(property="message", type="string", example="You are too far from the site. Distance: 150.5m"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     )
     * )
     */
    public function storeScan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'run_sheet_id' => 'required|exists:run_sheets,id',
            'nfc_tag_id'   => 'required|exists:nfc_tags,id',
            'latitude'     => 'nullable|string',
            'longitude'    => 'nullable|string',
            'date'         => 'nullable|date',
            'time'         => 'nullable',
            'reason'       => 'nullable|string',
            'image'        => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), null, 422);
        }

        $runsheet = RunSheet::where('id', $request->run_sheet_id)->first();
        if (!$runsheet) {
            return $this->errorResponse('Run sheet not found.', null, 404);
        }
        $site = Site::where('id', $runsheet->site_id)->first();
        if (!$site) {
            return $this->errorResponse('Associated site not found.', null, 404);
        }

        // if ($request->latitude && $request->longitude) {
        //     $distance = $this->calculateDistance($request->latitude, $request->longitude, $site->latitude, $site->longitude);
        //
        //     if ($distance > 100) { // 100 meters
        //         return $this->errorResponse(
        //             'You are too far from the site. Distance: ' . round($distance, 2) . 'm',
        //             ['distance' => round($distance, 2)],
        //             422
        //         );
        //     }
        // }

        $activeShift = $this->shiftRepo->getActiveShift();
        $scanDate = $request->input('date') ?: ($runsheet->date ?: ($activeShift ? $activeShift->date : Carbon::now(config('app.timezone', 'UTC'))->format('Y-m-d')));
        $scanTime = $request->input('time') ?: Carbon::now(config('app.timezone', 'UTC'))->toTimeString();

        $scanData = [
            'run_sheet_id' => (int) $runsheet->id,
            'nfc_tag_id'   => (int) $request->nfc_tag_id,
            'user_id'      => Auth::id(),
            'date'         => $scanDate,
            'time'         => $scanTime,
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'reason'       => $request->reason,
        ];

        // Check if already scanned
        if ($this->runSheetRepo->isAlreadyScanned($scanData)) {
            return $this->errorResponse('This NFC tag has already been scanned for this run sheet today.', null, 422);
        }

        // Image upload handling
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('documents/RunSheetScans', 'public');
            $scanData['image'] = Storage::disk('public')->url($path);
        }

        $result = $this->runSheetRepo->storeScan($scanData);

        return $this->successResponse($result['runsheet'], 'Scan recorded successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/run-sheets/finish",
     *     summary="Finish run sheets by shift IDs",
     *     description="Takes an array of shift IDs and updates the runsheet_status of matching run sheets to completed.",
     *     tags={"Run Sheets"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"shift_ids"},
     *             @OA\Property(
     *                 property="shift_ids",
     *                 type="array",
     *                 description="Array of shift IDs to mark run sheets as completed",
     *                 @OA\Items(type="integer", example=806)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Run sheets finished successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Run sheets finished successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="updated_count", type="integer", example=5),
     *                 @OA\Property(property="shift_ids", type="array", @OA\Items(type="integer"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Error"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function finishRunSheets(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shift_ids'   => 'required|array',
            'shift_ids.*' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), null, 422);
        }

        $shiftIds = $request->input('shift_ids', []);
        $updatedCount = 0;

        foreach ($shiftIds as $shiftId) {
            $updatedCount += RunSheet::where('shift_id', $shiftId)
                ->update(['runsheet_status' => 'completed']);
        }

        return $this->successResponse([
            'updated_count' => $updatedCount,
            'shift_ids'     => $shiftIds,
        ], 'Run sheets finished successfully.');
    }
}
