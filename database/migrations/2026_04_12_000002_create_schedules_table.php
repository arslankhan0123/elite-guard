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
        Schema::create('schedules', function (Blueprint $create) {
            $create->id();
            $create->foreignId('user_id')->constrained()->onDelete('cascade');
            $create->foreignId('site_id')->constrained()->onDelete('cascade');
            $create->date('week_start_date');
            $create->text('notes')->nullable();
            $create->boolean('is_email_sent')->default(false);
            $create->boolean('is_notification_sent')->default(false);
            $create->timestamps();

            // Unique assignment for a specific user, site, and week
            $create->unique(['user_id', 'site_id', 'week_start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
