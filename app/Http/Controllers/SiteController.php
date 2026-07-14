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

    public function tours($site_id, Request $request)
    {
        $week = $request->input('week');
        if ($week) {
            $weekStart = \Carbon\Carbon::parse($week)->startOfWeek(\Carbon\Carbon::MONDAY);
        } else {
            $weekStart = \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY);
        }
        
        $weekEnd = $weekStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        $site = Site::with(['siteTours' => function($q) use ($weekStart, $weekEnd) {
            $q->whereHas('items', function($q2) use ($weekStart, $weekEnd) {
                $q2->whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')]);
            })
            ->with(['items' => function($q3) use ($weekStart, $weekEnd) {
                $q3->whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')]);
            }])
            ->orderBy('id', 'desc');
        }])->findOrFail($site_id);

        // Fetch users for the dropdown (user requested to show all users)
        $guards = \App\Models\User::all();

        $nfcTags = \App\Models\NfcTag::where('site_id', $site_id)->get();

        $prevWeek = $weekStart->copy()->subWeek()->format('Y-m-d');
        $nextWeek = $weekStart->copy()->addWeek()->format('Y-m-d');
        
        $weekStartFormatted = $weekStart->format('d M');
        $weekEndFormatted = $weekEnd->format('d M, Y');

        return view('admin.sites.tours', compact('site', 'guards', 'nfcTags', 'prevWeek', 'nextWeek', 'weekStartFormatted', 'weekEndFormatted', 'weekStart'));
    }

    public function storeTour(Request $request)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'scheduled_days' => 'required|array',
            'tag_type' => 'required|string',
            'tags' => 'nullable|array',
            'assigned_guards' => 'nullable|array',
            'max_duration' => 'nullable|array',
            'interval' => 'nullable|string|max:255',
            'open_time' => 'nullable|string|max:255',
            'grace_time' => 'nullable|string|max:255',
        ]);

        $site = \App\Models\Site::with('users')->findOrFail($request->site_id);
        $users = $site->users;
        
        $tourData = [
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
            'assigned_guards' => $request->assigned_guards ?? [],
            'interval' => $request->interval,
            'open_time' => $request->open_time,
            'grace_time' => $request->grace_time,
        ];

        $tour = null;
        $toursCreated = [];
        $isWeekUpdate = $request->tour_id === 'week_update';
        
        $baseWeekStart = $request->input('week_start_date') ? \Carbon\Carbon::parse($request->input('week_start_date'))->startOfWeek(\Carbon\Carbon::MONDAY) : \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $weekEndDate = $baseWeekStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        if ($users->count() > 0) {
            foreach ($users as $user) {
                $data = $tourData;
                $data['user_id'] = $user->id;
                
                if ($isWeekUpdate) {
                    $tour = \App\Models\SiteTour::where('site_id', $request->site_id)
                        ->where('user_id', $user->id)
                        ->whereHas('items', function($q) use ($baseWeekStart, $weekEndDate) {
                            $q->whereBetween('date', [$baseWeekStart->format('Y-m-d'), $weekEndDate->format('Y-m-d')]);
                        })->first();
                        
                    if ($tour) {
                        $tour->update($data);
                    } else {
                        $tour = \App\Models\SiteTour::create($data);
                    }
                } else {
                    $tour = \App\Models\SiteTour::create($data);
                }
                $toursCreated[] = $tour;
            }
        } else {
            // Create a tour with no specific user
            if ($isWeekUpdate) {
                $tour = \App\Models\SiteTour::where('site_id', $request->site_id)
                    ->whereNull('user_id')
                    ->whereHas('items', function($q) use ($baseWeekStart, $weekEndDate) {
                        $q->whereBetween('date', [$baseWeekStart->format('Y-m-d'), $weekEndDate->format('Y-m-d')]);
                    })->first();
                    
                if ($tour) {
                    $tour->update($tourData);
                } else {
                    $tour = \App\Models\SiteTour::create($tourData);
                }
            } else {
                $tour = \App\Models\SiteTour::create($tourData);
            }
            $toursCreated[] = $tour;
        }

        // Generate SiteTourItems
        if ($site->start_time && $site->end_time && $request->interval) {
            $intervalMinutes = (int) $request->interval;
            if ($intervalMinutes > 0) {
                $startTime = \Carbon\Carbon::parse($site->start_time);
                $endTime = \Carbon\Carbon::parse($site->end_time);

                if ($endTime->lt($startTime)) {
                    $endTime->addDay();
                }

                $itemsData = [];
                $baseTime = $startTime->copy();
                $n = 1;

                while ($baseTime->copy()->addMinutes(($n - 1) * $intervalMinutes)->lt($endTime)) {
                    $itemStart = $baseTime->copy()->addMinutes(($n - 1) * $intervalMinutes);
                    if ($n > 1) {
                        $itemStart->addMinute();
                    }
                    
                    $itemEnd = $baseTime->copy()->addMinutes($n * $intervalMinutes);
                    
                    if ($itemEnd->gt($endTime)) {
                        $itemEnd = $endTime->copy();
                    }

                    if ($itemStart->lt($itemEnd)) {
                        $itemsData[] = [
                            'start_time' => $itemStart->format('H:i:s'),
                            'end_time' => $itemEnd->format('H:i:s'),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    
                    $n++;
                }

                if (!empty($itemsData)) {
                    foreach ($toursCreated as $createdTour) {
                        $scheduledDays = $createdTour->scheduled_days ?? [];
                        
                        if (empty($scheduledDays)) {
                            $scheduledDays = [ \Carbon\Carbon::now()->format('l') ];
                        }

                        $intendedKeys = [];
                        
                        foreach ($scheduledDays as $dayName) {
                            $date = $baseWeekStart->copy()->modify($dayName)->format('Y-m-d');
                            
                            foreach ($itemsData as $item) {
                                $key = $date . '|' . $item['start_time'] . '|' . $item['end_time'];
                                $intendedKeys[$key] = [
                                    'site_tour_id' => $createdTour->id,
                                    'user_id' => $createdTour->user_id,
                                    'site_id' => $createdTour->site_id,
                                    'type' => null,
                                    'status' => false,
                                    'date' => $date,
                                    'start_time' => $item['start_time'],
                                    'end_time' => $item['end_time'],
                                    'created_at' => now()->toDateTimeString(),
                                    'updated_at' => now()->toDateTimeString(),
                                ];
                            }
                        }
                        
                        $existingItems = \App\Models\SiteTourItem::where('site_tour_id', $createdTour->id)->get();
                        
                        foreach ($existingItems as $existing) {
                            $sTime = \Carbon\Carbon::parse($existing->start_time)->format('H:i:s');
                            $eTime = \Carbon\Carbon::parse($existing->end_time)->format('H:i:s');
                            $key = $existing->date . '|' . $sTime . '|' . $eTime;
                            
                            if (isset($intendedKeys[$key])) {
                                unset($intendedKeys[$key]);
                            } else {
                                $existing->delete();
                            }
                        }
                        
                        if (!empty($intendedKeys)) {
                            \App\Models\SiteTourItem::insert(array_values($intendedKeys));
                        }
                    }
                }
            }
        }

        return response()->json(['tour' => $tour]);
    }

    public function updateTour(Request $request, $id)
    {
        $tour = \App\Models\SiteTour::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'scheduled_days' => 'required|array',
            'tag_type' => 'required|string',
            'tags' => 'nullable|array',
            'assigned_guards' => 'nullable|array',
            'max_duration' => 'nullable|array',
            'interval' => 'nullable|string|max:255',
            'open_time' => 'nullable|string|max:255',
            'grace_time' => 'nullable|string|max:255',
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
            'interval' => $request->interval,
            'open_time' => $request->open_time,
            'grace_time' => $request->grace_time,
        ]);

        return response()->json(['tour' => $tour]);
    }

    public function deleteTour($id)
    {
        $tour = \App\Models\SiteTour::findOrFail($id);
        $tour->delete();

        return redirect()->back()->with('success', 'Tour deleted successfully.');
    }

    public function deleteWeekTours($site_id, Request $request)
    {
        $weekStartDate = $request->input('week_start_date');
        $baseWeekStart = $weekStartDate ? \Carbon\Carbon::parse($weekStartDate)->startOfWeek(\Carbon\Carbon::MONDAY) : \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $weekEndDate = $baseWeekStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        $toursToDelete = \App\Models\SiteTour::where('site_id', $site_id)
            ->whereHas('items', function($q) use ($baseWeekStart, $weekEndDate) {
                $q->whereBetween('date', [$baseWeekStart->format('Y-m-d'), $weekEndDate->format('Y-m-d')]);
            })->get();
            
        foreach ($toursToDelete as $t) {
            $t->delete();
        }

        return redirect()->back()->with('success', 'All tours for the selected week have been deleted successfully.');
    }
}
