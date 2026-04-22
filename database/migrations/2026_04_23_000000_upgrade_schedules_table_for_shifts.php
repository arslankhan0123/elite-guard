<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Add individual indexes to keep foreign keys satisfied
            $table->index('user_id');
            $table->index('site_id');

            // Drop unique constraint
            $table->dropUnique(['user_id', 'site_id', 'week_start_date']);
            
            // Add new columns
            $table->date('date')->nullable()->after('site_id');
            $table->string('shift_name')->nullable()->after('date');
            $table->time('start_time')->nullable()->after('shift_name');
            $table->time('end_time')->nullable()->after('start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['date', 'shift_name', 'start_time', 'end_time']);
            
            // Restore unique constraint (might fail if data is inconsistent)
            $table->unique(['user_id', 'site_id', 'week_start_date']);
        });
    }
};
