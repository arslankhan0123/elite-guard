<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use App\Repositories\SiteRepository;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;

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
    public function index()
    {
        $user = Auth::user();
        $sites = $user->sites()->with('company')->orderBy('id', 'desc')->get();

        $data = [
            'status' => true,
            'message' => 'Assigned sites retrieved successfully',
            'sites' => $sites
        ];

        return $this->successResponse($data, 'Assigned sites fetched.');
    }
}
