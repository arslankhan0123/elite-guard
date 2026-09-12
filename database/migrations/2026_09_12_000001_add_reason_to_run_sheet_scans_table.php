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
        if (!Schema::hasColumn('run_sheet_scans', 'reason')) {
            Schema::table('run_sheet_scans', function (Blueprint $table) {
                $table->text('reason')->nullable()->after('image');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('run_sheet_scans', 'reason')) {
            Schema::table('run_sheet_scans', function (Blueprint $table) {
                $table->dropColumn('reason');
            });
        }
    }
};
