<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_daily_shift_forms', function (Blueprint $table) {
            $table->string('weather_conditions')->nullable()->after('client');
        });
    }

    public function down(): void
    {
        Schema::table('report_daily_shift_forms', function (Blueprint $table) {
            $table->dropColumn('weather_conditions');
        });
    }
};
