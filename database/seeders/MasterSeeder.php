<?php

namespace Database\Seeders;

use App\Models\Master\MasterAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        // On shared hosting, the master database must be created manually via cPanel.
        // We skip the CREATE DATABASE step and go straight to migrations.

        $this->command->info('Running master migrations...');

        Artisan::call('migrate', [
            '--database' => 'master',
            '--path'     => 'database/migrations/master',
            '--force'    => true,
        ]);

        $this->command->info('✔ Master migrations applied.');

        // Create the Master Admin
        if (!MasterAdmin::on('master')->where('email', 'arslan.devsspace@gmail.com')->exists()) {
            MasterAdmin::on('master')->create([
                'name'          => 'Arslan',
                'email'         => 'arslan.devsspace@gmail.com',
                'password'      => Hash::make('Admin@1234'),
                'real_password' => 'Admin@1234',
                'is_active'     => true,
            ]);

            $this->command->info('✔ Master Admin created: arslan.devsspace@gmail.com / Admin@1234');
        } else {
            $this->command->info('  Master Admin already exists — skipped.');
        }
    }
}
