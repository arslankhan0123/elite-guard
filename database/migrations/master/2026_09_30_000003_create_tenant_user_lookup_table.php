<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'master';

    public function up(): void
    {
        Schema::connection('master')->create('tenant_user_lookup', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('email');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(['tenant_id', 'email']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::connection('master')->dropIfExists('tenant_user_lookup');
    }
};
