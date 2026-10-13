<?php

namespace App\Console\Commands;

use App\Models\Master\Tenant;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AddAdminNotificationsToTenants extends Command
{
    protected $signature = 'tenants:add-admin-notifications';

    protected $description = 'Create the admin_notifications table in all tenant databases';

    public function handle(): int
    {
        $tenants = Tenant::on('master')->whereNotNull('db_name')->get();
        $updated = 0;
        $skipped = 0;
        $failed  = 0;

        foreach ($tenants as $tenant) {
            try {
                TenantService::setTenant($tenant);

                if (Schema::connection('tenant')->hasTable('admin_notifications')) {
                    $this->warn("Skipped {$tenant->db_name}: admin_notifications table already exists.");
                    $skipped++;
                    continue;
                }

                $exitCode = Artisan::call('migrate', [
                    '--database'       => 'tenant',
                    '--path'           => 'database/migrations/2026_10_13_000000_create_admin_notifications_table.php',
                    '--force'          => true,
                    '--no-interaction' => true,
                ]);

                $isReady = Schema::connection('tenant')->hasTable('admin_notifications');

                if ($exitCode !== 0 || ! $isReady) {
                    $this->error("Migration failed for {$tenant->db_name}: " . trim(Artisan::output()));
                    $failed++;
                    continue;
                }

                $this->info("admin_notifications ready: {$tenant->db_name}");
                $updated++;
            } catch (Throwable $exception) {
                $this->error("Failed: {$tenant->db_name} — {$exception->getMessage()}");
                $failed++;
            } finally {
                DB::purge('tenant');
            }
        }

        TenantService::resetTenant();
        $this->newLine();
        $this->info("Finished. Updated: {$updated}; skipped: {$skipped}; failed: {$failed}.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
