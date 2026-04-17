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
        Schema::table('employee_bank_details', function (Blueprint $table) {
            $table->longText('void_cheque_file')->nullable()->change();
        });

        Schema::table('employee_license_details', function (Blueprint $table) {
            $table->longText('security_license_file')->nullable()->change();
            $table->longText('drivers_license_file')->nullable()->change();
            $table->longText('work_eligibility_file')->nullable()->change();
            $table->longText('other_documents_file')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_bank_details', function (Blueprint $table) {
            $table->string('void_cheque_file')->nullable()->change();
        });

        Schema::table('employee_license_details', function (Blueprint $table) {
            $table->string('security_license_file')->nullable()->change();
            $table->string('drivers_license_file')->nullable()->change();
            $table->string('work_eligibility_file')->nullable()->change();
            $table->string('other_documents_file')->nullable()->change();
        });
    }
};
