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
        Schema::create('site_tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('scheduled_days');
            $table->boolean('is_continuous')->default(false);
            $table->string('schedule_type')->nullable(); // 'Specific Time' or 'Repeat'
            $table->json('specific_times')->nullable();
            $table->string('max_duration')->nullable();
            $table->string('tag_type');
            $table->json('tags')->nullable(); // Store selected NFC tag IDs
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Assigned guard
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_tours');
    }
};
