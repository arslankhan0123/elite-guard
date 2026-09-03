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
        Schema::create('deleted_weekly_run_sheet_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_run_sheet_entry_id')->constrained('weekly_run_sheet_entries')->cascadeOnDelete();
            $table->date('date');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['weekly_run_sheet_entry_id', 'date'], 'deleted_entry_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deleted_weekly_run_sheet_items');
    }
};
