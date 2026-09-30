<?php

namespace Database\Seeders;

use App\Models\Master\MasterAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating master database if not exists...');

        // On standard servers (AWS, VPS) this runs fine.
        // On shared hosting (Hostinger/cPanel), create the DB manually first.
        try {
            $charset   = config('database.connections.mysql.charset',   'utf8mb4');
            $collation = config('database.connections.mysql.collation', 'utf8mb4_unicode_ci');
            $dbName    = config('database.connections.master.database');

            DB::connection('mysql')->statement(
                "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET {$charset} COLLATE {$collation}"
            );
            $this->command->info("✔ Master database `{$dbName}` ready.");
        } catch (\Throwable $e) {
            // Shared hosting: DB must be pre-created via cPanel/hPanel.
            $this->command->warn('Could not auto-create master DB (shared hosting?): ' . $e->getMessage());
            $this->command->warn('Continuing — assuming the database was pre-created manually.');
        }

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
