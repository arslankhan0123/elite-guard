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
        Schema::table('shifts', function (Blueprint $table) {
            $table->string('type')->default('site')->after('site_id');
            $table->foreignId('weekly_run_sheet_id')->nullable()->after('type')->constrained('weekly_run_sheets')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropForeign(['weekly_run_sheet_id']);
            $table->dropColumn(['type', 'weekly_run_sheet_id']);
        });
    }
};
