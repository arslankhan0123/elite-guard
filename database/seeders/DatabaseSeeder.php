<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Run ALL:
     *   php artisan db:seed
     *
     * Run individually:
     *   php artisan db:seed --class=MasterSeeder
     *   php artisan db:seed --class=EliteGuardTenantSeeder
     */
    public function run(): void
    {
        $this->call([
            MasterSeeder::class,
            EliteGuardTenantSeeder::class,
        ]);
    }
}
