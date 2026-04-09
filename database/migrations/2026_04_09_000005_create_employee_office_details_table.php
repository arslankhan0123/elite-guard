<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_office_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            
            $table->string('employment_type')->nullable(); // Full Time, Part Time, Casual
            $table->date('start_date')->nullable();
            $table->string('job_position')->nullable();
            $table->string('wage')->nullable();
            $table->text('other_notes')->nullable();
            $table->string('hiring_manager_name')->nullable();
            $table->string('hiring_manager_signature')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_office_details');
    }
};
