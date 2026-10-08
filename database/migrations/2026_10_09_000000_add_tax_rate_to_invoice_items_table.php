<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('tax_rate', 8, 4)->default(0)->after('tax')
                  ->comment('Tax percentage rate (e.g. 13.00 for 13%). Tax dollar amount = (qty * rate) * (tax_rate / 100)');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('tax_rate');
        });
    }
};
