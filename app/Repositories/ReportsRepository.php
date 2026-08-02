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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\FireWatchReport;
use App\Models\FireWatchPatrolLog;

class ReportsRepository
{
    public function storeSecurityGuardDisciplinaryForm($request)
    {
        $user = Auth::user();
        $employeeSignature = $this->storeUploadedImage(
            $request->file('employee_signature'),
            'documents/DisciplinaryReport/signatures',
            $user->id ?? 'guest',
            'employee_signature'
        );

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
            'reported_by_id' => $request->reported_by_id,
            'incident_summary' => $request->incident_summary,
            'corrective_action' => $request->corrective_action,
            'action_taken' => $request->action_taken,
            'issued_by' => $request->issued_by,
            'issued_by_title' => $request->issued_by_title,
            'employee_signature' => $employeeSignature,
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
            'date_of_incident' => $request->date_of_incident,
            'time_of_incident' => $request->time_of_incident,
            'location' => $request->location,
            'property' => $request->property,
            'property_name' => $request->property_name,
            'property_location' => $request->property_location,
            'incident_location' => $request->incident_location,
            'incident_type' => $request->incident_type,
            'reported_by' => $request->reported_by,
            'reported_by_id' => $request->reported_by_id,
            'reporting_guard_name' => $request->reporting_guard_name,
            'employee_id' => $request->employee_id,
            'responding_authority' => $request->responding_authority,
            'responding_authority_case_number' => $request->responding_authority_case_number,
            'supervisor_notified' => $request->supervisor_notified,
            'cps_case_number' => $request->cps_case_number,
            'incident_report' => $request->incident_report,
            'action_taken' => $request->action_taken,
            'evidence_observed' => $request->evidence_observed,
            'subjects' => is_string($request->subjects)
                ? json_decode($request->subjects, true)
                : $request->subjects,
            'subject_description' => $request->subject_description,
            'outcome' => $request->outcome,
            'reported_by_name' => $request->reported_by_name,
            'reported_by_title' => $request->reported_by_title,
            'reviewed_by_name' => $request->reviewed_by_name,
            'reviewed_by_title' => $request->reviewed_by_title,
        ]);

        $images = $request->file('evidence_images', $request->file('images', []));

        if (is_array($images)) {
            foreach ($images as $image) {
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
        $signature = $this->storeUploadedImage(
            $request->file('signature'),
            'documents/GeneralReport/signatures',
            $user->id ?? 'guest',
            'signature'
        );

        $form = ReportGeneralForm::create([
            'user_id' => $user->id ?? null,
            'report_date' => $request->report_date,
            'report_time' => $request->report_time,
            'property_location' => $request->property_location,
            'property_name' => $request->property_name,
            'property' => $request->property,
            'property_address' => $request->property_address,
            'reported_by' => $request->reported_by ?? $user->name ?? 'No Username',
            'reported_by_id' => $request->reported_by_id,
            'report_type' => $request->report_type,
            'time_engaged' => $request->time_engaged,
            'time_area_cleared' => $request->time_area_cleared,
            'location_of_incident' => $request->location_of_incident,
            'location' => $request->location,
            'location_of_report' => $request->location_of_report,
            'observation_situation' => $request->observation_situation,
            'action_taken' => $request->action_taken,
            'signature' => $signature,
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

    private function storeUploadedImage(
        $image,
        string $relativeDirectory,
        int|string $userId,
        string $name
    ): ?string
    {
        if (!$image) {
            return null;
        }

        $extension = $image->extension();
        $extension = $extension === 'jpeg' ? 'jpg' : $extension;
        $directory = public_path($relativeDirectory);
        File::ensureDirectoryExists($directory);

        $fileName = $userId . '_' . $name . '_' . time() . '_' . Str::random(10) . '.' . $extension;
        $image->move($directory, $fileName);

        return url(trim($relativeDirectory, '/') . '/' . $fileName);
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
            'weather_conditions' => $request->weather_conditions,
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

        public function storeFireWatchReport(Request $request)
    {
        $user = Auth::user();

        return DB::transaction(function () use ($request, $user) {
            $report = FireWatchReport::create([
                'user_id'               => $user->id,
                'client_site_name'      => $request->client_site_name,
                'address_location'      => $request->address_location,
                'reason_for_fire_watch' => $request->reason_for_fire_watch,
                'fire_watch_areas'      => $request->fire_watch_areas,
                'commenced_date'        => $request->commenced_date,
                'commenced_time'        => $request->commenced_time,
                'terminated_date'       => $request->terminated_date,
                'terminated_time'       => $request->terminated_time,
                'guards'                => $request->guards,
                'supervisor'            => $request->supervisor,
                'patrol_interval'       => $request->patrol_interval,
            ]);

            if ($request->has('patrol_logs') && is_array($request->patrol_logs)) {
                foreach ($request->patrol_logs as $log) {
                    FireWatchPatrolLog::create([
                        'fire_watch_report_id'    => $report->id,
                        'round'                   => $log['round'] ?? null,
                        'date'                    => $log['date'] ?? null,
                        'start_time'              => $log['start_time'] ?? null,
                        'end_time'                => $log['end_time'] ?? null,
                        'area_patrolled_findings' => $log['area_patrolled_findings'] ?? null,
                        'initials'                => $log['initials'] ?? null,
                    ]);
                }
            }

            return [
                'status'  => true,
                'message' => 'Fire Watch Report stored successfully.',
                'report'  => $report->load('patrolLogs'),
            ];
        });
    }
}
