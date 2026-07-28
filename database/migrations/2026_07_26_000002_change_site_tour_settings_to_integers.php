<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->unsignedInteger('interval')->nullable()->change();
            $table->unsignedInteger('open_time')->nullable()->change();
            $table->unsignedInteger('grace_time')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->decimal('interval', 12, 2)->nullable()->change();
            $table->decimal('open_time', 12, 2)->nullable()->change();
            $table->decimal('grace_time', 12, 2)->nullable()->change();
        });
    }
};
