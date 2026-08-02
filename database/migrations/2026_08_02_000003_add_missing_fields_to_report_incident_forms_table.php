<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_incident_forms', function (Blueprint $table) {
            $table->date('date_of_incident')->nullable()->after('time_of_report');
            $table->string('time_of_incident')->nullable()->after('date_of_incident');
            $table->string('property_name')->nullable()->after('property');
            $table->string('property_location')->nullable()->after('property_name');
            $table->string('incident_location')->nullable()->after('property_location');
            $table->string('reporting_guard_name')->nullable()->after('reported_by');
            $table->unsignedBigInteger('reported_by_id')->nullable()->after('reporting_guard_name');
            $table->string('employee_id')->nullable()->after('reported_by_id');
            $table->string('responding_authority_case_number')->nullable()->after('responding_authority');
            $table->string('supervisor_notified')->nullable()->after('responding_authority_case_number');
            $table->text('action_taken')->nullable()->after('incident_report');
            $table->text('evidence_observed')->nullable()->after('action_taken');
            $table->json('subjects')->nullable()->after('evidence_observed');
        });
    }

    public function down(): void
    {
        Schema::table('report_incident_forms', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_incident',
                'time_of_incident',
                'property_name',
                'property_location',
                'incident_location',
                'reporting_guard_name',
                'reported_by_id',
                'employee_id',
                'responding_authority_case_number',
                'supervisor_notified',
                'action_taken',
                'evidence_observed',
                'subjects',
            ]);
        });
    }
};
