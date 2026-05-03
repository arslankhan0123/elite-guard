<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_general_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('report_date')->nullable();
            $table->string('report_time')->nullable();
            $table->string('property_location')->nullable();
            $table->string('property_name')->nullable();
            $table->string('reported_by')->nullable();
            $table->string('report_type')->nullable();
            $table->string('time_engaged')->nullable();
            $table->string('time_area_cleared')->nullable();
            $table->string('location_of_incident')->nullable();
            $table->text('observation_situation')->nullable();
            $table->text('action_taken')->nullable();
            $table->longText('signature')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_general_forms');
    }
};
