<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Site;
use App\Repositories\ScheduleRepository;
use Illuminate\Http\Request;
use App\Repositories\SiteRepository;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SiteApiController extends Controller
{
    use ApiResponser;
    protected $siteRepo;
    protected $scheduleRepo;

    // Inject the repository via constructor
    public function __construct(SiteRepository $siteRepo, ScheduleRepository $scheduleRepo)
    {
        $this->siteRepo = $siteRepo;
        $this->scheduleRepo = $scheduleRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/sites",
     *     summary="Get sites assigned to the authenticated user",
     *     tags={"Sites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Assigned sites fetched.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Assigned sites fetched."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $dateString = $request->query('date');
        $date = $dateString ? Carbon::parse($dateString) : Carbon::now();
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');

        $schedules = Schedule::with(['shifts.site.company'])
            ->where('user_id', Auth::id())
            ->where('week_start_date', $weekStart)
            ->get();

        $data = [
            'status' => true,
            'message' => 'Schedules retrieved successfully',
            'week_start_date' => $weekStart,
            'sites' => $schedules
        ];

        return $this->successResponse($data, 'Scheduled Sites of the week fetched.');
    }
}
