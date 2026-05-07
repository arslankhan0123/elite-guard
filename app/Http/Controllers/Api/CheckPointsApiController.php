<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\SiteRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPointsApiController extends Controller
{
    use ApiResponser;

    protected $siteRepo;

    public function __construct(SiteRepository $siteRepo)
    {
        $this->siteRepo = $siteRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/check-points",
     *     summary="Get all sites/check-points",
     *     tags={"Check Points"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="All sites fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="All sites fetched successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Sites retrieved successfully"),
     *                 @OA\Property(property="sites", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $data = $this->siteRepo->getAllSitesAndNfcTags();

        return $this->successResponse($data, 'All sites and NfcTags fetched successfully.');
    }
}
