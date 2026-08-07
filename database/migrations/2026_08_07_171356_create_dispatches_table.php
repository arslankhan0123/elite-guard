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
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->foreignId('assigned_guard_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('priority')->default('Medium'); // Low, Medium, High, Emergency
            $table->string('caller_type')->default('Client'); // Client, Guard, Emergency Services, Other
            $table->string('caller_name');
            $table->string('incident_location');
            $table->string('incident_type');
            $table->date('incident_date');
            $table->time('incident_time');
            $table->text('incident_details');
            $table->text('action_taken')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('status')->default('Pending'); // Pending, In Progress, Completed, Cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatches');
    }
};
