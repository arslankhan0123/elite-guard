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
        Schema::create('fire_watch_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('client_site_name');
            $table->string('address_location');
            $table->text('reason_for_fire_watch')->nullable();
            $table->text('fire_watch_areas')->nullable();
            $table->string('commenced_date');
            $table->string('commenced_time');
            $table->string('terminated_date');
            $table->string('terminated_time');
            $table->text('guards')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('patrol_interval')->nullable(); // e.g. 30 Minutes, 60 Minutes, Continuous
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fire_watch_reports');
    }
};
