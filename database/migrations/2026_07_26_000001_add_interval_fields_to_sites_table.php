<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->unsignedInteger('interval')->nullable()->after('end_time');
            $table->unsignedInteger('open_time')->nullable()->after('interval');
            $table->unsignedInteger('grace_time')->nullable()->after('open_time');
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['interval', 'open_time', 'grace_time']);
        });
    }
};
