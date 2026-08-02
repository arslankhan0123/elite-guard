<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\FormsRepository;
use App\Repositories\NumberRepository;
use App\Repositories\ReportsRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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
     *             @OA\Property(property="reported_by_id", type="integer", nullable=true, example=7),
     *             @OA\Property(property="incident_summary", type="string", example="Arrived late..."),
     *             @OA\Property(property="corrective_action", type="string", example="Verbal warning"),
     *             @OA\Property(property="action_taken", type="string", example="Logged"),
     *             @OA\Property(property="issued_by", type="string", example="Captain B"),
     *             @OA\Property(property="issued_by_title", type="string", example="Site Supervisor"),
     *             @OA\Property(property="employee_signature", type="string", description="Base64 data URI of the employee signature image", example="data:image/png;base64,iVBORw0KGgo..."),
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
        Log::info('storeSecurityGuardDisciplinaryForm called with data: ' . json_encode($request->all()));
        $validator = Validator::make($request->all(), [
            'employee_name' => 'required',
            'employee_id_license' => 'required',
            'site_property' => 'required',
            'warning_date' => 'required',
            'reported_by_id' => 'nullable|integer',
            'employee_signature' => 'nullable|string',
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
        Log::info('storeIncidentReportForm called with data: ' . json_encode($request->all()));
        // Validation rules for the incident report form
        $validator = Validator::make($request->all(), [
            'date_of_report' => 'required',
            'time_of_report' => 'required',
            'location' => 'required',
            'property' => 'required',
            'incident_type' => 'required',
            'reported_by' => 'required',
            'date_of_incident' => 'nullable|date',
            'time_of_incident' => 'nullable|string',
            'property_name' => 'nullable|string',
            'property_location' => 'nullable|string',
            'incident_location' => 'nullable|string',
            'reporting_guard_name' => 'nullable|string',
            'employee_id' => 'nullable|string',
            'responding_authority_case_number' => 'nullable|string',
            'supervisor_notified' => 'nullable|string',
            'action_taken' => 'nullable|string',
            'evidence_observed' => 'nullable|string',
            'subjects' => 'nullable',
            'reported_by_id' => 'nullable|integer',
            'evidence_images' => 'nullable|array',
            'evidence_images.*' => 'file|image',
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
     *                 @OA\Property(property="property", type="string", nullable=true, example="Industrial Complex"),
     *                 @OA\Property(property="property_address", type="string", nullable=true, example="123 Main Street"),
     *                 @OA\Property(property="reported_by", type="string", example="John Doe"),
     *                 @OA\Property(property="reported_by_id", type="integer", nullable=true, example=2),
     *                 @OA\Property(property="report_type", type="string", example="Maintenance"),
     *                 @OA\Property(property="time_engaged", type="string", example="08:45"),
     *                 @OA\Property(property="time_area_cleared", type="string", example="09:15"),
     *                 @OA\Property(property="location_of_incident", type="string", example="Warehouse B"),
     *                 @OA\Property(property="location", type="string", nullable=true, example="123 Main Street"),
     *                 @OA\Property(property="location_of_report", type="string", nullable=true, example="123 Main Street"),
     *                 @OA\Property(property="observation_situation", type="string", example="Broken lock observed."),
     *                 @OA\Property(property="action_taken", type="string", example="Secured with temporary chain."),
     *                 @OA\Property(property="signature", type="string", description="Base64 data URI of the signature image", example="data:image/png;base64,iVBORw0KGgo..."),
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
        Log::info('storeGeneralReportForm called with data: ' . json_encode($request->all()));
        // Validation rules for the general report form
        $validator = Validator::make($request->all(), [
            'report_date' => 'required',
            'report_time' => 'required',
            'property_location' => 'required',
            'property_name' => 'required',
            'reported_by' => 'nullable',
            'reported_by_id' => 'nullable|integer',
            'location' => 'nullable|string',
            'location_of_report' => 'nullable|string',
            'property' => 'nullable|string',
            'property_address' => 'nullable|string',
            'signature' => 'nullable|string',
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
     *             @OA\Property(property="weather_conditions", type="string", nullable=true, example="Clear and sunny"),
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
        Log::info('storeDailyShiftReportForm called with data: ' . json_encode($request->all()));
        $validator = Validator::make($request->all(), [
            'shift_id' => 'required|exists:shifts,id',
            'security_company' => 'required',
            'security_guard' => 'required',
            'date' => 'required|date',
            'shift_time' => 'required',
            'location' => 'required',
            'client' => 'required',
            'weather_conditions' => 'nullable|string',
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

    /**
     * @OA\Post(
     *     path="/api/reports/fire-watch/store",
     *     summary="Store Fire Watch Report",
     *     description="Submit a Fire Watch Report Form with site details, reason, patrol areas, commencement/termination details, interval, and check logs.",
     *     tags={"Reports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"client_site_name","address_location","commenced_date","commenced_time","terminated_date","terminated_time"},
     *             @OA\Property(property="client_site_name", type="string", example="Elite Plaza"),
     *             @OA\Property(property="address_location", type="string", example="123 Main St, New York, NY"),
     *             @OA\Property(property="reason_for_fire_watch", type="string", example="Broken sprinkler system on 3rd floor"),
     *             @OA\Property(property="fire_watch_areas", type="string", example="3rd and 4th Floor hallways"),
     *             @OA\Property(property="commenced_date", type="string", example="2026-07-03"),
     *             @OA\Property(property="commenced_time", type="string", example="15:30"),
     *             @OA\Property(property="terminated_date", type="string", example="2026-07-03"),
     *             @OA\Property(property="terminated_time", type="string", example="16:00"),
     *             @OA\Property(property="guards", type="string", example="John Doe, Jane Smith"),
     *             @OA\Property(property="supervisor", type="string", example="Supervisor Mark"),
     *             @OA\Property(property="patrol_interval", type="string", example="30 Minutes", enum={"30 Minutes","60 Minutes","Continuous"}),
     *             @OA\Property(
     *                 property="patrol_logs",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="round", type="string", example="Round 1"),
     *                     @OA\Property(property="date", type="string", example="08/16"),
     *                     @OA\Property(property="start_time", type="string", example="15:30"),
     *                     @OA\Property(property="end_time", type="string", example="16:00"),
     *                     @OA\Property(property="area_patrolled_findings", type="string", example="All Clear"),
     *                     @OA\Property(property="initials", type="string", example="JD")
     *                 ),
     *                 example={
     *                     {
     *                         "round": "Round 1",
     *                         "date": "08/16",
     *                         "start_time": "15:30",
     *                         "end_time": "16:00",
     *                         "area_patrolled_findings": "All Clear",
     *                         "initials": "JD"
     *                     }
     *                 }
     *             ),
     *             example={
     *                 "client_site_name": "Elite Plaza",
     *                 "address_location": "123 Main St, New York, NY",
     *                 "reason_for_fire_watch": "Broken sprinkler system on 3rd floor",
     *                 "fire_watch_areas": "3rd and 4th Floor hallways",
     *                 "commenced_date": "2026-07-03",
     *                 "commenced_time": "15:30",
     *                 "terminated_date": "2026-07-03",
     *                 "terminated_time": "16:00",
     *                 "guards": "John Doe, Jane Smith",
     *                 "supervisor": "Supervisor Mark",
     *                 "patrol_interval": "30 Minutes",
     *                 "patrol_logs": {
     *                     {
     *                         "round": "Round 1",
     *                         "date": "08/16",
     *                         "start_time": "15:30",
     *                         "end_time": "16:00",
     *                         "area_patrolled_findings": "All Clear",
     *                         "initials": "JD"
     *                     }
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Fire Watch Report stored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Fire Watch Report stored successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function storeFireWatchReport(Request $request)
    {
        Log::info('storeFireWatchReport called with data: ' . json_encode($request->all()));
        $request->validate([
            'client_site_name'      => 'required|string|max:255',
            'address_location'      => 'required|string|max:255',
            'reason_for_fire_watch' => 'nullable|string',
            'fire_watch_areas'      => 'nullable|string',
            'commenced_date'        => 'required|string',
            'commenced_time'        => 'required|string',
            'terminated_date'       => 'required|string',
            'terminated_time'       => 'required|string',
            'guards'                => 'nullable|string',
            'supervisor'            => 'nullable|string|max:255',
            'patrol_interval'       => 'nullable|string|max:100',
            'patrol_logs'           => 'nullable|array',
            'patrol_logs.*.round'                   => 'nullable|string',
            'patrol_logs.*.date'                    => 'nullable|string',
            'patrol_logs.*.start_time'              => 'nullable|string',
            'patrol_logs.*.end_time'                => 'nullable|string',
            'patrol_logs.*.area_patrolled_findings' => 'nullable|string',
            'patrol_logs.*.initials'                => 'nullable|string',
        ]);

        $data = $this->reportsRepo->storeFireWatchReport($request);

        return $this->successResponse($data, 'Fire Watch Report stored successfully.');
    }
}
