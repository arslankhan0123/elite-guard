<?php

namespace App\Repositories;

use App\Models\Assessment;
use App\Models\DailyVehicleChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FormsRepository
{
    public function storeUserAssessments(Request $request)
    {
        $user = Auth::user();

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
            'signature' => $request->signature,
        ]);

        return [
            'status' => true,
            'message' => 'Assessment stored successfully.',
            'assessment' => $assessment,
        ];
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
}
