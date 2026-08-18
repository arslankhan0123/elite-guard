<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\WeeklyRunSheetRepository;
use App\Traits\ApiResponser;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use App\Models\WeeklyRunSheetEntry;
use App\Models\Site;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WeeklyRunSheetApiController extends Controller
{
    use ApiResponser, CommonTrait;

    public function __construct(private WeeklyRunSheetRepository $runSheetRepository)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/run-sheets/weekly",
     *     summary="Get all weekly runsheets assigned to the authenticated user",
     *     description="Returns only weekly runsheets assigned to the authenticated user, including all weekday child entries. Unassigned runsheets are omitted.",
     *     tags={"Weekly Runsheets"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Weekly runsheets fetched successfully")
     * )
     */
    public function index(Request $request)
    {
        return $this->successResponse(
            $this->runSheetRepository->getUserAssignedAllWeeklyRunSheets($request->user()),
            'Assigned weekly runsheets fetched successfully.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/run-sheets/user",
     *     summary="Get today's tours from weekly runsheets assigned to the authenticated user",
     *     description="Checks every weekly runsheet assigned to the authenticated user and returns only today's weekday child tours under each parent. A runsheet with no tours scheduled for today's weekday is omitted. The parent runsheet's stored week date does not restrict this recurring weekday schedule.",
     *     tags={"Weekly Runsheets"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Today's assigned weekly runsheets fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Today's assigned weekly runsheets fetched successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="date", type="string", format="date", example="2026-08-17"),
     *                 @OA\Property(property="day", type="string", example="Monday"),
     *                 @OA\Property(property="total_run_sheets", type="integer", example=2),
     *                 @OA\Property(property="run_sheets", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Mobile Patrol Route"),
     *                     @OA\Property(property="week_start_date", type="string", format="date", example="2026-08-17"),
     *                     @OA\Property(property="notes", type="string", nullable=true),
     *                     @OA\Property(property="entries", type="array", @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=10),
     *                         @OA\Property(property="day_of_week", type="integer", example=1),
     *                         @OA\Property(property="tour_name", type="string", example="Morning Patrol"),
     *                         @OA\Property(property="start_time", type="string", example="08:00:00"),
     *                         @OA\Property(property="end_time", type="string", example="09:00:00"),
     *                         @OA\Property(property="sequence", type="integer", example=1),
     *                         @OA\Property(property="site", type="object",
     *                             @OA\Property(property="id", type="integer", example=4),
     *                             @OA\Property(property="name", type="string", example="Elite Plaza"),
     *                             @OA\Property(property="company", type="object"),
     *                             @OA\Property(property="nfc_tags", type="array", @OA\Items(type="object"))
     *                         )
     *                     ))
     *                 ))
     *             )
     *         )
     *     )
     * )
     */
    public function userRunSheets(Request $request)
    {
        return $this->successResponse(
            $this->runSheetRepository->getUserAssignedWeeklyRunSheets($request->user()),
            "Today's assigned weekly runsheets fetched successfully."
        );
    }

    /**
     * @OA\Post(
     *     path="/api/run-sheets/weekly/scan",
     *     summary="Record an NFC tag scan for a weekly run sheet entry",
     *     description="Validates scan location and prevents duplicate scans for the same day.",
     *     tags={"Weekly Runsheets"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"weekly_run_sheet_id", "weekly_run_sheet_entry_id", "nfc_tag_id"},
     *                 @OA\Property(property="weekly_run_sheet_id", type="integer", example=1),
     *                 @OA\Property(property="weekly_run_sheet_entry_id", type="integer", example=1),
     *                 @OA\Property(property="nfc_tag_id", type="integer", example=2),
     *                 @OA\Property(property="date", type="string", format="date", example="2026-08-17"),
     *                 @OA\Property(property="time", type="string", example="14:30:00"),
     *                 @OA\Property(property="latitude", type="string", example="31.5038682"),
     *                 @OA\Property(property="longitude", type="string", example="74.3480792"),
     *                 @OA\Property(property="reason", type="string", nullable=true, example="NFC tag was inaccessible"),
     *                 @OA\Property(property="image", type="string", format="binary", description="Optional image/photo taken during scanning")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Scan recorded successfully."
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     )
     * )
     */
    public function storeScan(Request $request)
    {
        Log::info('Received scan request', $request->all());
        $validator = Validator::make($request->all(), [
            'weekly_run_sheet_id' => 'required|exists:weekly_run_sheets,id',
            'weekly_run_sheet_entry_id' => 'required|exists:weekly_run_sheet_entries,id',
            'nfc_tag_id' => 'required|exists:nfc_tags,id',
            'date' => 'nullable|date',
            'time' => 'nullable',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'reason' => 'nullable|string',
            'image' => 'nullable|image'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), null, 422);
        }

        $entry = WeeklyRunSheetEntry::find($request->weekly_run_sheet_entry_id);
        if (!$entry) {
            return $this->errorResponse('Weekly run sheet entry not found.', null, 404);
        }

        $site = Site::find($entry->site_id);
        if (!$site) {
            return $this->errorResponse('Associated site not found.', null, 404);
        }

        // Distance validation
        if ($request->latitude && $request->longitude) {
            $distance = $this->calculateDistance($request->latitude, $request->longitude, $site->latitude, $site->longitude);

            if ($distance > 100) { // 100 meters
                return $this->errorResponse(
                    'You are too far from the site. Distance: ' . round($distance, 2) . 'm',
                    ['distance' => round($distance, 2)],
                    422
                );
            }
        }

        $date = $request->input('date') ?: Carbon::now(config('app.timezone', 'UTC'))->toDateString();
        $time = $request->input('time') ?: Carbon::now(config('app.timezone', 'UTC'))->toTimeString();

        $data = [
            'weekly_run_sheet_id' => (int)$request->weekly_run_sheet_id,
            'weekly_run_sheet_entry_id' => (int)$request->weekly_run_sheet_entry_id,
            'nfc_tag_id' => (int)$request->nfc_tag_id,
            'user_id' => Auth::id(),
            'date' => $date,
            'time' => $time,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'reason' => $request->reason,
        ];

        // Check if already scanned on this date
        if ($this->runSheetRepository->isAlreadyScanned($data)) {
            return $this->errorResponse('This NFC tag has already been scanned for this weekly run sheet entry today.', null, 422);
        }

        // Image upload handling
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('documents/WeeklyRunSheetScans', 'public');
            $data['image'] = Storage::disk('public')->url($path);
        }

        $result = $this->runSheetRepository->storeScan($data);

        return $this->successResponse($result['scan'], 'Scan recorded successfully.');
    }
}
