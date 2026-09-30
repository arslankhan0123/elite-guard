<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bootstrap pagination
        Paginator::useBootstrapFive();

        // Prohibit destructive DB commands in all environments
        DB::prohibitDestructiveCommands(true);

        // Register UserObserver to keep tenant_user_lookup in sync
        User::observe(UserObserver::class);

        // Dynamically set system timezone from DB settings with fallback to env
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $timezone = \App\Models\Setting::get('timezone');
                if ($timezone && in_array($timezone, \DateTimeZone::listIdentifiers())) {
                    config(['app.timezone' => $timezone]);
                    date_default_timezone_set($timezone);
                }
            }
        } catch (\Throwable $e) {
            // Graceful fallback if database connection is not established
        }
    }
}
