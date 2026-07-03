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
        Schema::create('fire_watch_patrol_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fire_watch_report_id')->constrained('fire_watch_reports')->onDelete('cascade');
            $table->string('round')->nullable();
            $table->string('date')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->text('area_patrolled_findings')->nullable();
            $table->string('initials')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fire_watch_patrol_logs');
    }
};
