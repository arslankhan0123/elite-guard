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
            $reportSites = $results->pluck('site')->filter()->unique('id')->sortBy('name');
            $reportUsers = $results->map(fn($s) => $s->schedule->user ?? null)->filter()->unique('id')->sortBy('name');

            foreach ($results as $shift) {
                $userId = $shift->schedule->user_id ?? null;
                $siteId = $shift->site_id;
                if (!$userId || !$siteId) continue;

                $start = \Carbon\Carbon::parse($shift->start_time);
                $end = \Carbon\Carbon::parse($shift->end_time);
                if ($end->lt($start)) {
                    $end->addDay();
                }
                $hours = $start->diffInMinutes($end) / 60;

                if (!isset($matrix[$userId][$siteId])) {
                    $matrix[$userId][$siteId] = 0;
                }
                $matrix[$userId][$siteId] += $hours;

                if (!isset($userTotals[$userId])) {
                    $userTotals[$userId] = 0;
                }
                $userTotals[$userId] += $hours;

                if (!isset($siteTotals[$siteId])) {
                    $siteTotals[$siteId] = 0;
                }
                $siteTotals[$siteId] += $hours;

                $grandTotal += $hours;
            }
        }

        return view('admin.reports.index', compact(
            'results', 'type', 'date_filter', 'users', 'sites', 'extraFilters',
            'matrix', 'reportSites', 'reportUsers', 'userTotals', 'siteTotals', 'grandTotal'
        ));
    }
}
