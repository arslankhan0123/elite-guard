<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\SiteTourItemRepository;
use App\Repositories\ShiftRepository;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SiteTourItemApiController extends Controller
{
    use ApiResponser;
    protected $siteTourItemRepo;
    protected $shiftRepo;

    // Inject the repository via constructor
    public function __construct(SiteTourItemRepository $siteTourItemRepo, ShiftRepository $shiftRepo)
    {
        $this->siteTourItemRepo = $siteTourItemRepo;
        $this->shiftRepo = $shiftRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/site-tour-items/user",
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
        Log::info('userSiteTourItems Request:', $request->all());

        $user = Auth::user();

        // Automatically resolve the active (or next upcoming) shift.
        // This way mobile does not need to send shift_id manually.
        $activeShift = $this->shiftRepo->getActiveShift();

        if (!$activeShift) {
            return $this->successResponse([
                'status'          => true,
                'message'         => 'No active or upcoming shift found.',
                'shift'           => null,
                'site_tour_items' => [],
            ], 'No active or upcoming shift found.');
        }

        $shift_id  = $activeShift->id;
        $start_date = $activeShift->date;
        $end_date   = $activeShift->date;

        $data = $this->siteTourItemRepo->getUserSiteTourItems($user, $start_date, $end_date, $shift_id);

        return $this->successResponse($data, 'Assigned site tour items fetched.');
    }

    /**
     * @OA\Post(
     *     path="/api/site-tour-items/scan",
     *     summary="Scan an NFC tag for a site tour item",
     *     tags={"Site Tour Items"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"site_tour_item_id","nfc_tag_id","site_id","date","time"},
     *                 @OA\Property(property="site_tour_item_id", type="integer", example=1),
     *                 @OA\Property(property="nfc_tag_id", type="integer", example=2),
     *                 @OA\Property(property="site_id", type="integer", example=3),
     *                 @OA\Property(property="date", type="string", format="date", example="2026-07-14"),
     *                 @OA\Property(property="time", type="string", example="14:30:00"),
     *                 @OA\Property(property="reason", type="string", nullable=true, example="NFC tag was inaccessible"),
     *                 @OA\Property(property="image", type="string", format="binary", description="Optional image/photo taken during scanning")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="NFC tag scanned successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="NFC tag scanned and saved successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function storeScan(Request $request)
    {
        Log::info('Store Scan Request:', $request->all());
        $request->validate([
            'site_tour_item_id' => 'required|integer',
            'nfc_tag_id' => 'required|integer',
            'site_id' => 'required|integer',
            'date' => 'required|date',
            'time' => 'required',
            'reason' => 'nullable|string',
            'image' => 'nullable|image'
        ]);

        $user = Auth::user();
        $response = $this->siteTourItemRepo->storeScan($request, $user);

        return $this->successResponse($response, $response['message']);
    }
}
