<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Repositories\CompanyRepository;

class CompanyController extends Controller
{
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

        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'status' => 'required|boolean',
        ]);

        $companies = $this->companyRepo->createCompany($request);
        return redirect()->route('companies.index')->with('success', 'Company created successfully.');
    }

    public function edit($company_id)
    {
        $company = Company::findOrFail($company_id);
        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, $company_id)
    {
        $company = Company::findOrFail($company_id);

        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'status' => 'required|boolean',
        ]);

        $companies = $this->companyRepo->updateCompany($request, $company_id);
        return redirect()->route('companies.index')->with('success', 'Company updated successfully.');
    }

    public function delete($company_id)
    {
        $companies = $this->companyRepo->deleteCompany($company_id);
        return redirect()->route('companies.index')->with('success', 'Company deleted successfully.');
    }
}
