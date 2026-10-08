<?php

namespace App\Console\Commands;

use App\Models\Master\Tenant;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AddInvoiceItemDescriptionsToTenants extends Command
{
    protected $signature = 'tenants:add-invoice-item-descriptions';

    protected $description = 'Add the invoice item description column to each tenant database where it is missing';

    public function handle(): int
    {
        $tenants = Tenant::on('master')->whereNotNull('db_name')->get();
        $updated = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($tenants as $tenant) {
            try {
                TenantService::setTenant($tenant);

                if (! Schema::connection('tenant')->hasTable('invoice_items')) {
                    $this->warn("Skipped {$tenant->db_name}: invoice_items table not found.");
                    $skipped++;
                    continue;
                }

                if (Schema::connection('tenant')->hasColumn('invoice_items', 'description')) {
                    $this->line("Already up to date: {$tenant->db_name}");
                    $skipped++;
                    continue;
                }

                $exitCode = Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--path' => 'database/migrations/2026_10_08_000000_add_description_to_invoice_items_table.php',
                    '--force' => true,
                    '--no-interaction' => true,
                ]);

                if ($exitCode !== 0 || ! Schema::connection('tenant')->hasColumn('invoice_items', 'description')) {
                    $this->error("Migration failed for {$tenant->db_name}: " . trim(Artisan::output()));
                    $failed++;
                    continue;
                }

                $this->info("Updated: {$tenant->db_name}");
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
        $this->info("Finished. Updated: {$updated}; already present or skipped: {$skipped}; failed: {$failed}.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
