<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_run_sheets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('week_start_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('week_start_date');
        });

        Schema::create('weekly_run_sheet_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_run_sheet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->string('tour_name');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('sequence')->default(1);
            $table->timestamps();

            $table->index(['weekly_run_sheet_id', 'day_of_week', 'sequence'], 'weekly_run_sheet_day_sequence');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_run_sheet_entries');
        Schema::dropIfExists('weekly_run_sheets');
    }
};
