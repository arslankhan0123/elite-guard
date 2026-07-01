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
        Schema::create('shift_adjustment_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Employee Information
            $table->string('employee_name');
            $table->string('employee_id')->nullable();
            $table->string('position_site')->nullable();
            $table->string('department')->nullable();

            // Current Shift
            $table->date('current_date');
            $table->string('current_start_time')->nullable();
            $table->string('current_end_time')->nullable();
            $table->string('current_supervisor')->nullable();
            $table->string('current_shift_type')->nullable(); // Day / Night / On-call

            // Requested Adjustment - Checkboxes
            $table->boolean('shift_swap')->default(false);
            $table->boolean('late_start')->default(false);
            $table->boolean('coverage_request')->default(false);
            $table->boolean('early_release')->default(false);
            $table->boolean('time_off_request')->default(false);
            $table->boolean('overtime_approval')->default(false);

            // Requested Adjustment - Details
            $table->date('requested_date')->nullable();
            $table->string('requested_start_time')->nullable();
            $table->string('requested_end_time')->nullable();
            $table->string('replacement_employee')->nullable();
            $table->string('adjustment_reason')->nullable();
            $table->text('additional_details')->nullable();

            // Approval Section
            $table->string('supervisor_name')->nullable();
            $table->date('approval_date')->nullable();
            $table->string('decision')->nullable(); // Approved / Denied / Pending
            $table->string('approved_hours')->nullable();
            $table->text('supervisor_notes')->nullable();

            // Signatures
            $table->text('employee_signature')->nullable();
            $table->text('supervisor_signature')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_adjustment_forms');
    }
};
