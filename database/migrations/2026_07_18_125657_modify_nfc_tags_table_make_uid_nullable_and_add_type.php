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
        Schema::table('nfc_tags', function (Blueprint $table) {
            $table->string('uid')->nullable()->change();
            $table->string('type')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nfc_tags', function (Blueprint $table) {
            $table->string('uid')->nullable(false)->change();
            $table->dropColumn('type');
        });
    }
};
