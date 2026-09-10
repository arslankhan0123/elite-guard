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
        Schema::table('run_sheets', function (Blueprint $table) {
            $table->foreignId('weekly_run_sheet_entry_id')->nullable()->after('shift_id')->constrained('weekly_run_sheet_entries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('run_sheets', function (Blueprint $table) {
            $table->dropForeign(['weekly_run_sheet_entry_id']);
            $table->dropColumn('weekly_run_sheet_entry_id');
        });
    }
};
