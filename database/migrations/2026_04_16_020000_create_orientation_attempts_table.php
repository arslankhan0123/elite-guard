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
        Schema::create('orientation_attempts', function (Blueprint $create) {
            $create->id();
            $create->foreignId('user_id')->constrained()->onDelete('cascade');
            $create->foreignId('orientation_id')->constrained()->onDelete('cascade');
            $create->decimal('score', 5, 2);
            $create->boolean('is_passed');
            $create->json('answers')->nullable(); // Stores user answers for this attempt
            $create->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orientation_attempts');
    }
};
