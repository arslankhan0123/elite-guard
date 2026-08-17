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
        Schema::create('weekly_run_sheet_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_run_sheet_id')->constrained('weekly_run_sheets')->cascadeOnDelete();
            $table->foreignId('weekly_run_sheet_entry_id')->constrained('weekly_run_sheet_entries')->cascadeOnDelete();
            $table->foreignId('nfc_tag_id')->constrained('nfc_tags')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->time('time');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('image')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['weekly_run_sheet_entry_id', 'date'], 'weekly_entry_scan_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_run_sheet_scans');
    }
};
