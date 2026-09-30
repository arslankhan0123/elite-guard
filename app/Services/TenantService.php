<?php

namespace App\Services;

use App\Models\Master\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantService
{
    protected static ?Tenant $currentTenant = null;

    public static function setTenant(Tenant $tenant): void
    {
        static::$currentTenant = $tenant;

        $base = config('database.connections.mysql');

        Config::set('database.connections.tenant', array_merge($base, [
            'database' => $tenant->db_name,
        ]));

        DB::purge('tenant');
        DB::setDefaultConnection('tenant');
    }

    public static function getTenant(): ?Tenant
    {
        return static::$currentTenant;
    }

    public static function resetTenant(): void
    {
        static::$currentTenant = null;
        DB::purge('tenant');
        DB::setDefaultConnection('mysql');
    }

    /**
     * Create the tenant's database on the MySQL server.
     */
    public static function createDatabase(string $dbName): void
    {
        $charset   = config('database.connections.mysql.charset',   'utf8mb4');
        $collation = config('database.connections.mysql.collation', 'utf8mb4_unicode_ci');

        // Use the default 'mysql' connection for DDL — it doesn't require the
        // target database to already exist the way a per-DB connection does.
        DB::connection('mysql')->statement(
            "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET {$charset} COLLATE {$collation}"
        );
    }

    /**
     * Run all standard migrations against the tenant DB.
     */
    public static function runMigrationsForTenant(Tenant $tenant): void
    {
        static::setTenant($tenant);

        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path'     => 'database/migrations',
            '--force'    => true,
        ]);
    }

    /**
     * Copy all tables and rows from the source connection into the tenant DB.
     * Useful for seeding Elite Guard Inc. from the existing elite-guard_3 DB.
     */
    public static function seedFromExistingDatabase(Tenant $tenant, string $sourceConnection = 'mysql'): void
    {
        static::setTenant($tenant);

        $sourceDb  = config("database.connections.{$sourceConnection}.database");
        $targetDb  = $tenant->db_name;
        $masterPdo = DB::connection('master')->getPdo();

        // List all tables in the source DB (excluding system tables)
        $tables = DB::connection($sourceConnection)
            ->select("SHOW TABLES");

        $tableKey = "Tables_in_{$sourceDb}";

        // Disable FK checks on tenant connection
        DB::connection('tenant')->statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $row) {
            $table = $row->$tableKey;

            // Skip Laravel system tables — migrations already handle schema
            if (in_array($table, ['migrations', 'failed_jobs', 'jobs', 'job_batches', 'cache', 'cache_locks', 'sessions'], true)) {
                continue;
            }

            // Fetch all rows from source
            $rows = DB::connection($sourceConnection)->table($table)->get()->toArray();
            if (empty($rows)) {
                continue;
            }

            // Insert in chunks
            $chunks = array_chunk($rows, 500);
            foreach ($chunks as $chunk) {
                DB::connection('tenant')->table($table)->insert(
                    array_map(fn ($r) => (array) $r, $chunk)
                );
            }
        }

        DB::connection('tenant')->statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
