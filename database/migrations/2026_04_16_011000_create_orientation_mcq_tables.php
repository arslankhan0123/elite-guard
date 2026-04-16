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
        // 1. Update Orientations Table
        Schema::table('orientations', function (Blueprint $table) {
            $table->integer('passing_percentage')->default(80)->after('status');
        });

        // 2. Create Orientation Questions Table
        Schema::create('orientation_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orientation_id');
            $table->text('question_text');
            $table->timestamps();

            $table->foreign('orientation_id')->references('id')->on('orientations')->onDelete('cascade');
        });

        // 3. Create Orientation Options Table
        Schema::create('orientation_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->foreign('question_id')->references('id')->on('orientation_questions')->onDelete('cascade');
        });

        // 4. Create Signed Orientation Answers Table
        Schema::create('signed_orientation_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('signed_orientation_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('option_id');
            $table->boolean('is_correct');
            $table->timestamps();

            $table->foreign('signed_orientation_id')->references('id')->on('signed_orientations')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('orientation_questions')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('orientation_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signed_orientation_answers');
        Schema::dropIfExists('orientation_options');
        Schema::dropIfExists('orientation_questions');
        Schema::table('orientations', function (Blueprint $table) {
            $table->dropColumn('passing_percentage');
        });
    }
};
