<?php

namespace App\Console\Commands;

use App\Models\Master\Tenant;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AddUomToInvoiceItems extends Command
{
    protected $signature = 'tenants:add-uom-to-invoice-items';

    protected $description = 'Add the uom column to invoice_items table in all tenant databases';

    public function handle(): int
    {
        $tenants = Tenant::on('master')->whereNotNull('db_name')->get();
        $updated = 0;
        $skipped = 0;
        $failed  = 0;

        foreach ($tenants as $tenant) {
            try {
                TenantService::setTenant($tenant);

                if (Schema::connection('tenant')->hasColumn('invoice_items', 'uom')) {
                    $this->warn("Skipped {$tenant->db_name}: uom column already exists in invoice_items.");
                    $skipped++;
                    continue;
                }

                $exitCode = Artisan::call('migrate', [
                    '--database'       => 'tenant',
                    '--path'           => 'database/migrations/2026_10_15_000000_add_uom_to_invoice_items_table.php',
                    '--force'          => true,
                    '--no-interaction' => true,
                ]);

                $isReady = Schema::connection('tenant')->hasColumn('invoice_items', 'uom');

                if ($exitCode !== 0 || ! $isReady) {
                    $this->error("Migration failed for {$tenant->db_name}: " . trim(Artisan::output()));
                    $failed++;
                    continue;
                }

                $this->info("uom column added to invoice_items: {$tenant->db_name}");
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
