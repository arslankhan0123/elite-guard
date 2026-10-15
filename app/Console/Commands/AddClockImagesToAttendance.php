<?php

namespace App\Console\Commands;

use App\Models\Master\Tenant;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AddClockImagesToAttendance extends Command
{
    protected $signature = 'tenants:add-clock-images-to-attendance';
    protected $description = 'Add clock_in_image and clock_out_image columns to shift_attendance table in all tenant databases';

    public function handle(): int
    {
        $tenants = Tenant::on('master')->whereNotNull('db_name')->get();
        $updated = 0;
        $skipped = 0;
        $failed  = 0;

        foreach ($tenants as $tenant) {
            try {
                TenantService::setTenant($tenant);

                $alreadyDone = Schema::connection('tenant')->hasColumn('shift_attendance', 'clock_in_image')
                    && Schema::connection('tenant')->hasColumn('shift_attendance', 'clock_out_image');

                if ($alreadyDone) {
                    $this->warn("Skipped {$tenant->db_name}: columns already exist.");
                    $skipped++;
                    continue;
                }

                $exitCode = Artisan::call('migrate', [
                    '--database'       => 'tenant',
                    '--path'           => 'database/migrations/2026_10_16_000000_add_clock_images_to_shift_attendance_table.php',
                    '--force'          => true,
                    '--no-interaction' => true,
                ]);

                $isReady = Schema::connection('tenant')->hasColumn('shift_attendance', 'clock_in_image');

                if ($exitCode !== 0 || !$isReady) {
                    $this->error("Migration failed for {$tenant->db_name}: " . trim(Artisan::output()));
                    $failed++;
                    continue;
                }

                $this->info("clock_in_image & clock_out_image added to shift_attendance: {$tenant->db_name}");
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
