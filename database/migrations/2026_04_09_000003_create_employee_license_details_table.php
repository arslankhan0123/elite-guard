<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_license_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            
            // Security License
            $table->string('security_license_number')->nullable();
            $table->date('security_license_expiry')->nullable();
            $table->string('security_license_file')->nullable();
            
            // Drivers License
            $table->string('drivers_license_number')->nullable();
            $table->date('drivers_license_expiry')->nullable();
            $table->string('drivers_license_file')->nullable();
            
            // Work Eligibility
            $table->string('work_eligibility_type_number')->nullable();
            $table->date('work_eligibility_expiry')->nullable();
            $table->string('work_eligibility_file')->nullable();
            
            // Others
            $table->string('criminal_record_check')->nullable();
            $table->string('first_aid_training')->nullable();
            $table->text('other_certificates')->nullable();
            $table->string('other_documents_file')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_license_details');
    }
};
