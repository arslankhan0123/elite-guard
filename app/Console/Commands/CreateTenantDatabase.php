<?php

namespace App\Console\Commands;

use App\Models\Master\Tenant;
use App\Models\Master\TenantUserLookup;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateTenantDatabase extends Command
{
    protected $signature = 'tenant:create
                            {name : The tenant/company name}
                            {email : Admin email address}
                            {password : Admin password}
                            {--notes= : Internal notes}';

    protected $description = 'Create a new tenant with its own database, run migrations, and seed the SuperAdmin.';

    public function handle(): int
    {
        $name     = $this->argument('name');
        $email    = $this->argument('email');
        $password = $this->argument('password');
        $notes    = $this->option('notes');

        // Generate unique slug
        $slug = Str::slug($name);
        $base = $slug;
        $i    = 2;
        while (Tenant::on('master')->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $dbName = Tenant::dbNameFromSlug($slug);

        // Check email uniqueness
        if (Tenant::on('master')->where('admin_email', $email)->exists()) {
            $this->error("Email {$email} is already assigned to another tenant.");
            return self::FAILURE;
        }

        $this->info("Creating tenant: {$name}");
        $this->info("  Slug:     {$slug}");
        $this->info("  Database: {$dbName}");

        // Create record
        $tenant = Tenant::on('master')->create([
            'name'        => $name,
            'slug'        => $slug,
            'db_name'     => $dbName,
            'admin_email' => $email,
            'is_active'   => true,
            'notes'       => $notes,
        ]);

        // Create database
        $this->info("Creating database...");
        TenantService::createDatabase($dbName);

        // Run migrations
        $this->info("Running migrations...");
        TenantService::runMigrationsForTenant($tenant);

        // Create SuperAdmin user
        $this->info("Creating admin user...");
        $userId = DB::connection('tenant')->table('users')->insertGetId([
            'name'          => $name . ' Admin',
            'email'         => $email,
            'password'      => Hash::make($password),
            'real_password' => $password,
            'role'          => 'SuperAdmin',
            'status'        => 1,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // Sync to lookup table
        TenantUserLookup::on('master')->updateOrCreate(
            ['tenant_id' => $tenant->id, 'email' => $email],
            ['user_id'   => $userId]
        );

        $this->info('');
        $this->info("✔ Tenant created successfully!");
        $this->table(
            ['Field', 'Value'],
            [
                ['Tenant ID',    $tenant->id],
                ['Name',         $tenant->name],
                ['Database',     $tenant->db_name],
                ['Admin Email',  $email],
                ['Admin Panel',  url('/login')],
            ]
        );

        return self::SUCCESS;
    }
}
