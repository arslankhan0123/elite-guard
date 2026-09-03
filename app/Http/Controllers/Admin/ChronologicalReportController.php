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
        $weeklyRunSheets = \App\Models\WeeklyRunSheet::orderBy('name')->get();

        $selectedUser = $request->get('user_id');
        $selectedSites = $request->get('site_ids') ?: [];
        if (!is_array($selectedSites)) {
            $selectedSites = $selectedSites ? [$selectedSites] : [];
        }
        // Support fallback to site_id if passed
        if ($request->has('site_id') && $request->get('site_id') && empty($selectedSites)) {
            $selectedSites = [$request->get('site_id')];
        }

        $selectedWeeklyRunSheet = $request->get('weekly_run_sheet_id');
        $startDate = $request->get('start_date') ?: Carbon::today()->format('Y-m-d');
        $endDate = $request->get('end_date') ?: Carbon::today()->format('Y-m-d');
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');

        $reports = [];
        $hasSearched = $request->has('start_date');

        if ($hasSearched) {
            $reports = $this->getChronologicalData($selectedUser, $selectedSites, $selectedWeeklyRunSheet, $startDate, $endDate, $startTime, $endTime);
        }

        return view('admin.chronological-reports.index', compact(
            'users',
            'sites',
            'weeklyRunSheets',
            'reports',
            'selectedUser',
            'selectedSites',
            'selectedWeeklyRunSheet',
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
        $selectedSites = $request->get('site_ids') ?: [];
        if (!is_array($selectedSites)) {
            $selectedSites = $selectedSites ? [$selectedSites] : [];
        }
        if ($request->has('site_id') && $request->get('site_id') && empty($selectedSites)) {
            $selectedSites = [$request->get('site_id')];
        }
        $selectedWeeklyRunSheet = $request->get('weekly_run_sheet_id');
        $startDate = $request->get('start_date') ?: Carbon::today()->format('Y-m-d');
        $endDate = $request->get('end_date') ?: Carbon::today()->format('Y-m-d');
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');

        $reports = $this->getChronologicalData($selectedUser, $selectedSites, $selectedWeeklyRunSheet, $startDate, $endDate, $startTime, $endTime);

        $userObj = $selectedUser ? User::find($selectedUser) : null;
        $sitesObj = !empty($selectedSites) ? Site::whereIn('id', $selectedSites)->get() : collect();
        $weeklyRunSheetObj = $selectedWeeklyRunSheet ? \App\Models\WeeklyRunSheet::find($selectedWeeklyRunSheet) : null;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.chronological-reports.pdf', compact(
            'reports',
            'userObj',
            'sitesObj',
            'weeklyRunSheetObj',
            'startDate',
            'endDate',
            'startTime',
            'endTime'
        ))->setPaper('a4', 'landscape');

        $filename = 'chronological-report-' . Carbon::parse($startDate)->format('Ymd') . '-' . Carbon::parse($endDate)->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    private function getChronologicalData($userId, $siteIds, $weeklyRunSheetId, $startDate, $endDate, $startTime, $endTime)
    {
        $merged = collect();
        if (!is_array($siteIds)) {
            $siteIds = $siteIds ? [$siteIds] : [];
        }

        // Determine actual end date (handle same-day wrap around)
        $actualEndDate = $endDate;
        if ($startTime && $endTime && $startDate === $endDate && $startTime > $endTime) {
            $actualEndDate = Carbon::parse($endDate)->addDay()->format('Y-m-d');
        }

        // Define datetime filter bounds
        $filterStartDatetime = $startDate . ' ' . ($startTime ?: '00:00:00');
        $filterEndDatetime = $actualEndDate . ' ' . ($endTime ?: '23:59:59');

        // 1. Site Tour Items (Structured Tours)
        $tourQuery = \App\Models\SiteTourItem::with(['siteTour', 'scans.nfcTag', 'scans.user', 'user', 'site', 'images'])
            ->whereBetween('date', [$startDate, $actualEndDate]);

        if ($userId) {
            $tourQuery->where('user_id', $userId);
        }
        if (!empty($siteIds)) {
            $tourQuery->whereIn('site_id', $siteIds);
        }

        $tourItems = $weeklyRunSheetId ? collect() : $tourQuery->get();

        if ($startTime || $endTime) {
            $tourItems = $tourItems->filter(function ($item) use ($filterStartDatetime, $filterEndDatetime) {
                $itemStart = $item->date ? (is_string($item->date) ? $item->date : $item->date->format('Y-m-d')) : 'N/A';
                $itemTime = $item->start_time ?: '00:00:00';
                $itemStartDatetime = $itemStart . ' ' . $itemTime;
                return $itemStartDatetime >= $filterStartDatetime && $itemStartDatetime <= $filterEndDatetime;
            });
        }

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
            $tourImages = $item->images ? $item->images->pluck('image_path')->filter()->values()->all() : [];

            $merged->push([
                'id' => $item->id,
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
                'images' => $tourImages,
            ]);
        }

        // 2. Weekly RunSheets (Structured Patrols)
        $runsheetTargets = collect();

        // (a) Fetch actual scans from weekly_run_sheet_scans for the date range
        $scanGroupQuery = \App\Models\WeeklyRunSheetScan::with(['weeklyRunSheetEntry.site.nfcTags', 'weeklyRunSheetEntry.runSheet', 'user', 'nfcTag', 'weeklyRunSheet'])
            ->whereBetween('date', [$startDate, $actualEndDate]);

        if ($userId) {
            $scanGroupQuery->where('user_id', $userId);
        }
        if ($weeklyRunSheetId) {
            $scanGroupQuery->where('weekly_run_sheet_id', $weeklyRunSheetId);
        }
        if (!empty($siteIds)) {
            $scanGroupQuery->whereHas('weeklyRunSheetEntry', function ($q) use ($siteIds) {
                $q->whereIn('site_id', $siteIds);
            });
        }

        $scansInRange = $scanGroupQuery->get();

        $scansGrouped = $scansInRange->groupBy(function ($scan) {
            $d = is_string($scan->date) ? $scan->date : $scan->date->format('Y-m-d');
            return $d . '_' . $scan->weekly_run_sheet_entry_id . '_' . $scan->user_id;
        });

        foreach ($scansGrouped as $key => $scansForEntry) {
            $firstScan = $scansForEntry->first();
            $entry = $firstScan->weeklyRunSheetEntry;
            if (!$entry) continue;

            $d = is_string($firstScan->date) ? $firstScan->date : $firstScan->date->format('Y-m-d');
            $runsheetTargets->put($key, [
                'date' => $d,
                'entry' => $entry,
                'user' => $firstScan->user,
                'weekly_run_sheet_id' => $firstScan->weekly_run_sheet_id,
                'scans' => $scansForEntry,
            ]);
        }

        // (b) Also include scheduled shifts of type runsheet (for unscanned scheduled entries)
        $runsheetShiftsQuery = \App\Models\Shift::with(['schedule.user', 'weeklyRunSheet.entries.site.nfcTags'])
            ->where('type', 'runsheet')
            ->whereBetween('date', [$startDate, $actualEndDate]);

        if ($userId) {
            $runsheetShiftsQuery->whereHas('schedule', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }
        if (!empty($siteIds)) {
            $runsheetShiftsQuery->whereHas('weeklyRunSheet.entries', function ($q) use ($siteIds) {
                $q->whereIn('site_id', $siteIds);
            });
        }
        if ($weeklyRunSheetId) {
            $runsheetShiftsQuery->where('weekly_run_sheet_id', $weeklyRunSheetId);
        }

        $runsheetShifts = $runsheetShiftsQuery->get();

        foreach ($runsheetShifts as $shift) {
            $user = $shift->schedule?->user;
            $shiftDate = is_string($shift->date) ? $shift->date : $shift->date->format('Y-m-d');
            $weeklyRunSheet = $shift->weeklyRunSheet;

            if (!$weeklyRunSheet) continue;

            $dayOfWeek = Carbon::parse($shiftDate)->dayOfWeekIso;
            $entries = $weeklyRunSheet->entries->where('day_of_week', $dayOfWeek);

            foreach ($entries as $entry) {
                if (!empty($siteIds) && !in_array($entry->site_id, $siteIds)) {
                    continue;
                }

                $uId = $user?->id ?? 0;
                $key = $shiftDate . '_' . $entry->id . '_' . $uId;

                if (!$runsheetTargets->has($key)) {
                    $existingScans = \App\Models\WeeklyRunSheetScan::with(['nfcTag', 'user'])
                        ->where('weekly_run_sheet_entry_id', $entry->id)
                        ->where('date', $shiftDate)
                        ->when($uId, fn($q) => $q->where('user_id', $uId))
                        ->get();

                    $runsheetTargets->put($key, [
                        'date' => $shiftDate,
                        'entry' => $entry,
                        'user' => $user,
                        'weekly_run_sheet_id' => $shift->weekly_run_sheet_id,
                        'scans' => $existingScans,
                    ]);
                }
            }
        }

        foreach ($runsheetTargets as $target) {
            $entry = $target['entry'];
            $targetDate = $target['date'];
            $user = $target['user'];
            $scansForEntry = $target['scans'];

            $dayOfWeek = Carbon::parse($targetDate)->dayOfWeekIso;

            $entryStart = $entry->start_time ?: ($entry->runSheet?->getDayStartTime($dayOfWeek));
            $entryEnd = $entry->end_time ?: ($entry->runSheet?->getDayEndTime($dayOfWeek));

            $entryStartFormatted = $entryStart ? \Carbon\Carbon::parse($entryStart)->format('H:i:s') : '00:00:00';
            $entryStartDatetime = $targetDate . ' ' . $entryStartFormatted;

            if ($startTime || $endTime) {
                if ($entryStartDatetime < $filterStartDatetime || $entryStartDatetime > $filterEndDatetime) {
                    continue;
                }
            }

            $requiredTags = $entry->site?->nfcTags ?? collect();
            $requiredTagIds = $requiredTags->pluck('id')->toArray();

            $validScans = $scansForEntry
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

            $merged->push([
                'id' => $entry->id,
                'type' => 'Runsheet Tour',
                'name' => $entry->tour_name ?: ($entry->runSheet?->name ?? 'Runsheet Tour'),
                'user' => $user?->name ?? 'N/A',
                'site' => $entry->site?->name ?? 'N/A',
                'date' => $targetDate,
                'start_time' => $entryStart ?: '00:00:00',
                'end_time' => $entryEnd ?: '23:59:59',
                'required_count' => $requiredCount,
                'scanned_count' => $scannedCount,
                'status' => $status,
                'scans' => $scansList,
                'missing_tags' => $missingTags->pluck('name')->values()->all(),
            ]);
        }

        // 3. SiteItems (Structured Generic Checkpoints)
        $siteItemQuery = \App\Models\SiteItem::with(['site.nfcTags', 'scans.nfcTag', 'scans.user', 'user', 'site'])
            ->whereBetween('date', [$startDate, $actualEndDate]);

        if ($userId) {
            $siteItemQuery->where('user_id', $userId);
        }
        if (!empty($siteIds)) {
            $siteItemQuery->whereIn('site_id', $siteIds);
        }

        $siteItems = $weeklyRunSheetId ? collect() : $siteItemQuery->get();

        if ($startTime || $endTime) {
            $siteItems = $siteItems->filter(function ($sItem) use ($filterStartDatetime, $filterEndDatetime) {
                $itemStart = $sItem->date ? (is_string($sItem->date) ? $sItem->date : $sItem->date->format('Y-m-d')) : 'N/A';
                $itemTime = $sItem->start_time ?: '00:00:00';
                $itemStartDatetime = $itemStart . ' ' . $itemTime;
                return $itemStartDatetime >= $filterStartDatetime && $itemStartDatetime <= $filterEndDatetime;
            });
        }

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
                'id' => $sItem->id,
                'type' => 'Site Checkpoints Tour',
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

        // Sort chronologically by date and start_time descending (latest records first)
        return $merged->sortByDesc(function ($item) {
            return $item['date'] . ' ' . $item['start_time'];
        })->values()->all();
    }

    public function destroy(Request $request)
    {
        try {
            if ($request->filled('items')) {
                $items = is_array($request->items) ? $request->items : json_decode($request->items, true);
                if (is_array($items) && count($items) > 0) {
                    foreach ($items as $item) {
                        $this->deleteOneTourItem($item['type'] ?? '', $item['id'] ?? 0, $item['date'] ?? null);
                    }
                    return redirect()->back()->with('success', count($items) . ' tour item(s) deleted successfully.');
                }
            }

            $request->validate([
                'type' => 'required|string',
                'id' => 'required',
                'date' => 'nullable|date',
            ]);

            $this->deleteOneTourItem($request->get('type'), $request->get('id'), $request->get('date'));
            return redirect()->back()->with('success', 'Tour data deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete tour data: ' . $e->getMessage());
        }
    }

    private function deleteOneTourItem($type, $id, $date)
    {
        if ($type === 'Site Tour') {
            $item = \App\Models\SiteTourItem::find($id);
            if ($item) {
                foreach ($item->scans as $scan) {
                    $this->deletePhysicalImage($scan->image);
                    $scan->delete();
                }
                foreach ($item->images as $tourImg) {
                    $this->deletePhysicalImage($tourImg->image_path);
                    $tourImg->delete();
                }
                $item->delete();
            }
        } elseif ($type === 'Runsheet Tour') {
            $scans = \App\Models\WeeklyRunSheetScan::where('weekly_run_sheet_entry_id', $id)
                ->when($date, fn($q) => $q->whereDate('date', $date))
                ->get();
            foreach ($scans as $scan) {
                $this->deletePhysicalImage($scan->image);
                $scan->delete();
            }
        } elseif ($type === 'Site Checkpoints Tour') {
            $item = \App\Models\SiteItem::find($id);
            if ($item) {
                foreach ($item->scans as $scan) {
                    $this->deletePhysicalImage($scan->image);
                    $scan->delete();
                }
                $item->delete();
            }
        }
    }

    private function deletePhysicalImage($image)
    {
        if (empty($image)) return;
        $path = parse_url($image, PHP_URL_PATH);
        if ($path) {
            $pos = strpos($path, 'storage/');
            if ($pos !== false) {
                $relativePath = substr($path, $pos + 8);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($relativePath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($relativePath);
                }
            }
        }
    }
}
