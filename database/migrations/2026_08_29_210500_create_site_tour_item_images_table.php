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
        Schema::create('site_tour_item_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_tour_item_id');
            $table->longText('image_path')->nullable();
            $table->timestamps();

            $table->foreign('site_tour_item_id')
                  ->references('id')
                  ->on('site_tour_items')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_tour_item_images');
    }
};
