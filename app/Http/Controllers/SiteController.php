<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Site;
use App\Repositories\SiteRepository;
use Illuminate\Http\Request;

class SiteController extends Controller
{
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

        return view('admin.sites.index', compact('sites'));
    }

    public function create()
    {
        $companies = Company::where('status', true)->get();
        return view('admin.sites.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'status' => 'required|boolean',
        ]);

        Site::create($request->all());

        return redirect()->route('sites.index')->with('success', 'Site created successfully.');
    }

    public function edit($site_id)
    {
        $site = Site::findOrFail($site_id);
        $companies = Company::where('status', true)->get();
        return view('admin.sites.edit', compact('site', 'companies'));
    }

    public function update(Request $request, $site_id)
    {
        $site = Site::findOrFail($site_id);

        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'status' => 'required|boolean',
        ]);

        $site->update($request->all());

        return redirect()->route('sites.index')->with('success', 'Site updated successfully.');
    }

    public function delete($site_id)
    {
        $site = Site::findOrFail($site_id);
        $site->delete();

        return redirect()->route('sites.index')->with('success', 'Site deleted successfully.');
    }
}
