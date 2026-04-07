<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DegreeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NfcTagController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TimeClockController;
use App\Http\Controllers\UniversityController;
use App\Models\Company;
use App\Models\Employee;
use App\Models\NfcTag;
use App\Models\Site;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/architecture', function () {
    return view('architecture');
})->name('architecture');

Route::middleware(['auth', 'verified', 'superadmin'])->group(function () {
    Route::get('/dashboard', function () {
        $companyCount = Company::count();
        $siteCount = Site::count();
        $nfcCount = NfcTag::count();
        $employeeCount = Employee::count();

        return view('dashboard', compact('companyCount', 'siteCount', 'nfcCount', 'employeeCount'));
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

    Route::group(['prefix' => '/employee'], function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/store', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/edit/{id}', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::post('/update/{id}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::get('/delete/{id}', [EmployeeController::class, 'delete'])->name('employees.delete');
    });

    Route::group(['prefix' => '/reports'], function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
    });

    Route::group(['prefix' => '/time-clocks'], function () {
        Route::get('/', [TimeClockController::class, 'index'])->name('time-clocks.index');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
