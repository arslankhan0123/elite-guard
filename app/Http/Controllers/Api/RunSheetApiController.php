<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\RunSheetRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RunSheetApiController extends Controller
{
    use ApiResponser;

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
}
