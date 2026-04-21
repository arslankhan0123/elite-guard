<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_document_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tax_document_id')->constrained('tax_documents')->onDelete('cascade');
            $table->string('document_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_document_submissions');
    }
};
