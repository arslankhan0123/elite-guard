<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Site;
use App\Models\Schedule;
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
            'interval'   => 'required|integer|min:1',
            'open_time'  => 'required|integer|min:0',
            'grace_time' => 'required|integer|min:0',
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
            'interval'   => 'required|integer|min:1',
            'open_time'  => 'required|integer|min:0',
            'grace_time' => 'required|integer|min:0',
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
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'filter_site_id' => 'nullable|exists:sites,id',
            'shift_name' => 'nullable|string|max:255',
        ]);

        $startDate = \Carbon\Carbon::parse(
            $request->input('start_date') ?: $request->input('end_date') ?: now()->toDateString()
        )->startOfDay();
        $endDate = \Carbon\Carbon::parse(
            $request->input('end_date') ?: $request->input('start_date') ?: now()->toDateString()
        )->endOfDay();

        if ($endDate->lt($startDate)) {
            return back()->withErrors(['end_date' => 'End date must be on or after the start date.'])->withInput();
        }

        $weekStart = $startDate->copy();
        $weekEnd = $endDate->copy();

        $site = Site::findOrFail($site_id);

        $siteTours = \App\Models\SiteTour::where('site_id', $site_id)
        ->where(function ($query) use ($weekStart, $weekEnd, $site_id) {
            $query->whereHas('items', function($q) use ($weekStart, $weekEnd, $site_id) {
                $q->where('site_id', $site_id)
                  ->whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')]);
            })->orWhereHas('shift', function ($q) use ($weekStart, $weekEnd) {
                $q->whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')]);
            });
        })
        ->when($request->filled('user_id'), function ($query) use ($request) {
            $query->where('user_id', $request->integer('user_id'));
        })
        ->when($request->filled('filter_site_id'), function ($query) use ($request) {
            $query->where('site_id', $request->integer('filter_site_id'));
        })
        ->when($request->filled('shift_name'), function ($query) use ($request) {
            $query->whereHas('shift', function ($shiftQuery) use ($request) {
                $shiftQuery->where('shift_name', $request->input('shift_name'));
            });
        })
        ->with(['user', 'items' => function($q) use ($weekStart, $weekEnd, $site_id) {
            $q->where('site_id', $site_id)
              ->whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
              ->with('site')
              ->withCount('scans');
        }])
        ->orderBy('id', 'desc')
        ->get();

        // Fetch users for the dropdown (user requested to show all users)
        $guards = \App\Models\User::all();
        $filterSites = Site::orderBy('name')->get();
        $shiftNames = \App\Models\Shift::whereNotNull('shift_name')->where('shift_name', '!=', '')
            ->distinct()->orderBy('shift_name')->pluck('shift_name');

        $nfcTags = \App\Models\NfcTag::where('site_id', $site_id)->get();

        $prevWeek = $weekStart->copy()->subWeek()->format('Y-m-d');
        $nextWeek = $weekStart->copy()->addWeek()->format('Y-m-d');
        
        $weekStartFormatted = $weekStart->format('d M');
        $weekEndFormatted = $weekEnd->format('d M, Y');

        return view('admin.sites.tours', compact('siteTours', 'site', 'guards', 'filterSites', 'shiftNames', 'nfcTags', 'prevWeek', 'nextWeek', 'weekStartFormatted', 'weekEndFormatted', 'weekStart', 'startDate', 'endDate'));
    }

    public function allTours(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'filter_site_id' => 'nullable|exists:sites,id',
            'shift_name' => 'nullable|string|max:255',
        ]);

        $startDate = \Carbon\Carbon::parse(
            $request->input('start_date') ?: $request->input('end_date') ?: now()->toDateString()
        )->startOfDay();
        $endDate = \Carbon\Carbon::parse(
            $request->input('end_date') ?: $request->input('start_date') ?: now()->toDateString()
        )->endOfDay();

        if ($endDate->lt($startDate)) {
            return back()->withErrors(['end_date' => 'End date must be on or after the start date.'])->withInput();
        }

        $weekStart = $startDate->copy();
        $weekEnd = $endDate->copy();

        // Fetch all site tours that have items in this week
        $siteTours = \App\Models\SiteTour::with(['site.company', 'user', 'items' => function($q) use ($weekStart, $weekEnd) {
            $q->whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
               ->with('site')
               ->withCount('scans');
        }])
        ->where(function ($query) use ($weekStart, $weekEnd) {
            $query->whereHas('items', function($q) use ($weekStart, $weekEnd) {
                $q->whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')]);
            })->orWhereHas('shift', function ($q) use ($weekStart, $weekEnd) {
                $q->whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')]);
            });
        })
        ->when($request->filled('user_id'), function ($query) use ($request) {
            $query->where('user_id', $request->integer('user_id'));
        })
        ->when($request->filled('filter_site_id'), function ($query) use ($request) {
            $query->where('site_id', $request->integer('filter_site_id'));
        })
        ->when($request->filled('shift_name'), function ($query) use ($request) {
            $query->whereHas('shift', function ($shiftQuery) use ($request) {
                $shiftQuery->where('shift_name', $request->input('shift_name'));
            });
        })
        ->orderBy('id', 'desc')
        ->get();

        $guards = \App\Models\User::all();
        $nfcTags = \App\Models\NfcTag::all();
        $sites = \App\Models\Site::with('company')->where('status', true)->get();
        $filterSites = Site::orderBy('name')->get();
        $shiftNames = \App\Models\Shift::whereNotNull('shift_name')->where('shift_name', '!=', '')
            ->distinct()->orderBy('shift_name')->pluck('shift_name');

        $prevWeek = $weekStart->copy()->subWeek()->format('Y-m-d');
        $nextWeek = $weekStart->copy()->addWeek()->format('Y-m-d');
        
        $weekStartFormatted = $weekStart->format('d M');
        $weekEndFormatted = $weekEnd->format('d M, Y');

        $site = null; // Set $site as null to indicate global view

        return view('admin.sites.tours', compact('siteTours', 'site', 'guards', 'filterSites', 'shiftNames', 'nfcTags', 'sites', 'prevWeek', 'nextWeek', 'weekStartFormatted', 'weekEndFormatted', 'weekStart', 'startDate', 'endDate'));
    }

    public function storeTour(Request $request)
    {
        $baseWeekStart = $request->input('week_start_date') ? \Carbon\Carbon::parse($request->input('week_start_date'))->startOfWeek(\Carbon\Carbon::MONDAY) : \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $weekEndDate = $baseWeekStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        // Find users scheduled on this site for this week
        $schedules = Schedule::where('week_start_date', $baseWeekStart->format('Y-m-d'))->get();
        $scheduledUserIds = $schedules->pluck('user_id')->unique();
        $request->validate([
            'site_id' => 'nullable|exists:sites,id',
            'name' => 'required|string|max:255',
            'scheduled_days' => 'nullable|array',
            'tag_type' => 'nullable|string',
            'tags' => 'nullable|array',
            'assigned_guards' => 'nullable|array',
            'max_duration' => 'nullable|array',
            'interval' => 'nullable|string|max:255',
            'open_time' => 'nullable|string|max:255',
            'grace_time' => 'nullable|string|max:255',
        ]);

        $site = $request->site_id ? \App\Models\Site::find($request->site_id) : null;

        $lastTour = null;
        $isWeekUpdate = $request->tour_id === 'week_update';

        foreach ($scheduledUserIds as $userId) {
            // Find all shifts of this user for this week (across all sites)
            $userShifts = \App\Models\Shift::whereHas('schedule', function($q) use ($userId, $baseWeekStart) {
                $q->where('user_id', $userId)
                  ->where('week_start_date', $baseWeekStart->format('Y-m-d'));
            })->get();

            $days = $userShifts->map(function($shift) {
                return \Carbon\Carbon::parse($shift->date)->format('l');
            })->unique()->values()->all();

            $firstShift = $userShifts->first();
            $tourSiteId = $firstShift ? $firstShift->site_id : ($request->site_id ?? null);

            $tourData = [
                'site_id' => $tourSiteId,
                'user_id' => $userId,
                'name' => $request->name,
                'description' => $request->description,
                'scheduled_days' => $days,
                'is_continuous' => $request->has('is_continuous'),
                'schedule_type' => $request->schedule_type,
                'specific_times' => $request->specific_times ?? [],
                'max_duration' => $request->max_duration ?? [],
                'tag_type' => $request->tag_type ?? 'nfc',
                'tags' => $request->tags,
                'assigned_guards' => $request->assigned_guards ?? [],
                'interval' => $request->interval,
                'open_time' => $request->open_time,
                'grace_time' => $request->grace_time,
            ];

            if ($isWeekUpdate) {
                $tour = \App\Models\SiteTour::where('user_id', $userId)
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
            
            $lastTour = $tour;

            $intendedKeys = [];

            foreach ($userShifts as $shift) {
                // Generate SiteTourItems for this specific shift
                if ($shift->start_time && $shift->end_time && $request->interval) {
                    $intervalMinutes = (int) $request->interval;
                    if ($intervalMinutes > 0) {
                        $startTime = \Carbon\Carbon::parse($shift->start_time);
                        $endTime = \Carbon\Carbon::parse($shift->end_time);

                        if ($endTime->lt($startTime)) {
                            $endTime->addDay();
                        }

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
                                $date = $shift->date;
                                $key = $date . '|' . $shift->site_id . '|' . $itemStart->format('H:i:s') . '|' . $itemEnd->format('H:i:s');
                                $intendedKeys[$key] = [
                                    'site_tour_id' => $tour->id,
                                    'user_id' => $tour->user_id,
                                    'site_id' => $shift->site_id,
                                    'type' => null,
                                    'status' => false,
                                    'date' => $date,
                                    'start_time' => $itemStart->format('H:i:s'),
                                    'end_time' => $itemEnd->format('H:i:s'),
                                    'created_at' => now()->toDateTimeString(),
                                    'updated_at' => now()->toDateTimeString(),
                                ];
                            }
                            
                            $n++;
                        }
                    }
                }
            }

            $existingItems = \App\Models\SiteTourItem::where('site_tour_id', $tour->id)->get();
            
            foreach ($existingItems as $existing) {
                $sTime = \Carbon\Carbon::parse($existing->start_time)->format('H:i:s');
                $eTime = \Carbon\Carbon::parse($existing->end_time)->format('H:i:s');
                $key = $existing->date . '|' . $existing->site_id . '|' . $sTime . '|' . $eTime;
                
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

        if (!$lastTour) {
            $lastTour = \App\Models\SiteTour::where('site_id', $request->site_id)->latest()->first();
        }

        return response()->json(['tour' => $lastTour]);
    }

    public function updateTour(Request $request, $id)
    {
        $tour = \App\Models\SiteTour::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'scheduled_days' => 'nullable|array',
            'tag_type' => 'nullable|string',
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
            'scheduled_days' => $request->scheduled_days ?? ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            'is_continuous' => $request->has('is_continuous'),
            'schedule_type' => $request->schedule_type,
            'specific_times' => $request->specific_times ?? [],
            'max_duration' => $request->max_duration ?? [],
            'tag_type' => $request->tag_type ?? 'nfc',
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

        $query = \App\Models\SiteTour::query();
        if ($site_id !== 'all' && $site_id != 0) {
            $query->where('site_id', $site_id);
        }

        $toursToDelete = $query->whereHas('items', function($q) use ($baseWeekStart, $weekEndDate) {
                $q->whereBetween('date', [$baseWeekStart->format('Y-m-d'), $weekEndDate->format('Y-m-d')]);
            })->get();
            
        foreach ($toursToDelete as $t) {
            $t->delete();
        }

        return redirect()->back()->with('success', 'All tours for the selected week have been deleted successfully.');
    }
}
