<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\SiteTourItemRepository;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;

class SiteTourItemApiController extends Controller
{
    use ApiResponser;
    protected $siteTourItemRepo;

    // Inject the repository via constructor
    public function __construct(SiteTourItemRepository $siteTourItemRepo)
    {
        $this->siteTourItemRepo = $siteTourItemRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/site-tour-items",
     *     summary="Get site tour items assigned to the authenticated user",
     *     tags={"Site Tour Items"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Optional start date to filter items (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Optional end date to filter items (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assigned site tour items fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Assigned site tour items fetched."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Assigned site tour items retrieved successfully"),
     *                 @OA\Property(property="site_tour_items", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function userSiteTourItems(Request $request)
    {
        $user = Auth::user();
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        
        // Also support single 'date' parameter for backward compatibility if needed
        $date = $request->input('date');
        if ($date && !$start_date && !$end_date) {
            $start_date = $date;
            $end_date = $date;
        }
        
        // Default to today's date if absolutely no date is provided
        if (!$start_date && !$end_date && !$date) {
            $today = \Carbon\Carbon::now()->format('Y-m-d');
            $start_date = $today;
            $end_date = $today;
        }

        $data = $this->siteTourItemRepo->getUserSiteTourItems($user, $start_date, $end_date);

        return $this->successResponse($data, 'Assigned site tour items fetched.');
    }
}
