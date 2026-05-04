<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\NoticeBoardRepository;
use App\Repositories\NumberRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class NoticeBoardApiController extends Controller
{
    use ApiResponser;
    protected $noticeBoardRepo;

    public function __construct(NoticeBoardRepository $noticeBoardRepo)
    {
        $this->noticeBoardRepo = $noticeBoardRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/notice-board",
     *     summary="Get all notices from the Notice Board",
     *     tags={"Notice Board"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Notice Board Data fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Notice Board Data fetched successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Notice Board Data fetched successfully."),
     *                 @OA\Property(property="notices", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="date", type="string", format="date", example="2026-05-04"),
     *                     @OA\Property(property="subject", type="string", example="Meeting Notice"),
     *                     @OA\Property(property="long_description", type="string", example="Description of the meeting..."),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-05-04T20:37:13Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-05-04T20:37:13Z")
     *                 ))
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $data = $this->noticeBoardRepo->getNoticeBoard();
        return $this->successResponse($data, 'Notice Board Data fetched successfully.');
    }
}
