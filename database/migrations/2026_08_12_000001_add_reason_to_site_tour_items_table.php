<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_tour_items', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('site_tour_items', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
};
