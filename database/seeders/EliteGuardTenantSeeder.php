<?php

namespace Database\Seeders;

use App\Models\Master\Tenant;
use App\Models\Master\TenantUserLookup;
use App\Services\TenantService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EliteGuardTenantSeeder extends Seeder
{
    /**
     * Creates the "Elite Guard Inc." tenant and copies data from the
     * existing elite-guard_3 database into the tenant's own database.
     *
     * Prerequisites:
     *   - MasterSeeder has already run (master DB + migrations exist)
     *   - The source DB (elite-guard_3) is accessible via the 'mysql' connection
     *
     * Run with:
     *   php artisan db:seed --class=EliteGuardTenantSeeder
     */
    public function run(): void
    {
        $slug = 'elite-guard-inc';
        $dbName = Tenant::dbNameFromSlug($slug);

        // 1. Create or retrieve the tenant record in the master DB
        $tenant = Tenant::on('master')->firstOrCreate(
            ['slug' => $slug],
            [
                'name' => 'Elite Guard Inc.',
                'db_name' => 'u227527917_eg_tenant',
                'admin_email' => 'admin@eliteguardinc.ca',
                'is_active' => true,
                'notes' => 'Migrated from existing elite-guard_3 database.',
            ]
        );

        $this->command->info("✔ Tenant record ready: {$tenant->name} (ID: {$tenant->id})");

        // 2. Create the tenant database
        TenantService::createDatabase($dbName);
        $this->command->info("✔ Database `{$dbName}` created.");

        // 3. Run standard migrations on the tenant DB
        TenantService::runMigrationsForTenant($tenant);
        $this->command->info("✔ Migrations applied to `{$dbName}`.");

        // 4. Copy all data from the existing 'mysql' (elite-guard_3) DB
        $this->command->info("  Copying data from 'mysql' connection (elite-guard_3)...");
        TenantService::seedFromExistingDatabase($tenant, 'mysql');
        $this->command->info("✔ Data copied.");

        // 5. Ensure a SuperAdmin user exists in the tenant DB
        $adminEmail = $tenant->admin_email;
        $existingUser = DB::connection('tenant')->table('users')
            ->where('email', $adminEmail)
            ->first();

        if (!$existingUser) {
            $userId = DB::connection('tenant')->table('users')->insertGetId([
                'name' => 'Elite Guard Admin',
                'email' => $adminEmail,
                'password' => Hash::make('Admin@1234'),
                'real_password' => 'Admin@1234',
                'role' => 'SuperAdmin',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $userId = $existingUser->id;
        }

        // 6. Sync all existing users from the tenant DB to the lookup table
        $this->command->info("  Syncing users to tenant_user_lookup...");
        $users = DB::connection('tenant')->table('users')->get();

        foreach ($users as $user) {
            TenantUserLookup::on('master')->updateOrCreate(
                ['tenant_id' => $tenant->id, 'email' => $user->email],
                ['user_id' => $user->id]
            );
        }

        $this->command->info("✔ Synced {$users->count()} users to lookup table.");
        $this->command->info("✔ Elite Guard Inc. tenant is ready.");
        $this->command->info("  Admin login: {$adminEmail} / Admin@1234");
        $this->command->info("  Admin panel: your-domain.com/login");
    }
}
