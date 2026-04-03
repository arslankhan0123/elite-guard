<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Repositories\CompanyRepository;
use App\Traits\ApiResponser;

class CompanyApiController extends Controller
{
    use ApiResponser;
    protected $companyRepo;

    // Inject the repository via constructor
    public function __construct(CompanyRepository $companyRepo)
    {
        $this->companyRepo = $companyRepo;
    }

    public function index()
    {
        // Use the repository to get all companies
        $companies = $this->companyRepo->getAllCompanies();
        return $this->successResponse($companies, 'All companies fetched.');
    }
}
