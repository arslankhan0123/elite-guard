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
     *     summary="Get weekly runsheets assigned to the authenticated user",
     *     tags={"Weekly Runsheets"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Assigned weekly runsheets fetched successfully")
     * )
     */
    public function userRunSheets(Request $request)
    {
        return $this->successResponse(
            $this->runSheetRepository->getUserAssignedWeeklyRunSheets($request->user()),
            'Assigned weekly runsheets fetched successfully.'
        );
    }
}
