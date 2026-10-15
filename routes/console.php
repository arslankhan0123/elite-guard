<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Check subscription expiry daily at 8:00 AM
Schedule::command('subscriptions:check-expiry')->dailyAt('08:00');

// Check missed clock-ins every 5 minutes
Schedule::command('shifts:check-missed-clockins')->everyFiveMinutes();

// Check missed clock-outs every 5 minutes
// Notifies guard + admins from 10 min before shift end up to 30 min after
Schedule::command('shifts:check-missed-clockouts')->everyFiveMinutes();

// Add tax_rate column to invoice_items across all tenant DBs (safe to run daily — skips if already done)
Schedule::command('tenants:add-tax-rate')->dailyAt('08:05');
