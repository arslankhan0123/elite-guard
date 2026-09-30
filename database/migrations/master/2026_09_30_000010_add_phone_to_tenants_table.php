<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'master';

    public function up(): void
    {
        Schema::connection('master')->table('tenants', function (Blueprint $table) {
            $table->string('phone', 50)->nullable()->after('admin_email');
        });
    }

    public function down(): void
    {
        Schema::connection('master')->table('tenants', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};
