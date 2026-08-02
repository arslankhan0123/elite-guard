<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('daily_vehicle_checklists', 'issues_found')) {
            Schema::table('daily_vehicle_checklists', function (Blueprint $table) {
                $table->text('issues_found')->nullable()->after('bwc_used_for_inspection');
            });
        }

        if (!Schema::hasTable('daily_vehicle_checklist_images')) {
            Schema::create('daily_vehicle_checklist_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('daily_vehicle_checklist_id');
                $table->text('image_path');
                $table->timestamps();
                $table->foreign('daily_vehicle_checklist_id', 'dvci_checklist_id_foreign')
                    ->references('id')
                    ->on('daily_vehicle_checklists')
                    ->onDelete('cascade');
            });
        } else {
            Schema::table('daily_vehicle_checklist_images', function (Blueprint $table) {
                $table->foreign('daily_vehicle_checklist_id', 'dvci_checklist_id_foreign')
                    ->references('id')
                    ->on('daily_vehicle_checklists')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_vehicle_checklist_images');

        Schema::table('daily_vehicle_checklists', function (Blueprint $table) {
            $table->dropColumn('issues_found');
        });
    }
};
