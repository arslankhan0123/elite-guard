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
        $masterDb  = config('database.connections.master.database', 'elite_guard_master');
        $charset   = config('database.connections.mysql.charset',   'utf8mb4');
        $collation = config('database.connections.mysql.collation', 'utf8mb4_unicode_ci');

        // ── Step 1: Create the master database ────────────────────────────
        // MUST use the default 'mysql' connection here — the 'master'
        // connection cannot connect until its database actually exists.
        DB::connection('mysql')->statement(
            "CREATE DATABASE IF NOT EXISTS `{$masterDb}` CHARACTER SET {$charset} COLLATE {$collation}"
        );

        $this->command->info("✔ Master database `{$masterDb}` ready.");

        // ── Step 2: Run master-specific migrations ────────────────────────
        Artisan::call('migrate', [
            '--database' => 'master',
            '--path'     => 'database/migrations/master',
            '--force'    => true,
        ]);

        $this->command->info('✔ Master migrations applied.');

        // ── Step 3: Create the Master Admin ──────────────────────────────
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
