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
}
