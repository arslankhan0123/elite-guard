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
        Schema::table('site_tour_items', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->boolean('status')->default(false);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('site_id')->nullable()->constrained('sites')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_tour_items', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['site_id']);
            $table->dropColumn(['type', 'status', 'user_id', 'site_id']);
        });
    }
};
