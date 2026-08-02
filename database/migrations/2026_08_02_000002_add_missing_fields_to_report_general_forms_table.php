<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_general_forms', function (Blueprint $table) {
            $table->unsignedBigInteger('reported_by_id')->nullable()->after('reported_by');
            $table->string('location')->nullable()->after('location_of_incident');
            $table->string('location_of_report')->nullable()->after('location');
            $table->string('property')->nullable()->after('property_name');
            $table->string('property_address')->nullable()->after('property');
        });
    }

    public function down(): void
    {
        Schema::table('report_general_forms', function (Blueprint $table) {
            $table->dropColumn([
                'reported_by_id',
                'location',
                'location_of_report',
                'property',
                'property_address',
            ]);
        });
    }
};
