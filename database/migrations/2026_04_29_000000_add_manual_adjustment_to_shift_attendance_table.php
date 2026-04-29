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
        Schema::table('shift_attendance', function (Blueprint $table) {
            $table->integer('manual_adjustment')->default(0)->after('status')->comment('Adjustment in minutes');
            $table->text('admin_note')->nullable()->after('manual_adjustment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_attendance', function (Blueprint $table) {
            $table->dropColumn(['manual_adjustment', 'admin_note']);
        });
    }
};
