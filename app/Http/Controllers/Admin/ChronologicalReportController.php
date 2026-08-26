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
        $selectedSite = $request->get('site_id');
        $selectedWeeklyRunSheet = $request->get('weekly_run_sheet_id');
        $startDate = $request->get('start_date') ?: Carbon::today()->format('Y-m-d');
        $endDate = $request->get('end_date') ?: Carbon::today()->format('Y-m-d');
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');

        $reports = [];
        $hasSearched = $request->has('start_date');

        if ($hasSearched) {
            $reports = $this->getChronologicalData($selectedUser, $selectedSite, $selectedWeeklyRunSheet, $startDate, $endDate, $startTime, $endTime);
        }

        return view('admin.chronological-reports.index', compact(
            'users',
            'sites',
            'weeklyRunSheets',
            'reports',
            'selectedUser',
            'selectedSite',
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
        $selectedSite = $request->get('site_id');
        $selectedWeeklyRunSheet = $request->get('weekly_run_sheet_id');
        $startDate = $request->get('start_date') ?: Carbon::today()->format('Y-m-d');
        $endDate = $request->get('end_date') ?: Carbon::today()->format('Y-m-d');
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');

        $reports = $this->getChronologicalData($selectedUser, $selectedSite, $selectedWeeklyRunSheet, $startDate, $endDate, $startTime, $endTime);

        $userObj = $selectedUser ? User::find($selectedUser) : null;
        $siteObj = $selectedSite ? Site::find($selectedSite) : null;
        $weeklyRunSheetObj = $selectedWeeklyRunSheet ? \App\Models\WeeklyRunSheet::find($selectedWeeklyRunSheet) : null;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.chronological-reports.pdf', compact(
            'reports',
            'userObj',
            'siteObj',
            'weeklyRunSheetObj',
            'startDate',
            'endDate',
            'startTime',
            'endTime'
        ))->setPaper('a4', 'landscape');

        $filename = 'chronological-report-' . Carbon::parse($startDate)->format('Ymd') . '-' . Carbon::parse($endDate)->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    private function getChronologicalData($userId, $siteId, $weeklyRunSheetId, $startDate, $endDate, $startTime, $endTime)
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
        if ($startTime && $endTime) {
            if ($startTime > $endTime) {
                $tourQuery->where(function($q) use ($startTime, $endTime) {
                    $q->whereTime('start_time', '>=', $startTime)
                      ->orWhereTime('start_time', '<=', $endTime);
                });
            } else {
                $tourQuery->whereTime('start_time', '>=', $startTime)
                          ->whereTime('start_time', '<=', $endTime);
            }
        } elseif ($startTime) {
            $tourQuery->whereTime('start_time', '>=', $startTime);
        } elseif ($endTime) {
            $tourQuery->whereTime('start_time', '<=', $endTime);
        }

        $tourItems = $weeklyRunSheetId ? collect() : $tourQuery->get();
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

        // 2. Weekly RunSheets (Structured Patrols via Shifts of type runsheet)
        $runsheetShiftsQuery = \App\Models\Shift::with(['schedule.user', 'weeklyRunSheet'])
            ->where('type', 'runsheet')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($userId) {
            $runsheetShiftsQuery->whereHas('schedule', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }
        if ($siteId) {
            $runsheetShiftsQuery->whereHas('weeklyRunSheet.entries', function ($q) use ($siteId) {
                $q->where('site_id', $siteId);
            });
        }
        if ($weeklyRunSheetId) {
            $runsheetShiftsQuery->where('weekly_run_sheet_id', $weeklyRunSheetId);
        }

        $runsheetShifts = $runsheetShiftsQuery->get();
        foreach ($runsheetShifts as $shift) {
            $user = $shift->schedule?->user;
            $dateStrForScans = \Carbon\Carbon::parse($shift->date)->format('Y-m-d');
            $weeklyRunSheet = \App\Models\WeeklyRunSheet::with(['entries.site.nfcTags', 'entries.scans' => function($q) use ($dateStrForScans, $userId) {
                $q->where('date', $dateStrForScans);
                if ($userId) {
                    $q->where('user_id', $userId);
                }
            }])->find($shift->weekly_run_sheet_id);

            if (!$weeklyRunSheet) {
                continue;
            }

            $dayOfWeek = Carbon::parse($shift->date)->dayOfWeekIso;

            // Filter entries for the specific day of the week
            $entries = $weeklyRunSheet->entries->where('day_of_week', $dayOfWeek);

            foreach ($entries as $entry) {
                // If filter by site is set, filter entries by site_id
                if ($siteId && $entry->site_id != $siteId) {
                    continue;
                }

                $entryStart = $entry->start_time ?: $weeklyRunSheet->getDayStartTime($dayOfWeek);
                $entryEnd = $entry->end_time ?: $weeklyRunSheet->getDayEndTime($dayOfWeek);

                $entryStartFormatted = $entryStart ? \Carbon\Carbon::parse($entryStart)->format('H:i:s') : null;
                $startTimeFormatted = $startTime ? \Carbon\Carbon::parse($startTime)->format('H:i:s') : null;
                $endTimeFormatted = $endTime ? \Carbon\Carbon::parse($endTime)->format('H:i:s') : null;

                $isWithinTime = true;
                if ($startTimeFormatted && $endTimeFormatted) {
                    if ($startTimeFormatted > $endTimeFormatted) {
                        if (!($entryStartFormatted >= $startTimeFormatted || $entryStartFormatted <= $endTimeFormatted)) {
                            $isWithinTime = false;
                        }
                    } else {
                        if (!($entryStartFormatted >= $startTimeFormatted && $entryStartFormatted <= $endTimeFormatted)) {
                            $isWithinTime = false;
                        }
                    }
                } elseif ($startTimeFormatted) {
                    if ($entryStartFormatted < $startTimeFormatted) {
                        $isWithinTime = false;
                    }
                } elseif ($endTimeFormatted) {
                    if ($entryStartFormatted > $endTimeFormatted) {
                        $isWithinTime = false;
                    }
                }

                if (!$isWithinTime) {
                    continue;
                }

                $requiredTags = $entry->site?->nfcTags ?? collect();
                $requiredTagIds = $requiredTags->pluck('id')->toArray();

                // Get scans for this entry on this date
                $scans = $entry->scans;
                $validScans = $scans
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
                        'image' => $scan->image, // Runsheet scans have images
                        'user' => $scan->user?->name ?? 'N/A',
                    ];
                })->values()->all();

                $merged->push([
                    'type' => 'Runsheet Tour',
                    'name' => $entry->tour_name ?: ($weeklyRunSheet->name ?? 'Runsheet Tour'),
                    'user' => $user?->name ?? 'N/A',
                    'site' => $entry->site?->name ?? 'N/A',
                    'date' => $shift->date,
                    'start_time' => $entryStart ?: '00:00:00',
                    'end_time' => $entryEnd ?: '23:59:59',
                    'required_count' => $requiredCount,
                    'scanned_count' => $scannedCount,
                    'status' => $status,
                    'scans' => $scansList,
                    'missing_tags' => $missingTags->pluck('name')->values()->all(),
                ]);
            }
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
        if ($startTime && $endTime) {
            if ($startTime > $endTime) {
                $siteItemQuery->where(function($q) use ($startTime, $endTime) {
                    $q->whereTime('start_time', '>=', $startTime)
                      ->orWhereTime('start_time', '<=', $endTime);
                });
            } else {
                $siteItemQuery->whereTime('start_time', '>=', $startTime)
                              ->whereTime('start_time', '<=', $endTime);
            }
        } elseif ($startTime) {
            $siteItemQuery->whereTime('start_time', '>=', $startTime);
        } elseif ($endTime) {
            $siteItemQuery->whereTime('start_time', '<=', $endTime);
        }

        $siteItems = $weeklyRunSheetId ? collect() : $siteItemQuery->get();
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
}
