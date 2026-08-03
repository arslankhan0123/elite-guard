<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shift_adjustment_forms', function (Blueprint $table) {
            $table->unsignedBigInteger('site_id')->nullable()->after('user_id')->index();
            $table->unsignedBigInteger('current_supervisor_id')->nullable()->after('site_id')->index();
            $table->unsignedBigInteger('supervisor_id')->nullable()->after('current_supervisor_id')->index();
            $table->unsignedBigInteger('approving_supervisor_id')->nullable()->after('supervisor_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('shift_adjustment_forms', function (Blueprint $table) {
            $table->dropColumn([
                'site_id',
                'current_supervisor_id',
                'supervisor_id',
                'approving_supervisor_id',
            ]);
        });
    }
};
