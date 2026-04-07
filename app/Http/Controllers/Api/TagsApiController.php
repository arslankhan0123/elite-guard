<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NfcTag;
use App\Repositories\NfcTagsRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class TagsApiController extends Controller
{
    use ApiResponser;
    protected $nfcTagsRepo;

    // Inject the repository via constructor
    public function __construct(NfcTagsRepository $nfcTagsRepo)
    {
        $this->nfcTagsRepo = $nfcTagsRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/nfc-tags",
     *     summary="Get all NFC tags",
     *     tags={"NFC Tags"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="All NFC tags fetched.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="All NFC tags fetched."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        // Use the repository to get all NFC tags
        $nfcTags = $this->nfcTagsRepo->getAllNfcTags();
        return $this->successResponse($nfcTags, 'All NFC tags fetched.');
    }

    /**
     * @OA\Get(
     *     path="/api/nfc-tags/{site_id}",
     *     summary="Get NFC tags for a specific site",
     *     tags={"NFC Tags"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="site_id",
     *         in="path",
     *         required=true,
     *         description="ID of the site to fetch NFC tags for",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="NFC tags for the specified site fetched.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="NFC tags for the specified site fetched."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Site not found or no tags found."
     *     )
     * )
     */
    public function siteTags($site_id)
    {
        // Use the repository to get all NFC tags for a specific site
        $nfcTags = $this->nfcTagsRepo->getNfcTagsBySiteId($site_id);
        return $this->successResponse($nfcTags, 'NFC tags for the specified site fetched.');
    }

    /**
     * @OA\Get(
     *     path="/api/nfc-tags/checkSiteTags/{site_id}",
     *     summary="Get NFC tag check points for a specific site",
     *     tags={"NFC Tags"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="site_id",
     *         in="path",
     *         required=true,
     *         description="ID of the site to fetch NFC tag check points for",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="NFC tag check points for the specified site fetched.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="NFC tags Check Points for the specified site fetched."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Site not found or no check points found."
     *     )
     * )
     */
    public function checkSiteTags($site_id)
    {
        // Use the repository to get all NFC tags for a specific site
        $nfcTags = $this->nfcTagsRepo->getCheckSiteTags($site_id);
        return $this->successResponse($nfcTags, 'NFC tags Check Points for the specified site fetched.');
    }

    /**
     * @OA\Get(
     *     path="/api/nfc-tags/check/points",
     *     summary="Get all NFC tag check points",
     *     tags={"NFC Tags"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="All NFC tag check points fetched.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="NFC Tags Check Points retrieved successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function checkPoints()
    {
        $nfcTags = $this->nfcTagsRepo->getCheckPoints();
        return $this->successResponse($nfcTags, 'NFC Tags Check Points retrieved successfully.');
    }
}
