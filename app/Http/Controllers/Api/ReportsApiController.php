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

    /**
     * @OA\Post(
     *     path="/api/reports/security-guard-disciplinary-form/store",
     *     summary="Store Security Guard Disciplinary Form",
     *     tags={"Reports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"employee_name","employee_id_license","site_property","warning_date"},
     *             @OA\Property(property="employee_name", type="string", example="John Doe"),
     *             @OA\Property(property="employee_id_license", type="string", example="LIC123456"),
     *             @OA\Property(property="site_property", type="string", example="Downtown Mall"),
     *             @OA\Property(property="warning_date", type="string", format="date", example="2026-05-03"),
     *             @OA\Property(property="supervisor", type="string", example="Super Visor"),
     *             @OA\Property(property="shift_time", type="string", example="08:00 - 16:00"),
     *             @OA\Property(property="department_client", type="string", example="Main Office"),
     *             @OA\Property(property="reference_number", type="string", example="REF789"),
     *             @OA\Property(property="violation_type", type="string", example="Attendance"),
     *             @OA\Property(property="classification_severity", type="string", example="Minor"),
     *             @OA\Property(property="classification_severity_other", type="string", example="N/A"),
     *             @OA\Property(property="incident_date", type="string", format="date", example="2026-05-02"),
     *             @OA\Property(property="incident_time", type="string", example="08:30"),
     *             @OA\Property(property="location", type="string", example="Gate 1"),
     *             @OA\Property(property="reported_by", type="string", example="Guard A"),
     *             @OA\Property(property="incident_summary", type="string", example="Arrived late..."),
     *             @OA\Property(property="corrective_action", type="string", example="Verbal warning"),
     *             @OA\Property(property="action_taken", type="string", example="Logged"),
     *             @OA\Property(property="issued_by", type="string", example="Captain B"),
     *             @OA\Property(property="issued_by_title", type="string", example="Site Supervisor"),
     *             @OA\Property(property="employee_signature", type="string", example="Digital Signature"),
     *             @OA\Property(property="signature_date", type="string", format="date", example="2026-05-03")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Disciplinary form stored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Disciplinary form stored successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/reports/incident-report-form/store",
     *     summary="Store Incident Report Form",
     *     tags={"Reports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"date_of_report","time_of_report","location","property","incident_type","reported_by"},
     *                 @OA\Property(property="date_of_report", type="string", format="date", example="2026-05-03"),
     *                 @OA\Property(property="time_of_report", type="string", example="14:30"),
     *                 @OA\Property(property="location", type="string", example="Main Entrance"),
     *                 @OA\Property(property="property", type="string", example="Elite Plaza"),
     *                 @OA\Property(property="incident_type", type="string", example="Theft"),
     *                 @OA\Property(property="reported_by", type="string", example="Guard Smith"),
     *                 @OA\Property(property="responding_authority", type="string", example="Local Police"),
     *                 @OA\Property(property="cps_case_number", type="string", example="CPS-123"),
     *                 @OA\Property(property="incident_report", type="string", example="Detailed description of the incident..."),
     *                 @OA\Property(property="subject_description", type="string", example="Height 6ft, wearing blue jacket..."),
     *                 @OA\Property(property="outcome", type="string", example="Subject apprehended"),
     *                 @OA\Property(property="reported_by_name", type="string", example="Officer John"),
     *                 @OA\Property(property="reported_by_title", type="string", example="Security Guard"),
     *                 @OA\Property(property="reviewed_by_name", type="string", example="Supervisor Jane"),
     *                 @OA\Property(property="reviewed_by_title", type="string", example="Manager"),
     *                 @OA\Property(
     *                     property="images[]",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Incident report stored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Incident report stored successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/reports/general-report-form/store",
     *     summary="Store General Report Form",
     *     tags={"Reports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"report_date","report_time","property_location","property_name","reported_by"},
     *                 @OA\Property(property="report_date", type="string", format="date", example="2026-05-03"),
     *                 @OA\Property(property="report_time", type="string", example="09:00"),
     *                 @OA\Property(property="property_location", type="string", example="Sector 7"),
     *                 @OA\Property(property="property_name", type="string", example="Industrial Complex"),
     *                 @OA\Property(property="reported_by", type="string", example="John Doe"),
     *                 @OA\Property(property="report_type", type="string", example="Maintenance"),
     *                 @OA\Property(property="time_engaged", type="string", example="08:45"),
     *                 @OA\Property(property="time_area_cleared", type="string", example="09:15"),
     *                 @OA\Property(property="location_of_incident", type="string", example="Warehouse B"),
     *                 @OA\Property(property="observation_situation", type="string", example="Broken lock observed."),
     *                 @OA\Property(property="action_taken", type="string", example="Secured with temporary chain."),
     *                 @OA\Property(property="signature", type="string", example="John's Signature"),
     *                 @OA\Property(
     *                     property="observation_image_path[]",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary")
     *                 ),
     *                 @OA\Property(
     *                     property="cleared_area_image_path[]",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="General report stored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="General report stored successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/reports/daily-shift-report-form/store",
     *     summary="Store Daily Shift Report Form",
     *     tags={"Reports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"shift_id","security_company","security_guard","date","shift_time","location","client"},
     *             @OA\Property(property="shift_id", type="integer", example=1),
     *             @OA\Property(property="security_company", type="string", example="Elite Guarding"),
     *             @OA\Property(property="security_guard", type="string", example="Officer Smith"),
     *             @OA\Property(property="date", type="string", format="date", example="2026-05-03"),
     *             @OA\Property(property="shift_time", type="string", example="08:00 - 16:00"),
     *             @OA\Property(property="location", type="string", example="North Gate"),
     *             @OA\Property(property="client", type="string", example="ABC Corp"),
     *             @OA\Property(
     *                 property="patrol_entries",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="time_range", type="string", example="08:00 - 09:00"),
     *                     @OA\Property(property="summary", type="string", example="Initial patrol completed.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daily shift report stored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Daily shift report stored successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function storeDailyShiftReportForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shift_id' => 'required|exists:shifts,id',
            'security_company' => 'required',
            'security_guard' => 'required',
            'date' => 'required|date',
            'shift_time' => 'required',
            'location' => 'required',
            'client' => 'required',
            'patrol_entries' => 'nullable|array',
            'patrol_entries.*.time_range' => 'required|string',
            'patrol_entries.*.summary' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 401);
        }

        $data = $this->reportsRepo->storeDailyShiftReportForm($request);
        return $this->successResponse($data, 'Daily shift report stored successfully.');
    }
}
