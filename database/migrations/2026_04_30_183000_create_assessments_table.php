<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable()->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('worker_email')->nullable();
            $table->string('shift_date')->nullable();
            $table->string('location')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();

            $table->string('client')->nullable();
            $table->string('supervisor_first_name')->nullable();
            $table->string('supervisor_last_name')->nullable();
            $table->string('position_today')->nullable();

            // Boolean fields from the form
            $table->boolean('compliance_fit_for_duty')->default(false);
            $table->boolean('any_injuries')->default(false);
            $table->boolean('physically_prepared')->default(false);
            $table->boolean('any_symptoms')->default(false);
            $table->boolean('understand_unethical_work_sick')->default(false);
            $table->boolean('up_to_date_orders')->default(false);
            $table->boolean('believe_fit_for_duty')->default(false);
            $table->boolean('safety_concerns')->default(false);
            $table->boolean('hazards_identified')->default(false);

            $table->string('right_to_refuse')->nullable();
            $table->string('right_to_participate')->nullable();
            $table->text('signature')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
