<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fire_watch_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('site_id')->nullable()->after('user_id')->index();
            $table->unsignedBigInteger('supervisor_id')->nullable()->after('site_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('fire_watch_reports', function (Blueprint $table) {
            $table->dropColumn(['site_id', 'supervisor_id']);
        });
    }
};
