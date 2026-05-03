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
        Schema::create('report_daily_shift_form_patrol_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_daily_shift_form_id');
            $table->string('time_range')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();

            $table->foreign('report_daily_shift_form_id', 'rdsfpe_rdsf_id_foreign')->references('id')->on('report_daily_shift_forms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_daily_shift_form_patrol_entries');
    }
};
