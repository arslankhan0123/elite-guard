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
        Schema::create('report_security_guard_disciplinary_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('employee_name')->nullable();
            $table->string('employee_id_license')->nullable();
            $table->string('site_property')->nullable();
            $table->date('warning_date')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('shift_time')->nullable();
            $table->string('department_client')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('violation_type')->nullable();
            $table->string('classification_severity')->nullable();
            $table->string('classification_severity_other')->nullable();
            $table->date('incident_date')->nullable();
            $table->string('incident_time')->nullable();
            $table->string('location')->nullable();
            $table->string('reported_by')->nullable();
            $table->text('incident_summary')->nullable();
            $table->text('corrective_action')->nullable();
            $table->string('action_taken')->nullable();
            $table->string('issued_by')->nullable();
            $table->string('issued_by_title')->nullable();
            $table->longText('employee_signature')->nullable();
            $table->date('signature_date')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_security_guard_disciplinary_forms');
    }
};
