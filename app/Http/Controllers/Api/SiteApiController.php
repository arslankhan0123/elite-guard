<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use App\Repositories\SiteRepository;
use App\Traits\ApiResponser;

class SiteApiController extends Controller
{
    use ApiResponser;
    protected $siteRepo;

    // Inject the repository via constructor
    public function __construct(SiteRepository $siteRepo)
    {
        $this->siteRepo = $siteRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/sites",
     *     summary="Get all sites",
     *     tags={"Sites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="All sites fetched.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="All sites fetched."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        // Use the repository to get all sites
        $sites = $this->siteRepo->getAllSites();
        return $this->successResponse($sites, 'All sites fetched.');
    }
}
