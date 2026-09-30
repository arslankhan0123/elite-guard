<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds tenant_id to all report & form tables so data can be scoped
     * directly by tenant without relying on a user relationship join.
     */
    public function up(): void
    {
        $tables = [
            'report_general_forms',
            'report_incident_forms',
            'report_security_guard_disciplinary_forms',
            'report_daily_shift_forms',
            'assessments',
            'daily_vehicle_checklists',
            'fire_watch_reports',
            'shift_adjustment_forms',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('tenant_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('tenants')
                      ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'report_general_forms',
            'report_incident_forms',
            'report_security_guard_disciplinary_forms',
            'report_daily_shift_forms',
            'assessments',
            'daily_vehicle_checklists',
            'fire_watch_reports',
            'shift_adjustment_forms',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropForeign([$tableName . '_tenant_id_foreign'] ?? ['tenant_id']);
                $table->dropColumn('tenant_id');
            });
        }
    }
};
