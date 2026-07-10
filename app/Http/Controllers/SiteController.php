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
            'name'       => 'required|string|max:255',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'nullable|string|max:20',
            'city'       => 'nullable|string|max:100',
            'country'    => 'nullable|string|max:100',
            'address'    => 'nullable|string',
            'latitude'   => 'nullable|numeric|between:-90,90',
            'longitude'  => 'nullable|numeric|between:-180,180',
            'start_time' => 'nullable',
            'end_time'   => 'nullable',
            'status'     => 'required|boolean',
        ]);

        $this->siteRepo->createSite($request);

        return redirect()->route('sites.index')->with('success', 'Site created successfully.');
    }

    public function edit($site_id)
    {
        $site = $this->siteRepo->findSiteById($site_id);
        $companies = Company::where('status', true)->get();
        return view('admin.sites.edit', compact('site', 'companies'));
    }

    public function update(Request $request, $site_id)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name'       => 'required|string|max:255',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'nullable|string|max:20',
            'city'       => 'nullable|string|max:100',
            'country'    => 'nullable|string|max:100',
            'address'    => 'nullable|string',
            'latitude'   => 'nullable|numeric|between:-90,90',
            'longitude'  => 'nullable|numeric|between:-180,180',
            'start_time' => 'nullable',
            'end_time'   => 'nullable',
            'status'     => 'required|boolean',
        ]);

        $this->siteRepo->updateSite($request, $site_id);

        return redirect()->route('sites.index')->with('success', 'Site updated successfully.');
    }

    public function delete($site_id)
    {
        $this->siteRepo->deleteSite($site_id);

        return redirect()->route('sites.index')->with('success', 'Site deleted successfully.');
    }

    public function nfcTags($site_id)
    {
        $site = Site::with(['nfcTags' => function($q) {
            $q->orderBy('id', 'desc');
        }])->findOrFail($site_id);

        return view('admin.sites.nfc-tags', compact('site'));
    }

    public function tours($site_id)
    {
        $site = Site::with(['siteTours' => function($q) {
            $q->orderBy('id', 'desc');
        }])->findOrFail($site_id);

        // Fetch users for the dropdown (user requested to show all users)
        $guards = \App\Models\User::all();

        $nfcTags = \App\Models\NfcTag::where('site_id', $site_id)->get();

        return view('admin.sites.tours', compact('site', 'guards', 'nfcTags'));
    }

    public function storeTour(Request $request)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'scheduled_days' => 'required|array',
            'tag_type' => 'required|string',
            'tags' => 'required|array',
            'assigned_guards' => 'required|array',
            'max_duration' => 'nullable|array',
        ]);

        $tour = \App\Models\SiteTour::create([
            'site_id' => $request->site_id,
            'name' => $request->name,
            'description' => $request->description,
            'scheduled_days' => $request->scheduled_days,
            'is_continuous' => $request->has('is_continuous'),
            'schedule_type' => $request->schedule_type,
            'specific_times' => $request->specific_times ?? [],
            'max_duration' => $request->max_duration ?? [],
            'tag_type' => $request->tag_type,
            'tags' => $request->tags,
            'assigned_guards' => $request->assigned_guards,
        ]);

        return response()->json(['tour' => $tour]);
    }

    public function updateTour(Request $request, $id)
    {
        $tour = \App\Models\SiteTour::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'scheduled_days' => 'required|array',
            'tag_type' => 'required|string',
            'tags' => 'required|array',
            'assigned_guards' => 'required|array',
            'max_duration' => 'nullable|array',
        ]);

        $tour->update([
            'name' => $request->name,
            'description' => $request->description,
            'scheduled_days' => $request->scheduled_days,
            'is_continuous' => $request->has('is_continuous'),
            'schedule_type' => $request->schedule_type,
            'specific_times' => $request->specific_times ?? [],
            'max_duration' => $request->max_duration ?? [],
            'tag_type' => $request->tag_type,
            'tags' => $request->tags,
            'assigned_guards' => $request->assigned_guards,
        ]);

        return response()->json(['tour' => $tour]);
    }

    public function deleteTour($id)
    {
        $tour = \App\Models\SiteTour::findOrFail($id);
        $tour->delete();

        return redirect()->back()->with('success', 'Tour deleted successfully.');
    }
}
