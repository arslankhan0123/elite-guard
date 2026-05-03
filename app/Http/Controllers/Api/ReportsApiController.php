<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\FormsRepository;
use App\Repositories\NumberRepository;
use App\Repositories\ReportsRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportsApiController extends Controller
{
    use ApiResponser;
    protected $reportsRepo;

    public function __construct(ReportsRepository $reportsRepo)
    {
        $this->reportsRepo = $reportsRepo;
    }

    public function storeSecurityGuardDisciplinaryForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_name' => 'required',
            'employee_id_license' => 'required',
            'site_property' => 'required',
            'warning_date' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 401);
        }
        $data = $this->reportsRepo->storeSecurityGuardDisciplinaryForm($request);
        return $this->successResponse($data, 'Disciplinary form stored successfully.');
    }

    public function storeIncidentReportForm(Request $request)
    {
        // Validation rules for the incident report form
        $validator = Validator::make($request->all(), [
            'date_of_report' => 'required',
            'time_of_report' => 'required',
            'location' => 'required',
            'property' => 'required',
            'incident_type' => 'required',
            'reported_by' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 401);
        }

        // Store the incident report using the repository
        $data = $this->reportsRepo->storeIncidentReportForm($request);
        return $this->successResponse($data, 'Incident report stored successfully.');
    }

    public function storeGeneralReportForm(Request $request)
    {
        // Validation rules for the general report form
        $validator = Validator::make($request->all(), [
            'report_date' => 'required',
            'report_time' => 'required',
            'property_location' => 'required',
            'property_name' => 'required',
            'reported_by' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 401);
        }

        // Store the general report using the repository
        $data = $this->reportsRepo->storeGeneralReportForm($request);
        return $this->successResponse($data, 'General report stored successfully.');
    }

    public function storeDailyShiftReportForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shift_id'         => 'required|exists:shifts,id',
            'security_company' => 'required',
            'security_guard'   => 'required',
            'date'             => 'required|date',
            'shift_time'       => 'required',
            'location'         => 'required',
            'client'           => 'required',
            'patrol_entries'           => 'nullable|array',
            'patrol_entries.*.time_range' => 'required|string',
            'patrol_entries.*.summary'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()
            ], 401);
        }

        $data = $this->reportsRepo->storeDailyShiftReportForm($request);
        return $this->successResponse($data, 'Daily shift report stored successfully.');
    }
}
