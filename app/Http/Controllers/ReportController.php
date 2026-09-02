<?php

namespace App\Http\Controllers;

use App\Repositories\ReportRepository;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportRepo;

    public function __construct(ReportRepository $reportRepo)
    {
        $this->reportRepo = $reportRepo;
    }

    public function index(Request $request)
    {
        $type        = $request->get('type', 'companies');
        $date_filter = $request->get('date_filter', '');

        // Fetch users and sites for shift report dropdowns
        $users = [];
        $sites = [];
        if ($type === 'shifts') {
            $users = \App\Models\User::whereHas('employee')->orderBy('name')->get();
            $sites = \App\Models\Site::orderBy('name')->get();
        }

        $defaultStartDate = null;
        $defaultEndDate = null;
        if ($type === 'shifts') {
            $defaultStartDate = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
            $defaultEndDate = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $extraFilters = [
            'user_id' => $request->get('user_id'),
            'site_ids' => $request->get('site_ids', []),
            'start_date' => $request->get('start_date', $defaultStartDate),
            'end_date' => $request->get('end_date', $defaultEndDate),
            'start_time' => $request->get('start_time'),
            'end_time' => $request->get('end_time'),
        ];

        $results = $this->reportRepo->getReport($type, $date_filter, $extraFilters);

        $matrix = [];
        $reportSites = collect();
        $reportUsers = collect();
        $userTotals = [];
        $siteTotals = [];
        $grandTotal = 0;

        if ($type === 'shifts') {
            $matrix = [];
            $reportSitesMap = collect();
            $reportUsersMap = collect();
            $userTotals = [];
            $siteTotals = [];
            $grandTotal = 0;

            foreach ($results as $shift) {
                $user = $shift->schedule->user ?? null;
                if (!$user) continue;
                $userId = $user->id;
                $reportUsersMap->put($userId, $user);

                $start = \Carbon\Carbon::parse($shift->start_time);
                $end = \Carbon\Carbon::parse($shift->end_time);
                if ($end->lt($start)) {
                    $end->addDay();
                }
                $hours = $start->diffInMinutes($end) / 60;

                // Resolve site(s) for this shift (support assigned sites and runsheets)
                $shiftSites = collect();
                if ($shift->site) {
                    $shiftSites->push($shift->site);
                } elseif ($shift->type === 'runsheet' || $shift->weeklyRunSheet) {
                    $rsName = $shift->weeklyRunSheet->name ?? ($shift->shift_name ?: 'Runsheet');
                    $rsId = 'runsheet_' . ($shift->weekly_run_sheet_id ?? $shift->id);
                    $virtualSite = (object)[
                        'id' => $rsId,
                        'name' => 'Runsheet: ' . $rsName,
                    ];
                    $shiftSites->push($virtualSite);
                } else {
                    $virtualSite = (object)[
                        'id' => 'site_other',
                        'name' => 'Other / Unassigned',
                    ];
                    $shiftSites->push($virtualSite);
                }

                foreach ($shiftSites as $siteObj) {
                    $sKey = (string)$siteObj->id;
                    if (!$reportSitesMap->has($sKey)) {
                        $reportSitesMap->put($sKey, $siteObj);
                    }

                    if (!isset($matrix[$userId][$sKey])) {
                        $matrix[$userId][$sKey] = 0;
                    }
                    $matrix[$userId][$sKey] += $hours;

                    if (!isset($siteTotals[$sKey])) {
                        $siteTotals[$sKey] = 0;
                    }
                    $siteTotals[$sKey] += $hours;
                }

                if (!isset($userTotals[$userId])) {
                    $userTotals[$userId] = 0;
                }
                $userTotals[$userId] += $hours;

                $grandTotal += $hours;
            }

            $reportSites = $reportSitesMap->values()->sortBy('name');
            $reportUsers = $reportUsersMap->values()->sortBy('name');
        }

        return view('admin.reports.index', compact(
            'results', 'type', 'date_filter', 'users', 'sites', 'extraFilters',
            'matrix', 'reportSites', 'reportUsers', 'userTotals', 'siteTotals', 'grandTotal'
        ));
    }
}
