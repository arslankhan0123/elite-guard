<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\WeeklyRunSheetRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class WeeklyRunSheetApiController extends Controller
{
    use ApiResponser;

    public function __construct(private WeeklyRunSheetRepository $runSheetRepository)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/run-sheets/weekly",
     *     summary="Get all weekly runsheets",
     *     description="Returns all weekly runsheets with all weekday child entries.",
     *     tags={"Weekly Runsheets"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Weekly runsheets fetched successfully")
     * )
     */
    public function index(Request $request)
    {
        return $this->successResponse(
            $this->runSheetRepository->getAllWeeklyRunSheets(),
            'Weekly runsheets fetched successfully.'
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
}
