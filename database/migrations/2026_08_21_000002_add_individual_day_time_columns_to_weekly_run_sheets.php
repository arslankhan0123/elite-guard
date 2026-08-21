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
            if (Schema::hasColumn('weekly_run_sheets', 'day_times')) {
                $table->dropColumn('day_times');
            }

            $table->time('monday_start_time')->nullable()->after('notes');
            $table->time('monday_end_time')->nullable()->after('monday_start_time');
            $table->time('tuesday_start_time')->nullable()->after('monday_end_time');
            $table->time('tuesday_end_time')->nullable()->after('tuesday_start_time');
            $table->time('wednesday_start_time')->nullable()->after('tuesday_end_time');
            $table->time('wednesday_end_time')->nullable()->after('wednesday_start_time');
            $table->time('thursday_start_time')->nullable()->after('wednesday_end_time');
            $table->time('thursday_end_time')->nullable()->after('thursday_start_time');
            $table->time('friday_start_time')->nullable()->after('thursday_end_time');
            $table->time('friday_end_time')->nullable()->after('friday_start_time');
            $table->time('saturday_start_time')->nullable()->after('friday_end_time');
            $table->time('saturday_end_time')->nullable()->after('saturday_start_time');
            $table->time('sunday_start_time')->nullable()->after('saturday_end_time');
            $table->time('sunday_end_time')->nullable()->after('sunday_start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_run_sheets', function (Blueprint $table) {
            $table->dropColumn([
                'monday_start_time', 'monday_end_time',
                'tuesday_start_time', 'tuesday_end_time',
                'wednesday_start_time', 'wednesday_end_time',
                'thursday_start_time', 'thursday_end_time',
                'friday_start_time', 'friday_end_time',
                'saturday_start_time', 'saturday_end_time',
                'sunday_start_time', 'sunday_end_time',
            ]);
        });
    }
};
