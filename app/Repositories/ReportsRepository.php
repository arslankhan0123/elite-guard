<?php

namespace App\Repositories;

use App\Models\ReportDailyShiftForm;
use App\Models\ReportDailyShiftFormPatrolEntry;
use App\Models\ReportGeneralForm;
use App\Models\ReportGeneralFormImage;
use App\Models\ReportIncidentForm;
use App\Models\ReportIncidentFormImage;
use App\Models\ReportSecurityGuardDisciplinaryForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReportsRepository
{
    public function storeSecurityGuardDisciplinaryForm($request)
    {
        $user = Auth::user();

        $form = ReportSecurityGuardDisciplinaryForm::create([
            'user_id' => $user->id ?? null,
            'employee_name' => $request->employee_name,
            'employee_id_license' => $request->employee_id_license,
            'site_property' => $request->site_property,
            'warning_date' => $request->warning_date,
            'supervisor' => $request->supervisor,
            'shift_time' => $request->shift_time,
            'department_client' => $request->department_client,
            'reference_number' => $request->reference_number,
            'violation_type' => $request->violation_type,
            'classification_severity' => $request->classification_severity,
            'classification_severity_other' => $request->classification_severity_other,
            'incident_date' => $request->incident_date,
            'incident_time' => $request->incident_time,
            'location' => $request->location,
            'reported_by' => $request->reported_by,
            'incident_summary' => $request->incident_summary,
            'corrective_action' => $request->corrective_action,
            'action_taken' => $request->action_taken,
            'issued_by' => $request->issued_by,
            'issued_by_title' => $request->issued_by_title,
            'employee_signature' => $request->employee_signature,
            'signature_date' => $request->signature_date,
        ]);

        return [
            'status' => true,
            'message' => 'Security Guard Disciplinary Form stored successfully.',
            'form' => $form,
        ];
    }

    public function storeIncidentReportForm($request)
    {
        $user = Auth::user();

        $form = ReportIncidentForm::create([
            'user_id' => $user->id ?? null,
            'date_of_report' => $request->date_of_report,
            'time_of_report' => $request->time_of_report,
            'location' => $request->location,
            'property' => $request->property,
            'incident_type' => $request->incident_type,
            'reported_by' => $request->reported_by,
            'responding_authority' => $request->responding_authority,
            'cps_case_number' => $request->cps_case_number,
            'incident_report' => $request->incident_report,
            'subject_description' => $request->subject_description,
            'outcome' => $request->outcome,
            'reported_by_name' => $request->reported_by_name,
            'reported_by_title' => $request->reported_by_title,
            'reviewed_by_name' => $request->reviewed_by_name,
            'reviewed_by_title' => $request->reviewed_by_title,
        ]);

        if ($request->has('images') && is_array($request->images)) {
            foreach ($request->file('images') as $image) {
                $fileName = ($user->id ?? 'guest') . '_' . time() . '_' . Str::random(20) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('documents/IncidentReport'), $fileName);
                $imagePath = url('documents/IncidentReport/' . $fileName);
                ReportIncidentFormImage::create([
                    'report_incident_form_id' => $form->id,
                    'image_path' => $imagePath,
                ]);
            }
        }

        return [
            'status' => true,
            'message' => 'Incident Report Form stored successfully.',
            'form' => $form->load('images'),
        ];
    }

    public function storeGeneralReportForm($request)
    {
        // dd($request->all());
        $user = Auth::user();

        $form = ReportGeneralForm::create([
            'user_id' => $user->id ?? null,
            'report_date' => $request->report_date,
            'report_time' => $request->report_time,
            'property_location' => $request->property_location,
            'property_name' => $request->property_name,
            'reported_by' => $request->reported_by,
            'report_type' => $request->report_type,
            'time_engaged' => $request->time_engaged,
            'time_area_cleared' => $request->time_area_cleared,
            'location_of_incident' => $request->location_of_incident,
            'observation_situation' => $request->observation_situation,
            'action_taken' => $request->action_taken,
            'signature' => $request->signature,
        ]);

        $observationImages = $request->file('observation_image_path', []);
        $clearedImages = $request->file('cleared_area_image_path', []);

        // max count nikal lo (jo zyada ho)
        $max = max(count($observationImages), count($clearedImages));

        for ($i = 0; $i < $max; $i++) {

            $observationPath = null;
            $clearedPath = null;

            // ✅ Observation Image
            if (isset($observationImages[$i])) {
                $file = $observationImages[$i];

                $fileName = ($user->id ?? 'guest') . '_obs_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

                $file->move(public_path('documents/GeneralReport'), $fileName);

                $observationPath = url('documents/GeneralReport/' . $fileName);
            }

            // ✅ Cleared Image
            if (isset($clearedImages[$i])) {
                $file = $clearedImages[$i];

                $fileName = ($user->id ?? 'guest') . '_clr_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

                $file->move(public_path('documents/GeneralReport'), $fileName);

                $clearedPath = url('documents/GeneralReport/' . $fileName);
            }

            // ✅ Save only if at least one exists
            if ($observationPath || $clearedPath) {
                ReportGeneralFormImage::create([
                    'report_general_form_id' => $form->id,
                    'observation_image_path' => $observationPath,
                    'cleared_area_image_path' => $clearedPath,
                ]);
            }
        }

        return [
            'status' => true,
            'message' => 'General Report Form stored successfully.',
            'form' => $form->load('images'),
        ];
    }

    public function storeDailyShiftReportForm($request)
    {
        $user = Auth::user();

        $form = ReportDailyShiftForm::create([
            'user_id'          => $user->id ?? null,
            'shift_id'         => $request->shift_id,
            'security_company' => $request->security_company,
            'security_guard'   => $request->security_guard,
            'date'             => $request->date,
            'shift_time'       => $request->shift_time,
            'location'         => $request->location,
            'client'           => $request->client,
        ]);

        if ($request->has('patrol_entries') && is_array($request->patrol_entries)) {
            foreach ($request->patrol_entries as $entry) {
                ReportDailyShiftFormPatrolEntry::create([
                    'report_daily_shift_form_id' => $form->id,
                    'time_range'                => $entry['time_range'] ?? null,
                    'summary'                   => $entry['summary'] ?? null,
                ]);
            }
        }

        return [
            'status' => true,
            'message' => 'Daily Shift Report Form stored successfully.',
            'form' => $form->load('patrolEntries'),
        ];
    }
}
