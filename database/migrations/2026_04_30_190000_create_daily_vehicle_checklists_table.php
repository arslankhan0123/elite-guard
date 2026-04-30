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
        Schema::create('daily_vehicle_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            
            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->string('vehicle')->nullable();
            $table->string('odometer_reading')->nullable();
            $table->string('fuel')->nullable();
            $table->string('assigned_site')->nullable();
            $table->string('driver')->nullable();
            $table->longText('signature')->nullable();

            // Exterior
            $table->text('cosmetic_issues')->nullable();
            $table->text('tires')->nullable();
            $table->text('windows')->nullable();

            // Interior
            $table->text('staff_care')->nullable();
            $table->text('dash_lights_gauges')->nullable();
            $table->text('documents')->nullable();

            // Other
            $table->text('engine')->nullable();
            $table->string('oil_life_percentage')->nullable();
            $table->text('equipment')->nullable();

            // BWC Inspection
            $table->text('bwc_used_for_inspection')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_vehicle_checklists');
    }
};
