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
        Schema::create('report_incident_form_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_incident_form_id');
            $table->longText('image_path')->nullable();
            $table->timestamps();

            $table->foreign('report_incident_form_id')->references('id')->on('report_incident_forms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_incident_form_images');
    }
};
