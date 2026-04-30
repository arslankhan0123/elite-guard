<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\FormsRepository;
use App\Repositories\NumberRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class FormsApiController extends Controller
{
    use ApiResponser;
    protected $formsRepo;

    public function __construct(FormsRepository $formsRepo)
    {
        $this->formsRepo = $formsRepo;
    }

    public function storeUserAssessments(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'worker_email' => 'required|email',
            'shift_date' => 'required|date',
            'location' => 'required|string',
            'start_time' => 'required|string',
            'end_time' => 'nullable|string',
            'compliance_fit_for_duty' => 'required|boolean',
            'any_injuries' => 'required|boolean',
            'physically_prepared' => 'required|boolean',
            'any_symptoms' => 'required|boolean',
            'understand_unethical_work_sick' => 'required|boolean',
            'up_to_date_orders' => 'required|boolean',
            'believe_fit_for_duty' => 'required|boolean',
        ]);

        $data = $this->formsRepo->storeUserAssessments($request);

        return $this->successResponse($data, 'Assessment stored successfully.');
    }
}
