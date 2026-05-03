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
        Schema::create('report_incident_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('date_of_report')->nullable();
            $table->string('time_of_report')->nullable();
            $table->string('location')->nullable();
            $table->string('property')->nullable();
            $table->string('incident_type')->nullable();
            $table->string('reported_by')->nullable();
            $table->string('responding_authority')->nullable();
            $table->string('cps_case_number')->nullable();
            $table->text('incident_report')->nullable();
            $table->text('subject_description')->nullable();
            $table->text('outcome')->nullable();
            $table->string('reported_by_name')->nullable();
            $table->string('reported_by_title')->nullable();
            $table->string('reviewed_by_name')->nullable();
            $table->string('reviewed_by_title')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_incident_forms');
    }
};
