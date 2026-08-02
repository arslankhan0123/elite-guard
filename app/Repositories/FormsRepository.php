<?php

namespace App\Repositories;

use App\Models\Assessment;
use App\Models\DailyVehicleChecklist;
use App\Models\ShiftAdjustmentForm;
use App\Models\FireWatchReport;
use App\Models\FireWatchPatrolLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FormsRepository
{
    public function storeUserAssessments(Request $request)
    {
        $user = Auth::user();
        $signature = $this->storeAssessmentSignature(
            $request->signature,
            $user->id ?? 'guest'
        );

        $assessment = Assessment::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'worker_email' => $request->worker_email,
            'shift_date' => $request->shift_date,
            'location' => $request->location,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'client' => $request->client,
            'supervisor_first_name' => $request->supervisor_first_name,
            'supervisor_last_name' => $request->supervisor_last_name,
            'position_today' => $request->position_today,
            'compliance_fit_for_duty' => $request->compliance_fit_for_duty,
            'any_injuries' => $request->any_injuries,
            'physically_prepared' => $request->physically_prepared,
            'any_symptoms' => $request->any_symptoms,
            'understand_unethical_work_sick' => $request->understand_unethical_work_sick,
            'up_to_date_orders' => $request->up_to_date_orders,
            'believe_fit_for_duty' => $request->believe_fit_for_duty,
            'safety_concerns' => $request->safety_concerns,
            'hazards_identified' => $request->hazards_identified,
            'right_to_refuse' => $request->right_to_refuse,
            'right_to_participate' => $request->right_to_participate,
            'signature' => $signature,
        ]);

        return [
            'status' => true,
            'message' => 'Assessment stored successfully.',
            'assessment' => $assessment,
        ];
    }

    private function storeAssessmentSignature(?string $signature, int|string $userId): ?string
    {
        if (!$signature || !preg_match('/^data:image\/(png|jpeg|jpg|webp);base64,(.+)$/s', $signature, $matches)) {
            return $signature;
        }

        $image = base64_decode(preg_replace('/\s+/', '', $matches[2]), true);

        if ($image === false) {
            return $signature;
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $relativeDirectory = 'documents/Assessments/signatures';
        $directory = public_path($relativeDirectory);
        File::ensureDirectoryExists($directory);

        $fileName = $userId . '_signature_' . time() . '_' . Str::random(10) . '.' . $extension;
        File::put($directory . DIRECTORY_SEPARATOR . $fileName, $image);

        return url($relativeDirectory . '/' . $fileName);
    }

    public function storeDailyVehicleChecklist(Request $request)
    {
        $user = Auth::user();

        $documentPath = null;
        if ($request->hasFile('documents')) {
            $file = $request->file('documents');
            $fileName = $user->id . '_' . time() . '_' . Str::random(32) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('documents/DailyVehicleChecklist'), $fileName);
            $documentPath = url('documents/DailyVehicleChecklist/' . $fileName);
        }

        $checklist = DailyVehicleChecklist::create([
            'user_id' => $user->id,
            'date' => $request->date,
            'time' => $request->time,
            'vehicle' => $request->vehicle,
            'odometer_reading' => $request->odometer_reading,
            'fuel' => $request->fuel,
            'assigned_site' => $request->assigned_site,
            'driver' => $request->driver,
            'signature' => $request->signature,
            'cosmetic_issues' => $request->cosmetic_issues,
            'tires' => $request->tires,
            'windows' => $request->windows,
            'staff_care' => $request->staff_care,
            'dash_lights_gauges' => $request->dash_lights_gauges,
            'documents' => $documentPath,
            'engine' => $request->engine,
            'oil_life_percentage' => $request->oil_life_percentage,
            'equipment' => $request->equipment,
            'bwc_used_for_inspection' => $request->bwc_used_for_inspection,
        ]);

        return [
            'status' => true,
            'message' => 'Daily Vehicle Checklist stored successfully.',
            'checklist' => $checklist,
        ];
    }

    public function storeShiftAdjustmentForm(Request $request)
    {
        $user = Auth::user();

        $form = ShiftAdjustmentForm::create([
            'user_id'              => $user->id,

            // Employee Information
            'employee_name'        => $request->employee_name,
            'employee_id'          => $request->employee_id,
            'position_site'        => $request->position_site,
            'department'           => $request->department,

            // Current Shift
            'current_date'         => $request->current_date,
            'current_start_time'   => $request->current_start_time,
            'current_end_time'     => $request->current_end_time,
            'current_supervisor'   => $request->current_supervisor,
            'current_shift_type'   => $request->current_shift_type,

            // Requested Adjustment - Checkboxes
            'shift_swap'           => $request->shift_swap ?? false,
            'late_start'           => $request->late_start ?? false,
            'coverage_request'     => $request->coverage_request ?? false,
            'early_release'        => $request->early_release ?? false,
            'time_off_request'     => $request->time_off_request ?? false,
            'overtime_approval'    => $request->overtime_approval ?? false,

            // Requested Adjustment - Details
            'requested_date'       => $request->requested_date,
            'requested_start_time' => $request->requested_start_time,
            'requested_end_time'   => $request->requested_end_time,
            'replacement_employee' => $request->replacement_employee,
            'adjustment_reason'    => $request->adjustment_reason,
            'additional_details'   => $request->additional_details,

            // Approval Section
            'supervisor_name'      => $request->supervisor_name,
            'approval_date'        => $request->approval_date,
            'decision'             => $request->decision,
            'approved_hours'       => $request->approved_hours,
            'supervisor_notes'     => $request->supervisor_notes,

            // Signatures
            'employee_signature'   => $request->employee_signature,
            'supervisor_signature' => $request->supervisor_signature,
        ]);

        return [
            'status'  => true,
            'message' => 'Shift Adjustment Form stored successfully.',
            'form'    => $form,
        ];
    }
}
