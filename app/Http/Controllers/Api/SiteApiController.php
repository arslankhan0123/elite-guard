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

    public function index()
    {
        // Use the repository to get all sites
        $sites = $this->siteRepo->getAllSites();
        return $this->successResponse($sites, 'All sites fetched.');
    }
}
