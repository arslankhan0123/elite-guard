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
        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropForeign(['assigned_guard_id']);
            $table->dropColumn('assigned_guard_id');
            $table->longText('assigned_guard_ids')->nullable()->after('site_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropColumn('assigned_guard_ids');
            $table->foreignId('assigned_guard_id')->nullable()->after('site_id')->constrained('users')->onDelete('set null');
        });
    }
};
