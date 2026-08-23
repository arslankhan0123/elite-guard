<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Site;
use App\Models\SiteTourItemScan;
use App\Models\WeeklyRunSheetScan;
use App\Models\SiteItemScan;
use App\Models\SiteScan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ChronologicalReportController extends Controller
{
    public function index(Request $request)
    {
        $users = User::orderBy('name')->get();
        $sites = Site::orderBy('name')->get();

        $selectedUser = $request->get('user_id');
        $selectedSite = $request->get('site_id');
        $startDate = $request->get('start_date') ?: Carbon::today()->format('Y-m-d');
        $endDate = $request->get('end_date') ?: Carbon::today()->format('Y-m-d');
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');

        $reports = [];
        $hasSearched = $request->has('start_date');

        if ($hasSearched) {
            $reports = $this->getChronologicalData($selectedUser, $selectedSite, $startDate, $endDate, $startTime, $endTime);
        }

        return view('admin.chronological-reports.index', compact(
            'users',
            'sites',
            'reports',
            'selectedUser',
            'selectedSite',
            'startDate',
            'endDate',
            'startTime',
            'endTime',
            'hasSearched'
        ));
    }

    public function exportPdf(Request $request)
    {
        $selectedUser = $request->get('user_id');
        $selectedSite = $request->get('site_id');
        $startDate = $request->get('start_date') ?: Carbon::today()->format('Y-m-d');
        $endDate = $request->get('end_date') ?: Carbon::today()->format('Y-m-d');
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');

        $reports = $this->getChronologicalData($selectedUser, $selectedSite, $startDate, $endDate, $startTime, $endTime);

        $userObj = $selectedUser ? User::find($selectedUser) : null;
        $siteObj = $selectedSite ? Site::find($selectedSite) : null;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.chronological-reports.pdf', compact(
            'reports',
            'userObj',
            'siteObj',
            'startDate',
            'endDate',
            'startTime',
            'endTime'
        ))->setPaper('a4', 'landscape');

        $filename = 'chronological-report-' . Carbon::parse($startDate)->format('Ymd') . '-' . Carbon::parse($endDate)->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    private function getChronologicalData($userId, $siteId, $startDate, $endDate, $startTime, $endTime)
    {
        $merged = collect();

        // 1. Site Tour Items (Structured Tours)
        $tourQuery = \App\Models\SiteTourItem::with(['siteTour', 'scans.nfcTag', 'scans.user', 'user', 'site'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($userId) {
            $tourQuery->where('user_id', $userId);
        }
        if ($siteId) {
            $tourQuery->where('site_id', $siteId);
        }
        if ($startTime) {
            $tourQuery->whereTime('start_time', '>=', $startTime);
        }
        if ($endTime) {
            $tourQuery->whereTime('end_time', '<=', $endTime);
        }

        $tourItems = $tourQuery->get();
        foreach ($tourItems as $item) {
            $siteTags = \App\Models\NfcTag::where('site_id', $item->site_id)->get();
            $tourTagIds = $item->siteTour?->tags;
            $requiredTags = !empty($tourTagIds) && is_array($tourTagIds) 
                ? $siteTags->whereIn('id', $tourTagIds) 
                : $siteTags;

            $requiredTagIds = $requiredTags->pluck('id')->toArray();
            
            $validScans = $item->scans
                ->whereIn('nfc_tag_id', $requiredTagIds)
                ->unique('nfc_tag_id')
                ->sortBy('time');

            $scannedTagIds = $validScans->pluck('nfc_tag_id')->toArray();
            $missingTags = $requiredTags->whereNotIn('id', $scannedTagIds);

            $requiredCount = count($requiredTagIds);
            $scannedCount = count($scannedTagIds);
            $status = $requiredCount > 0 && $scannedCount >= $requiredCount
                ? 'Completed'
                : ($scannedCount > 0 ? 'Partial' : 'Missed');

            $scansList = $validScans->map(function($scan) {
                return [
                    'time' => $scan->time,
                    'name' => $scan->nfcTag?->name ?? 'N/A',
                    'uid' => $scan->nfcTag?->uid ?? 'N/A',
                    'image' => $scan->image,
                    'user' => $scan->user?->name ?? 'N/A',
                ];
            })->values()->all();

            $dateStr = $item->date ? (is_string($item->date) ? $item->date : $item->date->format('Y-m-d')) : 'N/A';

            $merged->push([
                'type' => 'Site Tour',
                'name' => $item->siteTour?->name ?? 'Site Tour',
                'user' => $item->user?->name ?? 'N/A',
                'site' => $item->site?->name ?? 'N/A',
                'date' => $dateStr,
                'start_time' => $item->start_time,
                'end_time' => $item->end_time,
                'required_count' => $requiredCount,
                'scanned_count' => $scannedCount,
                'status' => $status,
                'scans' => $scansList,
                'missing_tags' => $missingTags->pluck('name')->values()->all(),
            ]);
        }

        // 2. RunSheets (Structured Patrols)
        $runsheetQuery = \App\Models\RunSheet::with(['site.nfcTags', 'scans.nfcTag', 'scans.user', 'user'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($userId) {
            $runsheetQuery->where('user_id', $userId);
        }
        if ($siteId) {
            $runsheetQuery->where('site_id', $siteId);
        }
        if ($startTime) {
            $runsheetQuery->whereTime('start_time', '>=', $startTime);
        }
        if ($endTime) {
            $runsheetQuery->whereTime('end_time', '<=', $endTime);
        }

        $runsheets = $runsheetQuery->get();
        foreach ($runsheets as $rs) {
            $requiredTags = $rs->site?->nfcTags ?? collect();
            $requiredTagIds = $requiredTags->pluck('id')->toArray();

            $validScans = $rs->scans
                ->whereIn('nfc_tag_id', $requiredTagIds)
                ->unique('nfc_tag_id')
                ->sortBy('time');

            $scannedTagIds = $validScans->pluck('nfc_tag_id')->toArray();
            $missingTags = $requiredTags->whereNotIn('id', $scannedTagIds);

            $requiredCount = count($requiredTagIds);
            $scannedCount = count($scannedTagIds);
            $status = $requiredCount > 0 && $scannedCount >= $requiredCount
                ? 'Completed'
                : ($scannedCount > 0 ? 'Partial' : 'Missed');

            $scansList = $validScans->map(function($scan) {
                return [
                    'time' => $scan->time,
                    'name' => $scan->nfcTag?->name ?? 'N/A',
                    'uid' => $scan->nfcTag?->uid ?? 'N/A',
                    'image' => null,
                    'user' => $scan->user?->name ?? 'N/A',
                ];
            })->values()->all();

            $dateStr = $rs->date ? (is_string($rs->date) ? $rs->date : $rs->date->format('Y-m-d')) : 'N/A';

            $merged->push([
                'type' => 'Runsheet',
                'name' => $rs->run_sheet_name ?? 'Runsheet Patrol',
                'user' => $rs->user?->name ?? 'N/A',
                'site' => $rs->site?->name ?? 'N/A',
                'date' => $dateStr,
                'start_time' => $rs->start_time,
                'end_time' => $rs->end_time,
                'required_count' => $requiredCount,
                'scanned_count' => $scannedCount,
                'status' => $status,
                'scans' => $scansList,
                'missing_tags' => $missingTags->pluck('name')->values()->all(),
            ]);
        }

        // 3. SiteItems (Structured Generic Checkpoints)
        $siteItemQuery = \App\Models\SiteItem::with(['site.nfcTags', 'scans.nfcTag', 'scans.user', 'user', 'site'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($userId) {
            $siteItemQuery->where('user_id', $userId);
        }
        if ($siteId) {
            $siteItemQuery->where('site_id', $siteId);
        }
        if ($startTime) {
            $siteItemQuery->whereTime('start_time', '>=', $startTime);
        }
        if ($endTime) {
            $siteItemQuery->whereTime('end_time', '<=', $endTime);
        }

        $siteItems = $siteItemQuery->get();
        foreach ($siteItems as $sItem) {
            $requiredTags = $sItem->site?->nfcTags ?? collect();
            $requiredTagIds = $requiredTags->pluck('id')->toArray();

            $validScans = $sItem->scans
                ->whereIn('nfc_tag_id', $requiredTagIds)
                ->unique('nfc_tag_id')
                ->sortBy('time');

            $scannedTagIds = $validScans->pluck('nfc_tag_id')->toArray();
            $missingTags = $requiredTags->whereNotIn('id', $scannedTagIds);

            $requiredCount = count($requiredTagIds);
            $scannedCount = count($scannedTagIds);
            $status = $requiredCount > 0 && $scannedCount >= $requiredCount
                ? 'Completed'
                : ($scannedCount > 0 ? 'Partial' : 'Missed');

            $scansList = $validScans->map(function($scan) {
                return [
                    'time' => $scan->time,
                    'name' => $scan->nfcTag?->name ?? 'N/A',
                    'uid' => $scan->nfcTag?->uid ?? 'N/A',
                    'image' => $scan->image,
                    'user' => $scan->user?->name ?? 'N/A',
                ];
            })->values()->all();

            $dateStr = $sItem->date ? (is_string($sItem->date) ? $sItem->date : $sItem->date->format('Y-m-d')) : 'N/A';

            $merged->push([
                'type' => 'Site Tour Checkpoint',
                'name' => $sItem->type ?? 'Checkpoint Patrol',
                'user' => $sItem->user?->name ?? 'N/A',
                'site' => $sItem->site?->name ?? 'N/A',
                'date' => $dateStr,
                'start_time' => $sItem->start_time,
                'end_time' => $sItem->end_time,
                'required_count' => $requiredCount,
                'scanned_count' => $scannedCount,
                'status' => $status,
                'scans' => $scansList,
                'missing_tags' => $missingTags->pluck('name')->values()->all(),
            ]);
        }

        // Sort chronologically by date and start_time
        return $merged->sortBy(function ($item) {
            return $item['date'] . ' ' . $item['start_time'];
        })->values()->all();
    }
}
