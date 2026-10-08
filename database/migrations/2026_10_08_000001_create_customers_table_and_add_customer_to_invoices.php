<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('company_name')->nullable();
                $table->string('tax_id')->nullable();
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('province')->nullable();
                $table->string('postal_code', 30)->nullable();
                $table->string('country')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('invoices') && ! Schema::hasColumn('invoices', 'customer_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreignId('customer_id')->nullable()->after('company_id')
                    ->constrained('customers')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'customer_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropConstrainedForeignId('customer_id');
            });
        }

        Schema::dropIfExists('customers');
    }
};
