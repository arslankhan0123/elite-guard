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
        Schema::create('employee_offer_letters', function (Blueprint $blueprint) {
            $blueprint->id();

            $blueprint->unsignedBigInteger('user_id')->nullable()->unsigned();
            $blueprint->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $blueprint->string('job_title')->nullable();
            $blueprint->date('joining_date')->nullable();
            $blueprint->string('salary')->nullable();
            $blueprint->longText('description')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_offer_letters');
    }
};
