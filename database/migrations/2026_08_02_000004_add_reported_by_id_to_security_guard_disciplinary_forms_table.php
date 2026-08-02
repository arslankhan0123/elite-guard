<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_security_guard_disciplinary_forms', function (Blueprint $table) {
            $table->unsignedBigInteger('reported_by_id')->nullable()->after('reported_by');
        });
    }

    public function down(): void
    {
        Schema::table('report_security_guard_disciplinary_forms', function (Blueprint $table) {
            $table->dropColumn('reported_by_id');
        });
    }
};
