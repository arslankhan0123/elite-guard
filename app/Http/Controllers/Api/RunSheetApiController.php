<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RunSheet;
use App\Models\Site;
use App\Repositories\RunSheetRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\CommonTrait;

class RunSheetApiController extends Controller
{
    use ApiResponser, CommonTrait;

    protected $runSheetRepo;

    public function __construct(RunSheetRepository $runSheetRepo)
    {
        $this->runSheetRepo = $runSheetRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/run-sheets",
     *     summary="Get run sheets for the authenticated user",
     *     tags={"Run Sheets"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filter by date (YYYY-MM-DD). Defaults to today if not provided.",
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
     *                 @OA\Property(property="run_sheets", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="site_id", type="integer", example=1),
     *                     @OA\Property(property="date", type="string", format="date", example="2026-05-07"),
     *                     @OA\Property(property="run_sheet_name", type="string", example="Mobile Patrol Check"),
     *                     @OA\Property(property="start_time", type="string", example="10:00:00"),
     *                     @OA\Property(property="end_time", type="string", example="15:00:00"),
     *                     @OA\Property(property="duration", type="string", example="15 Min."),
     *                     @OA\Property(property="job_type", type="string", example="Mobile Patrol"),
     *                     @OA\Property(property="sequence", type="string", example="1 of 1"),
     *                     @OA\Property(property="site", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Elite Plaza"),
     *                         @OA\Property(property="company", type="object")
     *                     )
     *                 ))
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $date = $request->query('date'); // Optional date filter

        $data = $this->runSheetRepo->getUserRunSheets($user, $date);

        return $this->successResponse($data, 'Run sheets fetched successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/run-sheets/scan",
     *     summary="Record an NFC tag scan for a run sheet",
     *     description="Validates scan location and prevents duplicate scans for the same day.",
     *     tags={"Run Sheets"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"run_sheet_id", "nfc_tag_id"},
     *             @OA\Property(property="run_sheet_id", type="integer", example=46),
     *             @OA\Property(property="nfc_tag_id", type="integer", example=2),
     *             @OA\Property(property="latitude", type="string", example="31.5038682"),
     *             @OA\Property(property="longitude", type="string", example="74.3480792")
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
            'nfc_tag_id' => 'required|exists:nfc_tags,id',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
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

        $data = $request->only(['run_sheet_id', 'nfc_tag_id', 'latitude', 'longitude']);
        $data['user_id'] = Auth::id();

        // Check if already scanned today
        if ($this->runSheetRepo->isAlreadyScanned($data)) {
            return $this->errorResponse('This NFC tag has already been scanned for this run sheet today.', null, 422);
        }

        $scan = $this->runSheetRepo->storeScan($data);

        return $this->successResponse($scan, 'Scan recorded successfully.');
    }
}
