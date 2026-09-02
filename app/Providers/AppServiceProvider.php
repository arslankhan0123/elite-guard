<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
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
        // The admin dashboard uses Bootstrap, so render pagination controls with
        // Bootstrap markup instead of Laravel's Tailwind/SVG-based default.
        Paginator::useBootstrapFive();

        // Prohibit destructive database commands (migrate:fresh, migrate:reset, db:wipe, etc.) universally in all environments
        DB::prohibitDestructiveCommands(true);

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
