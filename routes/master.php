<?php

use App\Http\Controllers\Master\MasterAuthController;
use App\Http\Controllers\Master\MasterDashboardController;
use App\Http\Controllers\Master\TenantController;
use Illuminate\Support\Facades\Route;

Route::prefix('master')->name('master.')->group(function () {

    // Guest-only routes (redirect to dashboard if already logged in)
    Route::middleware('guest:master')->group(function () {
        Route::get('/login', [MasterAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [MasterAuthController::class, 'login'])->name('login.submit');
    });

    // Authenticated master admin routes
    Route::middleware(['auth:master', 'master.admin'])->group(function () {
        Route::post('/logout', [MasterAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [MasterDashboardController::class, 'index'])->name('dashboard');

        // Tenant CRUD
        Route::prefix('tenants')->name('tenants.')->group(function () {
            Route::get('/',          [TenantController::class, 'index'])->name('index');
            Route::get('/create',    [TenantController::class, 'create'])->name('create');
            Route::post('/',         [TenantController::class, 'store'])->name('store');
            Route::get('/{tenant}',  [TenantController::class, 'show'])->name('show');
            Route::get('/{tenant}/edit',   [TenantController::class, 'edit'])->name('edit');
            Route::put('/{tenant}',        [TenantController::class, 'update'])->name('update');
            Route::patch('/{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])->name('toggle-status');
        });
    });
});
