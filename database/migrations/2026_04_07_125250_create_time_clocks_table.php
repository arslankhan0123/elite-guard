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
        Schema::create('time_clocks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable()->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('nfc_tag_id')->nullable();
            $table->foreign('nfc_tag_id')->references('id')->on('nfc_tags')->onDelete('cascade')->onUpdate('cascade');

            $table->string('check_in_time')->nullable();
            $table->string('check_out_time')->nullable();
            $table->string('status')->nullable()->comment('checked_in or checked_out');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_clocks');
    }
};
