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

    public function index()
    {
        // Use the repository to get all NFC tags
        $nfcTags = $this->nfcTagsRepo->getAllNfcTags();
        return $this->successResponse($nfcTags, 'All NFC tags fetched.');
    }
}
