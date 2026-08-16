<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->time('start_time');
            $table->boolean('status')->default(false);
            $table->string('type')->default('scan');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->time('end_time')->nullable();
            $table->date('date');
            $table->timestamps();

            $table->unique(['site_id', 'user_id', 'date', 'type']);
        });

        Schema::create('site_item_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('nfc_tag_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('time');
            $table->string('image')->nullable();
            $table->timestamps();

            $table->index(['site_id', 'nfc_tag_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_item_scans');
        Schema::dropIfExists('site_items');
    }
};
