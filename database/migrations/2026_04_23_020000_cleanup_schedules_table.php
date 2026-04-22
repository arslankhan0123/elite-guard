<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Schedule;
use App\Models\Shift;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // 1. Drop foreign keys and indexes first
            $table->dropForeign(['site_id']);
            $table->dropIndex(['site_id']);
            
            // 2. Drop redundant columns
            $table->dropColumn(['site_id', 'date', 'shift_name', 'start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable()->after('user_id');
            $table->date('date')->nullable()->after('site_id');
            $table->string('shift_name')->nullable()->after('date');
            $table->time('start_time')->nullable()->after('shift_name');
            $table->time('end_time')->nullable()->after('start_time');
        });

        // Move data back? (Complex, usually we don't go back once refactored this way)
    }
};
