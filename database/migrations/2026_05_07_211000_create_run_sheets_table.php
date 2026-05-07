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
        Schema::create('run_sheets', function (Blueprint $header) {
            $header->id();
            $header->foreignId('user_id')->constrained()->onDelete('cascade');
            $header->foreignId('site_id')->constrained()->onDelete('cascade');
            $header->date('date');
            $header->string('run_sheet_name')->nullable();
            $header->time('start_time')->nullable();
            $header->time('end_time')->nullable();
            $header->string('duration')->nullable();
            $header->string('job_type')->nullable();
            $header->string('sequence')->nullable();
            $header->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('run_sheets');
    }
};
