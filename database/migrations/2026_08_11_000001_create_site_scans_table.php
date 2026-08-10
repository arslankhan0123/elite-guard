<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('nfc_tag_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('time');
            $table->string('image')->nullable();
            $table->timestamps();

            // No unique constraint: the same tag may be scanned repeatedly.
            $table->index(['site_id', 'nfc_tag_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_scans');
    }
};
