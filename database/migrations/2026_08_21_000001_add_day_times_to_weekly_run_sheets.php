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
        Schema::table('weekly_run_sheets', function (Blueprint $table) {
            $table->json('day_times')->nullable()->after('notes');
        });

        Schema::table('weekly_run_sheet_entries', function (Blueprint $table) {
            $table->time('start_time')->nullable()->change();
            $table->time('end_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_run_sheet_entries', function (Blueprint $table) {
            $table->time('start_time')->nullable(false)->change();
            $table->time('end_time')->nullable(false)->change();
        });

        Schema::table('weekly_run_sheets', function (Blueprint $table) {
            $table->dropColumn('day_times');
        });
    }
};
