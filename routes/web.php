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

        return view('dashboard', compact('companyCount', 'siteCount', 'nfcCount', 'employeeCount', 'pendingOpenShiftClaimsCount', 'pendingAvailCount', 'todayAttendanceCount'));
    })->name('dashboard');

    Route::group(['prefix' => '/company'], function () {
        Route::get('/', [CompanyController::class, 'index'])->name('companies.index');
        Route::get('/create', [CompanyController::class, 'create'])->name('companies.create');
        Route::post('/store', [CompanyController::class, 'store'])->name('companies.store');
        Route::get('/edit/{company_id}', [CompanyController::class, 'edit'])->name('companies.edit');
        Route::post('/update/{company_id}', [CompanyController::class, 'update'])->name('companies.update');
        Route::get('/delete/{company_id}', [CompanyController::class, 'delete'])->name('companies.delete');
    });

    Route::group(['prefix' => '/site'], function () {
        Route::get('/', [SiteController::class, 'index'])->name('sites.index');
        Route::get('/create', [SiteController::class, 'create'])->name('sites.create');
        Route::post('/store', [SiteController::class, 'store'])->name('sites.store');
        Route::get('/edit/{site_id}', [SiteController::class, 'edit'])->name('sites.edit');
        Route::post('/update/{site_id}', [SiteController::class, 'update'])->name('sites.update');
        Route::get('/delete/{site_id}', [SiteController::class, 'delete'])->name('sites.delete');
    });

    Route::group(['prefix' => '/nfc'], function () {
        Route::get('/', [NfcTagController::class, 'index'])->name('nfc.index');
        Route::get('/create', [NfcTagController::class, 'create'])->name('nfc.create');
        Route::post('/store', [NfcTagController::class, 'store'])->name('nfc.store');
        Route::get('/edit/{nfc_id}', [NfcTagController::class, 'edit'])->name('nfc.edit');
        Route::post('/update/{nfc_id}', [NfcTagController::class, 'update'])->name('nfc.update');
        Route::get('/delete/{nfc_id}', [NfcTagController::class, 'delete'])->name('nfc.delete');
    });

    Route::group(['prefix' => '/schedules'], function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::post('/store', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::post('/update', [ScheduleController::class, 'update'])->name('schedules.update');
        Route::get('/delete/{id}', [ScheduleController::class, 'destroy'])->name('schedules.delete');
    });

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
        });

        Route::group(['prefix' => '/employee'], function () {
            Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
            Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
            Route::post('/store', [EmployeeController::class, 'store'])->name('employees.store');
            Route::get('/edit/{id}', [EmployeeController::class, 'edit'])->name('employees.edit');
            Route::post('/update/{id}', [EmployeeController::class, 'update'])->name('employees.update');
            Route::get('/delete/{id}', [EmployeeController::class, 'delete'])->name('employees.delete');
            Route::post('/assign-sites/{user_id}', [EmployeeController::class, 'assignSites'])->name('employees.assignSites');
            Route::post('/update-offer-letter', [EmployeeController::class, 'updateOfferLetter'])->name('employees.updateOfferLetter');
            Route::post('/update-pay-slip', [EmployeeController::class, 'updatePaySlip'])->name('employees.updatePaySlip');
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
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
