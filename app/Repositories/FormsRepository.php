<?php

namespace App\Repositories;

use App\Models\Assessment;
use Illuminate\Http\Request;

class FormsRepository
{
    public function storeUserAssessments(Request $request)
    {
        $user = auth()->user();

        $assessment = Assessment::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'worker_email' => $request->worker_email,
            'shift_date' => $request->shift_date,
            'location' => $request->location,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'compliance_fit_for_duty' => $request->compliance_fit_for_duty,
            'any_injuries' => $request->any_injuries,
            'physically_prepared' => $request->physically_prepared,
            'any_symptoms' => $request->any_symptoms,
            'understand_unethical_work_sick' => $request->understand_unethical_work_sick,
            'up_to_date_orders' => $request->up_to_date_orders,
            'believe_fit_for_duty' => $request->believe_fit_for_duty,
        ]);

        return [
            'status' => true,
            'message' => 'Assessment stored successfully.',
            'assessment' => $assessment,
        ];
    }
}
