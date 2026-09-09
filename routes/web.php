<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DegreeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NfcTagController;
use App\Http\Controllers\OrientationController;
use App\Http\Controllers\PaySlipController;
use App\Http\Controllers\TaxDocumentController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\OpenShiftController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TaxDocController;
use App\Http\Controllers\TimeClockController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\NoticeBoardController;
use App\Http\Controllers\PostEscController;
use App\Http\Controllers\WeeklyRunSheetController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\ProductController;
use App\Models\Company;
use App\Models\Employee;
use App\Models\NfcTag;
use App\Models\Site;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-conditions', [FrontendController::class, 'termsConditions'])->name('terms-conditions');
Route::get('/security-protocol', [FrontendController::class, 'securityProtocol'])->name('security-protocol');
Route::get('/career-portal', [FrontendController::class, 'careerPortal'])->name('career-portal');
Route::get('/operational-faq', [FrontendController::class, 'operationalFaq'])->name('operational-faq');
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/services', [FrontendController::class, 'services'])->name('services');
Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');

Route::get('/architecture', function () {
    return view('architecture');
})->name('architecture');

Route::middleware(['auth', 'verified', 'superadmin'])->group(function () {
    Route::get('/dashboard', function () {
        $companyCount = \App\Models\Company::count();
        $siteCount = \App\Models\Site::count();
        $nfcCount = \App\Models\NfcTag::count();
        $employeeCount = \App\Models\Employee::count();
        $pendingOpenShiftClaimsCount = \App\Models\OpenShiftClaim::where('status', 'pending')->count();
        $pendingAvailCount = \App\Models\Availability::where('status', 'pending')->count();
        $todayAttendanceCount = \App\Models\ShiftAttendance::whereDate('clock_in_at', \Carbon\Carbon::today())->count();

        // System Health extra counts
        $dispatchCount = \App\Models\Dispatch::count();
        $openShiftCount = \App\Models\OpenShift::count();
        $availabilityCount = \App\Models\Availability::count();
        $runsheetCount = \App\Models\WeeklyRunSheet::count();
        $policyCount = \App\Models\Policy::count();
        $orientationCount = \App\Models\Orientation::count();
        $noticeBoardCount = \App\Models\NoticeBoard::count();
        $postEscCount = \App\Models\PostEsc::count();

        return view('dashboard', compact(
            'companyCount', 'siteCount', 'nfcCount', 'employeeCount', 
            'pendingOpenShiftClaimsCount', 'pendingAvailCount', 'todayAttendanceCount',
            'dispatchCount', 'openShiftCount', 'availabilityCount', 'runsheetCount',
            'policyCount', 'orientationCount', 'noticeBoardCount', 'postEscCount'
        ));
    })->name('dashboard');

    Route::get('/dashboard/live-data', function () {
        $attendances = \App\Models\ShiftAttendance::with(['user', 'shift.site'])
            ->orderByRaw('COALESCE(clock_out_at, clock_in_at) DESC')
            ->limit(8)
            ->get()
            ->map(function ($att) {
                return [
                    'user_name' => $att->user?->name ?? 'N/A',
                    'site_name' => $att->shift?->site?->name ?? 'N/A',
                    'type' => $att->clock_out_at ? 'Checked Out' : 'Checked In',
                    'time' => $att->clock_out_at 
                        ? \Carbon\Carbon::parse($att->clock_out_at)->format('g:i A') 
                        : \Carbon\Carbon::parse($att->clock_in_at)->format('g:i A'),
                    'date' => \Carbon\Carbon::parse($att->clock_in_at)->format('d-M'),
                ];
            });

        $startDate = \Carbon\Carbon::now()->subDays(7)->format('Y-m-d');
        $endDate = \Carbon\Carbon::now()->format('Y-m-d');

        $merged = collect();

        // 1. Site Tour Items (with scans recorded)
        $tourItems = \App\Models\SiteTourItem::with(['siteTour', 'scans.nfcTag', 'user', 'site'])
            ->whereHas('scans')
            ->get();

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
            $requiredCount = count($requiredTagIds);
            $scannedCount = count($scannedTagIds);

            if ($scannedCount === 0) {
                continue;
            }
            
            $lastScan = $validScans->last();
            $latestScanTime = ($lastScan && $lastScan->created_at)
                ? \Carbon\Carbon::parse($lastScan->created_at)->format('Y-m-d H:i:s')
                : ($lastScan ? $lastScan->date . ' ' . $lastScan->time : null);

            $status = $requiredCount > 0 && $scannedCount >= $requiredCount
                ? 'Completed'
                : 'Partial';

            $merged->push([
                'tour_type' => 'Site Tour',
                'tour_name' => $item->siteTour?->name ?? 'Site Tour',
                'site_name' => $item->site?->name ?? 'N/A',
                'user_name' => $item->user?->name ?? 'N/A',
                'progress' => "{$scannedCount}/{$requiredCount}",
                'scanned_count' => $scannedCount,
                'required_count' => $requiredCount,
                'status' => $status,
                'latest_scan_time' => $latestScanTime,
                'scheduled_time' => $item->date . ' ' . ($item->start_time ?: '00:00:00'),
            ]);
        }

        // 2a. Daily Runsheets (RunSheet model & run_sheet_scans table, with scans)
        $dailyRunSheets = \App\Models\RunSheet::with(['site.nfcTags', 'scans', 'user'])
            ->whereHas('scans')
            ->get();

        $processedRunSheetShiftIds = [];

        foreach ($dailyRunSheets as $rs) {
            if ($rs->shift_id && $rs->site_id) {
                $processedRunSheetShiftIds[] = $rs->shift_id . '_' . $rs->site_id;
            }

            $requiredTags = $rs->site?->nfcTags ?? collect();
            $requiredTagIds = $requiredTags->pluck('id')->toArray();

            $validScans = $rs->scans
                ->whereIn('nfc_tag_id', $requiredTagIds)
                ->unique('nfc_tag_id')
                ->sortBy('time');

            $scannedTagIds = $validScans->pluck('nfc_tag_id')->toArray();
            $requiredCount = count($requiredTagIds);
            $scannedCount = count($scannedTagIds);

            if ($scannedCount === 0) {
                continue;
            }

            $lastScan = $validScans->last();
            $latestScanTime = ($lastScan && $lastScan->created_at)
                ? \Carbon\Carbon::parse($lastScan->created_at)->format('Y-m-d H:i:s')
                : ($lastScan ? $lastScan->date . ' ' . $lastScan->time : null);

            $status = $requiredCount > 0 && $scannedCount >= $requiredCount
                ? 'Completed'
                : 'Partial';

            $merged->push([
                'tour_type' => 'Runsheet Tour',
                'tour_name' => $rs->run_sheet_name ?: 'Runsheet Tour',
                'site_name' => $rs->site?->name ?? 'N/A',
                'user_name' => $rs->user?->name ?? 'N/A',
                'progress' => "{$scannedCount}/{$requiredCount}",
                'scanned_count' => $scannedCount,
                'required_count' => $requiredCount,
                'status' => $status,
                'latest_scan_time' => $latestScanTime,
                'scheduled_time' => $rs->date . ' ' . ($rs->start_time ?: '00:00:00'),
            ]);
        }

        // 2b. Weekly Runsheet Shifts (WeeklyRunSheet model & weekly_run_sheet_scans table, with scans)
        $runsheetShifts = \App\Models\Shift::with(['schedule.user', 'weeklyRunSheet'])
            ->where('type', 'runsheet')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        foreach ($runsheetShifts as $shift) {
            $user = $shift->schedule?->user;
            $dateStrForScans = \Carbon\Carbon::parse($shift->date)->format('Y-m-d');
            $weeklyRunSheet = \App\Models\WeeklyRunSheet::with(['entries.site.nfcTags', 'entries.scans' => function($q) use ($dateStrForScans) {
                $q->where('date', $dateStrForScans);
            }])->find($shift->weekly_run_sheet_id);

            if (!$weeklyRunSheet) continue;

            $dayOfWeek = \Carbon\Carbon::parse($shift->date)->dayOfWeekIso;
            $entries = $weeklyRunSheet->entries->where('day_of_week', $dayOfWeek);

            foreach ($entries as $entry) {
                $key = $shift->id . '_' . $entry->site_id;
                if (in_array($key, $processedRunSheetShiftIds)) {
                    continue;
                }

                $entryStart = $entry->start_time ?: $weeklyRunSheet->getDayStartTime($dayOfWeek);
                $entryEnd = $entry->end_time ?: $weeklyRunSheet->getDayEndTime($dayOfWeek);

                $requiredTags = $entry->site?->nfcTags ?? collect();
                $requiredTagIds = $requiredTags->pluck('id')->toArray();

                $scans = $entry->scans;
                $validScans = $scans
                    ->whereIn('nfc_tag_id', $requiredTagIds)
                    ->unique('nfc_tag_id')
                    ->sortBy('time');

                $scannedTagIds = $validScans->pluck('nfc_tag_id')->toArray();
                $requiredCount = count($requiredTagIds);
                $scannedCount = count($scannedTagIds);

                if ($scannedCount === 0) {
                    continue;
                }

                $lastScan = $validScans->last();
                $latestScanTime = ($lastScan && $lastScan->created_at)
                    ? \Carbon\Carbon::parse($lastScan->created_at)->format('Y-m-d H:i:s')
                    : ($lastScan ? $lastScan->date . ' ' . $lastScan->time : null);

                $status = $requiredCount > 0 && $scannedCount >= $requiredCount
                    ? 'Completed'
                    : 'Partial';

                $merged->push([
                    'tour_type' => 'Runsheet Tour',
                    'tour_name' => $entry->tour_name ?: ($weeklyRunSheet->name ?? 'Runsheet Tour'),
                    'site_name' => $entry->site?->name ?? 'N/A',
                    'user_name' => $user?->name ?? 'N/A',
                    'progress' => "{$scannedCount}/{$requiredCount}",
                    'scanned_count' => $scannedCount,
                    'required_count' => $requiredCount,
                    'status' => $status,
                    'latest_scan_time' => $latestScanTime,
                    'scheduled_time' => $shift->date . ' ' . ($entryStart ?: '00:00:00'),
                ]);
            }
        }

        // 3. Site Items Checkpoints (with scans)
        $siteItems = \App\Models\SiteItem::with(['site.nfcTags', 'scans.nfcTag', 'user', 'site'])
            ->whereHas('scans')
            ->get();

        foreach ($siteItems as $sItem) {
            $requiredTags = $sItem->site?->nfcTags ?? collect();
            $requiredTagIds = $requiredTags->pluck('id')->toArray();

            $validScans = $sItem->scans
                ->whereIn('nfc_tag_id', $requiredTagIds)
                ->unique('nfc_tag_id')
                ->sortBy('time');

            $scannedTagIds = $validScans->pluck('nfc_tag_id')->toArray();
            $requiredCount = count($requiredTagIds);
            $scannedCount = count($scannedTagIds);

            if ($scannedCount === 0) {
                continue;
            }

            $lastScan = $validScans->last();
            $latestScanTime = ($lastScan && $lastScan->created_at)
                ? \Carbon\Carbon::parse($lastScan->created_at)->format('Y-m-d H:i:s')
                : ($lastScan ? $lastScan->date . ' ' . $lastScan->time : null);

            $status = $requiredCount > 0 && $scannedCount >= $requiredCount
                ? 'Completed'
                : 'Partial';

            $merged->push([
                'tour_type' => 'Site Checkpoint Tour',
                'tour_name' => $sItem->type ?? 'Checkpoint Patrol',
                'site_name' => $sItem->site?->name ?? 'N/A',
                'user_name' => $sItem->user?->name ?? 'N/A',
                'progress' => "{$scannedCount}/{$requiredCount}",
                'scanned_count' => $scannedCount,
                'required_count' => $requiredCount,
                'status' => $status,
                'latest_scan_time' => $latestScanTime,
                'scheduled_time' => $sItem->date . ' ' . ($sItem->start_time ?: '00:00:00'),
            ]);
        }

        $sorted = $merged->sort(function($a, $b) {
            $aScan = $a['latest_scan_time'];
            $bScan = $b['latest_scan_time'];

            if ($aScan && $bScan) {
                return strcmp($bScan, $aScan);
            }
            if ($aScan) return -1;
            if ($bScan) return 1;

            return strcmp($b['scheduled_time'], $a['scheduled_time']);
        })->values();

        $tours = $sorted->take(5);

        // Fetch Recent Reports (5 recent)
        $recentReports = collect();

        foreach (\App\Models\ReportIncidentForm::with('user', 'site')->latest()->take(5)->get() as $inc) {
            $recentReports->push([
                'id' => $inc->id,
                'type_key' => 'incident',
                'type_name' => 'Incident Report',
                'badge_class' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'user_name' => $inc->user?->name ?? 'N/A',
                'site_name' => $inc->site?->name ?? 'N/A',
                'date' => $inc->created_at ? $inc->created_at->format('d-M g:i A') : 'N/A',
                'created_at' => $inc->created_at,
            ]);
        }

        foreach (\App\Models\ReportSecurityGuardDisciplinaryForm::with('user', 'site')->latest()->take(5)->get() as $disc) {
            $recentReports->push([
                'id' => $disc->id,
                'type_key' => 'disciplinary',
                'type_name' => 'Disciplinary Report',
                'badge_class' => 'bg-warning-subtle text-warning border border-warning-subtle',
                'user_name' => $disc->user?->name ?? 'N/A',
                'site_name' => $disc->site?->name ?? 'N/A',
                'date' => $disc->created_at ? $disc->created_at->format('d-M g:i A') : 'N/A',
                'created_at' => $disc->created_at,
            ]);
        }

        foreach (\App\Models\ReportGeneralForm::with('user', 'site')->latest()->take(5)->get() as $gen) {
            $recentReports->push([
                'id' => $gen->id,
                'type_key' => 'general',
                'type_name' => 'General Report',
                'badge_class' => 'bg-primary-subtle text-primary border border-primary-subtle',
                'user_name' => $gen->user?->name ?? 'N/A',
                'site_name' => $gen->site?->name ?? 'N/A',
                'date' => $gen->created_at ? $gen->created_at->format('d-M g:i A') : 'N/A',
                'created_at' => $gen->created_at,
            ]);
        }

        foreach (\App\Models\ReportDailyShiftForm::with(['user', 'shift.site'])->latest()->take(5)->get() as $ds) {
            $recentReports->push([
                'id' => $ds->id,
                'type_key' => 'daily-shift',
                'type_name' => 'Daily Shift Report',
                'badge_class' => 'bg-info-subtle text-info border border-info-subtle',
                'user_name' => $ds->user?->name ?? 'N/A',
                'site_name' => $ds->shift?->site?->name ?? 'N/A',
                'date' => $ds->created_at ? $ds->created_at->format('d-M g:i A') : 'N/A',
                'created_at' => $ds->created_at,
            ]);
        }

        foreach (\App\Models\FireWatchReport::with('user', 'site')->latest()->take(5)->get() as $fw) {
            $recentReports->push([
                'id' => $fw->id,
                'type_key' => 'fire-watch',
                'type_name' => 'Fire Watch Report',
                'badge_class' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'user_name' => $fw->user?->name ?? 'N/A',
                'site_name' => $fw->site?->name ?? 'N/A',
                'date' => $fw->created_at ? $fw->created_at->format('d-M g:i A') : 'N/A',
                'created_at' => $fw->created_at,
            ]);
        }

        $reports = $recentReports->sortByDesc('created_at')->take(5)->values();

        // Fetch Recent Forms (5 recent)
        $recentForms = collect();

        foreach (\App\Models\DailyVehicleChecklist::with('user', 'site')->latest()->take(5)->get() as $chk) {
            $recentForms->push([
                'id' => $chk->id,
                'type_key' => 'vehicle-checklist',
                'type_name' => 'Vehicle Checklist',
                'badge_class' => 'bg-success-subtle text-success border border-success-subtle',
                'user_name' => $chk->user?->name ?? 'N/A',
                'site_name' => $chk->site?->name ?? 'N/A',
                'date' => $chk->created_at ? $chk->created_at->format('d-M g:i A') : 'N/A',
                'created_at' => $chk->created_at,
            ]);
        }

        foreach (\App\Models\Assessment::with('user')->latest()->take(5)->get() as $ass) {
            $recentForms->push([
                'id' => $ass->id,
                'type_key' => 'assessments',
                'type_name' => 'Guard Assessment',
                'badge_class' => 'bg-primary-subtle text-primary border border-primary-subtle',
                'user_name' => $ass->user?->name ?? 'N/A',
                'site_name' => 'N/A',
                'date' => $ass->created_at ? $ass->created_at->format('d-M g:i A') : 'N/A',
                'created_at' => $ass->created_at,
            ]);
        }

        foreach (\App\Models\ShiftAdjustmentForm::with('user')->latest()->take(5)->get() as $adj) {
            $recentForms->push([
                'id' => $adj->id,
                'type_key' => 'shift-adjustment',
                'type_name' => 'Shift Adjustment',
                'badge_class' => 'bg-warning-subtle text-warning border border-warning-subtle',
                'user_name' => $adj->user?->name ?? 'N/A',
                'site_name' => 'N/A',
                'date' => $adj->created_at ? $adj->created_at->format('d-M g:i A') : 'N/A',
                'created_at' => $adj->created_at,
            ]);
        }

        $forms = $recentForms->sortByDesc('created_at')->take(5)->values();

        // Compute Shift-based Tour Progress Stats (Site Tours & Runsheet Tours)
        $todayStr = \Carbon\Carbon::now()->format('Y-m-d');
        $targetDate = request('date') ? \Carbon\Carbon::parse(request('date'))->format('Y-m-d') : $todayStr;
        $isToday = ($targetDate === $todayStr);

        $totalSiteTourItems = 0;
        $scannedSiteTourItems = 0;
        $totalRunsheetEntries = 0;
        $scannedRunsheetEntries = 0;

        $stRepo = new \App\Repositories\SiteTourItemRepository();
        $rsRepo = new \App\Repositories\RunSheetRepository();

        if ($isToday) {
            // Check currently active checked-in shifts across guards
            $activeAttendances = \App\Models\ShiftAttendance::whereNull('clock_out_at')->with(['shift', 'user'])->get();

            if ($activeAttendances->isNotEmpty()) {
                foreach ($activeAttendances as $att) {
                    if ($att->user && $att->shift) {
                        $stRes = $stRepo->getUserSiteTourItems($att->user, $att->shift->date, $att->shift->date, $att->shift->id);
                        if (isset($stRes['total_site_tour_items'])) {
                            $totalSiteTourItems += $stRes['total_site_tour_items'];
                            $scannedSiteTourItems += $stRes['scanned_site_tour_items'];
                        }

                        $rsRes = $rsRepo->getUserRunSheets($att->user, $att->shift->date, $att->shift->id);
                        if (isset($rsRes['total_entries'])) {
                            $totalRunsheetEntries += $rsRes['total_entries'];
                            $scannedRunsheetEntries += $rsRes['scanned_entries'];
                        }
                    }
                }
            }
        } else {
            // For specified date, fetch shifts scheduled on that date
            $shiftsOnDate = \App\Models\Shift::where('date', $targetDate)->with(['schedule.user'])->get();
            foreach ($shiftsOnDate as $s) {
                $user = $s->schedule?->user;
                if ($user) {
                    $stRes = $stRepo->getUserSiteTourItems($user, $s->date, $s->date, $s->id);
                    if (isset($stRes['total_site_tour_items'])) {
                        $totalSiteTourItems += $stRes['total_site_tour_items'];
                        $scannedSiteTourItems += $stRes['scanned_site_tour_items'];
                    }

                    $rsRes = $rsRepo->getUserRunSheets($user, $s->date, $s->id);
                    if (isset($rsRes['total_entries'])) {
                        $totalRunsheetEntries += $rsRes['total_entries'];
                        $scannedRunsheetEntries += $rsRes['scanned_entries'];
                    }
                }
            }
        }

        // Fallback to direct SiteTourItem and RunSheet records on targetDate if count is 0
        if ($totalSiteTourItems === 0) {
            $siteTourItemsToday = \App\Models\SiteTourItem::with('scans')
                ->where('date', $targetDate)
                ->get();
            $totalSiteTourItems = $siteTourItemsToday->count();
            $scannedSiteTourItems = $siteTourItemsToday->filter(fn($item) => $item->scans->count() > 0)->count();
        }

        if ($totalRunsheetEntries === 0) {
            $dailyRunSheetsToday = \App\Models\RunSheet::with('scans')
                ->where('date', $targetDate)
                ->get();
            $totalRunsheetEntries = $dailyRunSheetsToday->count();
            $scannedRunsheetEntries = $dailyRunSheetsToday->filter(fn($rs) => $rs->scans->count() > 0)->count();
        }

        return response()->json([
            'attendances' => $attendances,
            'tours'       => $tours,
            'reports'     => $reports,
            'forms'       => $forms,
            'stats'       => [
                'selected_date'       => $targetDate,
                'selected_date_label' => \Carbon\Carbon::parse($targetDate)->format('D, d M Y'),
                'is_today'            => $isToday,
                'site_tours_total'    => $totalSiteTourItems,
                'site_tours_scanned'  => $scannedSiteTourItems,
                'runsheets_total'     => $totalRunsheetEntries,
                'runsheets_scanned'  => $scannedRunsheetEntries,
            ],
        ]);
    })->name('dashboard.live-data');

    Route::group(['prefix' => '/company'], function () {
        Route::get('/', [CompanyController::class, 'index'])->name('companies.index');
        Route::get('/create', [CompanyController::class, 'create'])->name('companies.create');
        Route::post('/store', [CompanyController::class, 'store'])->name('companies.store');
        Route::get('/edit/{company_id}', [CompanyController::class, 'edit'])->name('companies.edit');
        Route::post('/update/{company_id}', [CompanyController::class, 'update'])->name('companies.update');
        Route::get('/delete/{company_id}', [CompanyController::class, 'delete'])->name('companies.delete');
    });

    Route::group(['prefix' => '/sites'], function () {
        Route::get('/', [SiteController::class, 'index'])->name('sites.index');
        Route::get('/create', [SiteController::class, 'create'])->name('sites.create');
        Route::post('/store', [SiteController::class, 'store'])->name('sites.store');
        Route::get('/edit/{site_id}', [SiteController::class, 'edit'])->name('sites.edit');
        Route::post('/update/{site_id}', [SiteController::class, 'update'])->name('sites.update');
        Route::get('/delete/{site_id}', [SiteController::class, 'delete'])->name('sites.delete');
        Route::get('/{site_id}/scan-report', [SiteController::class, 'scanReport'])->name('sites.scan-report');
        Route::get('/{site_id}/scan-report/export/{format}', [SiteController::class, 'exportScanReport'])
            ->where('format', 'pdf|csv')
            ->name('sites.scan-report.export');
        Route::get('/{site_id}/nfc-tags', [SiteController::class, 'nfcTags'])->name('sites.nfcTags');
        Route::get('/tours', [SiteController::class, 'allTours'])->name('sites.tours.all');
        Route::get('/tours/{id}/report/pdf', [SiteController::class, 'tourReportPdf'])->name('sites.tours.report.pdf');
        Route::get('/{site_id}/tours', [SiteController::class, 'tours'])->name('sites.tours');
        Route::post('/tours/store', [SiteController::class, 'storeTour'])->name('sites.tours.store');
        Route::post('/tours/update/{id}', [SiteController::class, 'updateTour'])->name('sites.tours.update');
        Route::get('/tours/delete/{id}', [SiteController::class, 'deleteTour'])->name('sites.tours.delete');
        Route::delete('/tours/delete-week/{site_id}', [SiteController::class, 'deleteWeekTours'])->name('sites.tours.deleteWeek');
    });

    Route::resource('/run-sheets', WeeklyRunSheetController::class)
        ->parameters(['run-sheets' => 'weeklyRunSheet'])
        ->names('weekly-run-sheets');

    Route::group(['prefix' => '/nfc'], function () {
        Route::get('/', [NfcTagController::class, 'index'])->name('nfc.index');
        Route::get('/create', [NfcTagController::class, 'create'])->name('nfc.create');
        Route::post('/store', [NfcTagController::class, 'store'])->name('nfc.store');
        Route::post('/store-ajax', [NfcTagController::class, 'storeAjax'])->name('nfc.storeAjax');
        Route::get('/edit/{nfc_id}', [NfcTagController::class, 'edit'])->name('nfc.edit');
        Route::post('/update/{nfc_id}', [NfcTagController::class, 'update'])->name('nfc.update');
        Route::post('/update-ajax/{nfc_id}', [NfcTagController::class, 'updateAjax'])->name('nfc.updateAjax');
        Route::get('/delete/{nfc_id}', [NfcTagController::class, 'delete'])->name('nfc.delete');
        Route::delete('/delete-ajax/{nfc_id}', [NfcTagController::class, 'deleteAjax'])->name('nfc.deleteAjax');
    });

    Route::group(['prefix' => '/schedules'], function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::post('/store', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::post('/update', [ScheduleController::class, 'update'])->name('schedules.update');
        Route::get('/delete/{id}', [ScheduleController::class, 'destroy'])->name('schedules.delete');
        Route::get('/ajax/{user_id}', [ScheduleController::class, 'getAjaxSchedule'])->name('schedules.ajax');
    });

    Route::post('/employee-run-sheets/update', [\App\Http\Controllers\RunSheetController::class, 'update'])->name('run-sheets.update');

    Route::group(['prefix' => '/open-shifts'], function () {
        Route::get('/', [OpenShiftController::class, 'index'])->name('open-shifts.index');
        Route::get('/create', [OpenShiftController::class, 'create'])->name('open-shifts.create');
        Route::post('/store', [OpenShiftController::class, 'store'])->name('open-shifts.store');
        Route::get('/edit/{id}', [OpenShiftController::class, 'edit'])->name('open-shifts.edit');
        Route::post('/update/{id}', [OpenShiftController::class, 'update'])->name('open-shifts.update');
        Route::get('/delete/{id}', [OpenShiftController::class, 'delete'])->name('open-shifts.delete');
        Route::get('/claims', [OpenShiftController::class, 'claims'])->name('open-shifts.claims');
        Route::post('/claims/{id}/approve', [OpenShiftController::class, 'approveClaim'])->name('open-shifts.approve');
        Route::post('/claims/{id}/reject', [OpenShiftController::class, 'rejectClaim'])->name('open-shifts.reject');
    });

    Route::group(['prefix' => '/availabilities'], function () {
        Route::get('/', [AvailabilityController::class, 'index'])->name('availabilities.index');
        Route::put('/{id}', [AvailabilityController::class, 'update'])->name('availabilities.update');
        Route::delete('/{id}', [AvailabilityController::class, 'destroy'])->name('availabilities.destroy');
    });

    Route::group(['prefix' => '/time-clocks'], function () {
        Route::get('/', [TimeClockController::class, 'index'])->name('time-clocks.index');
    });

    Route::group(['prefix' => '/attendance'], function () {
        Route::get('/', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/export/pdf', [\App\Http\Controllers\AttendanceController::class, 'exportPdf'])->name('attendance.export.pdf');
        Route::post('/export/excel', [\App\Http\Controllers\AttendanceController::class, 'exportExcel'])->name('attendance.export.excel');
        Route::post('/update-adjustment', [\App\Http\Controllers\AttendanceController::class, 'updateAdjustment'])->name('attendance.updateAdjustment');
    });

    Route::group(['prefix' => '/profile'], function () {
        Route::group(['prefix' => '/policies'], function () {
            Route::get('/', [PolicyController::class, 'index'])->name('policies.index');
            Route::get('/create', [PolicyController::class, 'create'])->name('policies.create');
            Route::post('/store', [PolicyController::class, 'store'])->name('policies.store');
            Route::get('/edit/{id}', [PolicyController::class, 'edit'])->name('policies.edit');
            Route::post('/update/{id}', [PolicyController::class, 'update'])->name('policies.update');
            Route::get('/delete/{id}', [PolicyController::class, 'delete'])->name('policies.delete');
            Route::get('/{id}/signed', [PolicyController::class, 'signedPolicies'])->name('policies.signed');
        });

        Route::group(['prefix' => '/orientations'], function () {
            Route::get('/', [OrientationController::class, 'index'])->name('orientations.index');
            Route::get('/create', [OrientationController::class, 'create'])->name('orientations.create');
            Route::post('/store', [OrientationController::class, 'store'])->name('orientations.store');
            Route::get('/edit/{id}', [OrientationController::class, 'edit'])->name('orientations.edit');
            Route::post('/update/{id}', [OrientationController::class, 'update'])->name('orientations.update');
            Route::get('/delete/{id}', [OrientationController::class, 'delete'])->name('orientations.delete');
            Route::get('/{id}/attempts', [OrientationController::class, 'attempts'])->name('orientations.attempts');
            Route::get('/{id}/attempts/{attempt_id}', [OrientationController::class, 'showAttempt'])->name('orientations.showAttempt');
        });

        Route::group(['prefix' => '/employee'], function () {
            Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
            Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
            Route::post('/store', [EmployeeController::class, 'store'])->name('employees.store');
            Route::get('/edit/{id}', [EmployeeController::class, 'edit'])->name('employees.edit');
            Route::get('/show/{id}', [EmployeeController::class, 'show'])->name('employees.show');
            Route::post('/update/{id}', [EmployeeController::class, 'update'])->name('employees.update');
            Route::get('/delete/{id}', [EmployeeController::class, 'delete'])->name('employees.delete');
            Route::post('/assign-sites/{user_id}', [EmployeeController::class, 'assignSites'])->name('employees.assignSites');
            Route::post('/assign-weekly-run-sheets/{user_id}', [EmployeeController::class, 'assignWeeklyRunSheets'])->name('employees.assignWeeklyRunSheets');
            Route::post('/update-offer-letter', [EmployeeController::class, 'updateOfferLetter'])->name('employees.updateOfferLetter');
            Route::post('/update-pay-slip', [EmployeeController::class, 'updatePaySlip'])->name('employees.updatePaySlip');
            Route::get('/check-pay-slip', [EmployeeController::class, 'checkPaySlip'])->name('employees.checkPaySlip');
            Route::post('/{id}/update-profile-picture', [EmployeeController::class, 'updateProfilePicture'])->name('employees.updateProfilePicture');
            Route::post('/{id}/delete-profile-picture', [EmployeeController::class, 'deleteProfilePicture'])->name('employees.deleteProfilePicture');
            Route::group(['prefix' => '/pay-slips'], function () {
                Route::get('/', [PaySlipController::class, 'index'])->name('pay-slips.index');
            });
        });

        Route::group(['prefix' => '/tax-docs'], function () {
            Route::get('/', [TaxDocumentController::class, 'index'])->name('tax-docs.index');
            Route::get('/create', [TaxDocumentController::class, 'create'])->name('tax-docs.create');
            Route::post('/store', [TaxDocumentController::class, 'store'])->name('tax-docs.store');
            Route::get('/edit/{id}', [TaxDocumentController::class, 'edit'])->name('tax-docs.edit');
            Route::post('/update/{id}', [TaxDocumentController::class, 'update'])->name('tax-docs.update');
            Route::get('/delete/{id}', [TaxDocumentController::class, 'delete'])->name('tax-docs.delete');
        });

        Route::group(['prefix' => '/numbers'], function () {
            Route::get('/', [\App\Http\Controllers\NumberController::class, 'index'])->name('numbers.index');
            Route::get('/create', [\App\Http\Controllers\NumberController::class, 'create'])->name('numbers.create');
            Route::post('/store', [\App\Http\Controllers\NumberController::class, 'store'])->name('numbers.store');
            Route::get('/edit/{id}', [\App\Http\Controllers\NumberController::class, 'edit'])->name('numbers.edit');
            Route::post('/update/{id}', [\App\Http\Controllers\NumberController::class, 'update'])->name('numbers.update');
            Route::get('/delete/{id}', [\App\Http\Controllers\NumberController::class, 'delete'])->name('numbers.delete');
        });

        Route::group(['prefix' => '/reports'], function () {
            Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        });

        Route::group(['prefix' => '/forms'], function () {
            Route::get('/assessments', [\App\Http\Controllers\FormsController::class, 'assessments'])->name('forms.assessments');
            Route::get('/daily-vehicle-checklist', [\App\Http\Controllers\FormsController::class, 'dailyVehicleChecklist'])->name('forms.daily-vehicle-checklist');
        });

        Route::group(['prefix' => '/security-reports'], function () {
            // Route::get('/all', [\App\Http\Controllers\Admin\UnifiedReportController::class, 'index'])->name('reports.all');
            Route::get('/disciplinary', [\App\Http\Controllers\Admin\SecurityReportController::class, 'disciplinary'])->name('security-reports.disciplinary');
            Route::get('/incident', [\App\Http\Controllers\Admin\SecurityReportController::class, 'incident'])->name('security-reports.incident');
            Route::get('/general', [\App\Http\Controllers\Admin\SecurityReportController::class, 'general'])->name('security-reports.general');
            Route::get('/daily-shift', [\App\Http\Controllers\Admin\SecurityReportController::class, 'dailyShift'])->name('security-reports.daily-shift');
        });
    });

    Route::group(['prefix' => '/security-reports'], function () {
        Route::get('/all', [\App\Http\Controllers\Admin\UnifiedReportController::class, 'index'])->name('reports.all');
        Route::get('/show/{type}/{id}', [\App\Http\Controllers\Admin\UnifiedReportController::class, 'show'])->name('reports.show');
        Route::get('/edit/{type}/{id}', [\App\Http\Controllers\Admin\UnifiedReportController::class, 'edit'])->name('reports.edit');
        Route::put('/update/{type}/{id}', [\App\Http\Controllers\Admin\UnifiedReportController::class, 'update'])->name('reports.update');
        Route::delete('/delete/{type}/{id}', [\App\Http\Controllers\Admin\UnifiedReportController::class, 'destroy'])->name('reports.destroy');
        Route::get('/download/{type}/{id}', [\App\Http\Controllers\Admin\UnifiedReportController::class, 'downloadPdf'])->name('reports.download');
    });

    Route::group(['prefix' => '/chronological-reports'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\ChronologicalReportController::class, 'index'])->name('chronological-reports.index');
        Route::get('/pdf', [\App\Http\Controllers\Admin\ChronologicalReportController::class, 'exportPdf'])->name('chronological-reports.pdf');
        Route::delete('/delete', [\App\Http\Controllers\Admin\ChronologicalReportController::class, 'destroy'])->name('chronological-reports.destroy');
    });

    Route::group(['prefix' => '/notice-board'], function () {
        Route::get('/', [NoticeBoardController::class, 'index'])->name('notice-board.index');
        Route::get('/create', [NoticeBoardController::class, 'create'])->name('notice-board.create');
        Route::post('/store', [NoticeBoardController::class, 'store'])->name('notice-board.store');
        Route::get('/edit/{id}', [NoticeBoardController::class, 'edit'])->name('notice-board.edit');
        Route::post('/update/{id}', [NoticeBoardController::class, 'update'])->name('notice-board.update');
        Route::get('/delete/{id}', [NoticeBoardController::class, 'destroy'])->name('notice-board.delete');
    });

    Route::group(['prefix' => '/dispatches'], function () {
        Route::get('/', [\App\Http\Controllers\DispatchController::class, 'index'])->name('dispatches.index');
        Route::get('/create', [\App\Http\Controllers\DispatchController::class, 'create'])->name('dispatches.create');
        Route::post('/store', [\App\Http\Controllers\DispatchController::class, 'store'])->name('dispatches.store');
        Route::get('/show/{id}', [\App\Http\Controllers\DispatchController::class, 'show'])->name('dispatches.show');
        Route::get('/edit/{id}', [\App\Http\Controllers\DispatchController::class, 'edit'])->name('dispatches.edit');
        Route::post('/update/{id}', [\App\Http\Controllers\DispatchController::class, 'update'])->name('dispatches.update');
        Route::get('/delete/{id}', [\App\Http\Controllers\DispatchController::class, 'destroy'])->name('dispatches.delete');
    });

    Route::group(['prefix' => '/post-esc'], function () {
        Route::get('/', [PostEscController::class, 'index'])->name('post-esc.index');
        Route::get('/create', [PostEscController::class, 'create'])->name('post-esc.create');
        Route::post('/store', [PostEscController::class, 'store'])->name('post-esc.store');
        Route::get('/edit/{id}', [PostEscController::class, 'edit'])->name('post-esc.edit');
        Route::post('/update/{id}', [PostEscController::class, 'update'])->name('post-esc.update');
        Route::get('/download/{id}', [PostEscController::class, 'download'])->name('post-esc.download');
        Route::get('/delete/{id}', [PostEscController::class, 'destroy'])->name('post-esc.delete');
    });

    Route::group(['prefix' => '/invoices'], function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/store', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/show/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/edit/{id}', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::post('/update/{id}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::get('/delete/{id}', [InvoiceController::class, 'destroy'])->name('invoices.delete');
        Route::get('/download-pdf/{id}', [InvoiceController::class, 'downloadPdf'])->name('invoices.downloadPdf');
        Route::get('/send-email/{id}', [InvoiceController::class, 'sendEmail'])->name('invoices.sendEmail');
        Route::get('/sites-by-company/{company_id}', [InvoiceController::class, 'getSitesByCompany'])->name('invoices.sitesByCompany');
    });

    Route::group(['prefix' => '/taxes'], function () {
        Route::get('/', [TaxController::class, 'index'])->name('taxes.index');
        Route::post('/store', [TaxController::class, 'store'])->name('taxes.store');
        Route::post('/store-ajax', [TaxController::class, 'storeAjax'])->name('taxes.storeAjax');
        Route::post('/update/{id}', [TaxController::class, 'update'])->name('taxes.update');
        Route::get('/delete/{id}', [TaxController::class, 'destroy'])->name('taxes.delete');
    });

    Route::group(['prefix' => '/products'], function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
        Route::post('/store', [ProductController::class, 'store'])->name('products.store');
        Route::post('/store-ajax', [ProductController::class, 'storeAjax'])->name('products.storeAjax');
        Route::post('/update/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::get('/delete/{id}', [ProductController::class, 'destroy'])->name('products.delete');
        Route::get('/get-ajax', [ProductController::class, 'getProductsAjax'])->name('products.getAjax');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/timezone', [ProfileController::class, 'updateTimezone'])->name('profile.timezone.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
