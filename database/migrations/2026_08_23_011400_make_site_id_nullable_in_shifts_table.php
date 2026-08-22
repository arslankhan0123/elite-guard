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
            $table->dropForeign(['site_id']);
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->unsignedBigInteger('site_id')->nullable()->change();
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->unsignedBigInteger('site_id')->nullable(false)->change();
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
        });
    }
};
