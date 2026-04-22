<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ScheduleRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleApiController extends Controller
{
    use ApiResponser;
    protected $scheduleRepo;

    public function __construct(ScheduleRepository $scheduleRepo)
    {
        $this->scheduleRepo = $scheduleRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/schedules",
     *     summary="Get schedules for the authenticated user",
     *     description="Fetches schedules for the authenticated user for a specific week. Defaults to current week.",
     *     tags={"Schedules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Any date within the target week (format: YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Schedules fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Schedules fetched."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Schedules retrieved successfully"),
     *                 @OA\Property(property="week_start_date", type="string", example="2026-04-20"),
     *                 @OA\Property(property="schedules", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=40),
     *                         @OA\Property(property="user_id", type="integer", example=6),
     *                         @OA\Property(property="week_start_date", type="string", example="2026-04-20"),
     *                         @OA\Property(property="notes", type="string", example="Notes for the week"),
     *                         @OA\Property(property="days", type="array",
     *                             @OA\Items(type="object",
     *                                 @OA\Property(property="date", type="string", example="2026-04-20"),
     *                                 @OA\Property(property="day", type="string", example="Monday"),
     *                                 @OA\Property(property="total_duration", type="string", example="12h 30m"),
     *                                 @OA\Property(property="total_minutes", type="integer", example=750),
     *                                 @OA\Property(property="shifts", type="array", @OA\Items(type="object"))
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $date = $request->query('date');
        
        $data = $this->scheduleRepo->getSchedulesByWeek($user->id, $date);

        return $this->successResponse($data, 'Schedules fetched.');
    }
}
