<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL may use the old composite unique index to support these foreign
        // keys, so give each key its own index before removing that constraint.
        Schema::table('site_items', function (Blueprint $table) {
            $table->index('site_id', 'site_items_site_id_index');
            $table->index('user_id', 'site_items_user_id_index');
        });

        Schema::table('site_items', function (Blueprint $table) {
            $table->dropUnique('site_items_site_id_user_id_date_type_unique');
            $table->date('app_date')->nullable()->after('date');
            $table->time('app_time')->nullable()->after('app_date');
            $table->unique(
                ['site_id', 'user_id', 'app_date', 'app_time'],
                'site_items_site_user_app_datetime_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('site_items', function (Blueprint $table) {
            $table->dropUnique('site_items_site_user_app_datetime_unique');
            $table->dropColumn(['app_date', 'app_time']);
            $table->unique(['site_id', 'user_id', 'date', 'type']);
        });
    }
};
