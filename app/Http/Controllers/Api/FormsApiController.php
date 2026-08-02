<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\FormsRepository;
use App\Repositories\NumberRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FormsApiController extends Controller
{
    use ApiResponser;
    protected $formsRepo;

    public function __construct(FormsRepository $formsRepo)
    {
        $this->formsRepo = $formsRepo;
    }

    /**
     * @OA\Post(
     *     path="/api/forms/assessments/store",
     *     summary="Store User Assessment",
     *     tags={"Forms"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","worker_email","shift_date","location","start_time","compliance_fit_for_duty","any_injuries","physically_prepared","any_symptoms","understand_unethical_work_sick","up_to_date_orders","believe_fit_for_duty","client","supervisor_first_name","supervisor_last_name","position_today","safety_concerns","hazards_identified","right_to_refuse","right_to_participate","signature"},
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="worker_email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="shift_date", type="string", example="2026-04-30"),
     *             @OA\Property(property="location", type="string", example="123 Street Name"),
     *             @OA\Property(property="start_time", type="string", example="08:00 AM"),
     *             @OA\Property(property="end_time", type="string", example="04:00 PM"),
     *             @OA\Property(property="client", type="string", example="Client Name"),
     *             @OA\Property(property="supervisor_first_name", type="string", example="Super"),
     *             @OA\Property(property="supervisor_last_name", type="string", example="Visor"),
     *             @OA\Property(property="position_today", type="string", example="Security Guard"),
     *             @OA\Property(property="compliance_fit_for_duty", type="boolean", example=true),
     *             @OA\Property(property="any_injuries", type="boolean", example=false),
     *             @OA\Property(property="physically_prepared", type="boolean", example=true),
     *             @OA\Property(property="any_symptoms", type="boolean", example=false),
     *             @OA\Property(property="understand_unethical_work_sick", type="boolean", example=true),
     *             @OA\Property(property="up_to_date_orders", type="boolean", example=true),
     *             @OA\Property(property="believe_fit_for_duty", type="boolean", example=true),
     *             @OA\Property(property="safety_concerns", type="boolean", example=false),
     *             @OA\Property(property="hazards_identified", type="boolean", example=false),
     *             @OA\Property(property="right_to_refuse", type="string", example="I understand..."),
     *             @OA\Property(property="right_to_participate", type="string", example="I understand..."),
     *             @OA\Property(property="signature", type="string", description="Base64 data URI of the signature image", example="data:image/png;base64,iVBORw0KGgo...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assessment stored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Assessment stored successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function storeUserAssessments(Request $request)
    {
        Log::info('storeUserAssessments called with data: ' . json_encode($request->all()));
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'worker_email' => 'required|email',
            'shift_date' => 'required|string',
            'location' => 'required|string',
            'start_time' => 'required|string',
            'end_time' => 'nullable|string',
            'client' => 'required|string',
            'supervisor_first_name' => 'required|string',
            'supervisor_last_name' => 'required|string',
            'position_today' => 'required|string',
            'compliance_fit_for_duty' => 'required|boolean',
            'any_injuries' => 'required|boolean',
            'physically_prepared' => 'required|boolean',
            'any_symptoms' => 'required|boolean',
            'understand_unethical_work_sick' => 'required|boolean',
            'up_to_date_orders' => 'required|boolean',
            'believe_fit_for_duty' => 'required|boolean',
            'safety_concerns' => 'required|boolean',
            'hazards_identified' => 'required|boolean',
            'right_to_refuse' => 'required|string',
            'right_to_participate' => 'required|string',
            'signature' => 'required|string',
        ]);

        $data = $this->formsRepo->storeUserAssessments($request);

        return $this->successResponse($data, 'Assessment stored successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/forms/daily-vehicle-checklist/store",
     *     summary="Store Daily Vehicle Checklist",
     *     tags={"Forms"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"date","time","vehicle","odometer_reading","fuel","assigned_site","driver"},
     *                 @OA\Property(property="date", type="string", example="2026-04-30"),
     *                 @OA\Property(property="time", type="string", example="10:30 AM"),
     *                 @OA\Property(property="vehicle", type="string", example="Toyota Camry"),
     *                 @OA\Property(property="odometer_reading", type="string", example="125400"),
     *                 @OA\Property(property="fuel", type="string", example="Full Tank"),
     *                 @OA\Property(property="assigned_site", type="string", example="Downtown Mall"),
     *                 @OA\Property(property="driver", type="string", example="John Doe"),
     *                 @OA\Property(property="signature", type="string", description="Base64 data URI of the signature image", example="data:image/png;base64,iVBORw0KGgo..."),
     *                 @OA\Property(property="cosmetic_issues", type="string", example="No issues"),
     *                 @OA\Property(property="tires", type="string", example="Good"),
     *                 @OA\Property(property="windows", type="string", example="Clear"),
     *                 @OA\Property(property="staff_care", type="string", example="Clean"),
     *                 @OA\Property(property="dash_lights_gauges", type="string", example="Normal"),
     *                 @OA\Property(property="documents", type="string", description="Document inspection result or an uploaded file"),
     *                 @OA\Property(property="engine", type="string", example="Smooth"),
     *                 @OA\Property(property="oil_life_percentage", type="string", example="90%"),
     *                 @OA\Property(property="equipment", type="string", example="All present"),
     *                 @OA\Property(property="bwc_used_for_inspection", type="string", example="Yes"),
     *                 @OA\Property(property="issues_found", type="string", nullable=true, example="Scratch on rear bumper"),
     *                 @OA\Property(property="issue_images[]", type="array", @OA\Items(type="string", format="binary"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daily Vehicle Checklist stored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Daily Vehicle Checklist stored successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function storeDailyVehicleChecklist(Request $request)
    {
        Log::info('storeDailyVehicleChecklist called with data: ' . json_encode($request->all()));
        $request->validate([
            'date' => 'required|string',
            'time' => 'required|string',
            'vehicle' => 'required|string',
            'odometer_reading' => 'required|string',
            'fuel' => 'required|string',
            'assigned_site' => 'required|string',
            'driver' => 'required|string',
            'signature' => 'nullable|string',
            'documents' => 'nullable',
            'issues_found' => 'nullable|string',
            'issue_images' => 'nullable|array',
            'issue_images.*' => 'file|image',
        ]);

        $data = $this->formsRepo->storeDailyVehicleChecklist($request);

        return $this->successResponse($data, 'Daily Vehicle Checklist stored successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/forms/shift-adjustment/store",
     *     summary="Store Shift Adjustment Form",
     *     description="Submit a Shift Adjustment Form with employee info, current shift, requested adjustments, and approval section.",
     *     tags={"Forms"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"employee_name","current_date"},
     *
     *             @OA\Property(property="employee_name",        type="string",  example="John Doe"),
     *             @OA\Property(property="employee_id",          type="string",  example="EMP-1024"),
     *             @OA\Property(property="position_site",        type="string",  example="Security Guard / Downtown Mall"),
     *             @OA\Property(property="department",           type="string",  example="Security Operations"),
     *
     *             @OA\Property(property="current_date",         type="string",  format="date",    example="2026-07-01"),
     *             @OA\Property(property="current_start_time",   type="string",  example="08:00"),
     *             @OA\Property(property="current_end_time",     type="string",  example="16:00"),
     *             @OA\Property(property="current_supervisor",   type="string",  example="Jane Smith"),
     *             @OA\Property(property="current_shift_type",   type="string",  example="Day", enum={"Day","Night","On-call"}),
     *
     *             @OA\Property(property="shift_swap",           type="boolean", example=false),
     *             @OA\Property(property="late_start",           type="boolean", example=false),
     *             @OA\Property(property="coverage_request",     type="boolean", example=true),
     *             @OA\Property(property="early_release",        type="boolean", example=false),
     *             @OA\Property(property="time_off_request",     type="boolean", example=false),
     *             @OA\Property(property="overtime_approval",    type="boolean", example=false),
     *
     *             @OA\Property(property="requested_date",       type="string",  format="date",    example="2026-07-05"),
     *             @OA\Property(property="requested_start_time", type="string",  example="10:00"),
     *             @OA\Property(property="requested_end_time",   type="string",  example="18:00"),
     *             @OA\Property(property="replacement_employee", type="string",  example="Mark Johnson"),
     *             @OA\Property(property="adjustment_reason",    type="string",  example="Doctor appointment in the morning"),
     *             @OA\Property(property="additional_details",   type="string",  example="Need coverage from 10:00 AM. Mark Johnson has agreed to swap."),
     *
     *             @OA\Property(property="supervisor_name",      type="string",  example="Jane Smith"),
     *             @OA\Property(property="approval_date",        type="string",  format="date",    example="2026-07-02"),
     *             @OA\Property(property="decision",             type="string",  example="Approved", enum={"Approved","Denied","Pending"}),
     *             @OA\Property(property="approved_hours",       type="string",  example="8"),
     *             @OA\Property(property="supervisor_notes",     type="string",  example="Approved. Ensure replacement is briefed before shift start."),
     *
     *             @OA\Property(property="employee_signature", type="string", description="Base64 data URI of the employee signature image", example="data:image/png;base64,iVBORw0KGgo..."),
     *             @OA\Property(property="supervisor_signature", type="string", description="Base64 data URI of the supervisor signature image", example="data:image/png;base64,iVBORw0KGgo..."),
     *
     *             example={
     *                 "employee_name":        "John Doe",
     *                 "employee_id":          "EMP-1024",
     *                 "position_site":        "Security Guard / Downtown Mall",
     *                 "department":           "Security Operations",
     *                 "current_date":         "2026-07-01",
     *                 "current_start_time":   "08:00",
     *                 "current_end_time":     "16:00",
     *                 "current_supervisor":   "Jane Smith",
     *                 "current_shift_type":   "Day",
     *                 "shift_swap":           false,
     *                 "late_start":           false,
     *                 "coverage_request":     true,
     *                 "early_release":        false,
     *                 "time_off_request":     false,
     *                 "overtime_approval":    false,
     *                 "requested_date":       "2026-07-05",
     *                 "requested_start_time": "10:00",
     *                 "requested_end_time":   "18:00",
     *                 "replacement_employee": "Mark Johnson",
     *                 "adjustment_reason":    "Doctor appointment in the morning",
     *                 "additional_details":   "Need coverage from 10:00 AM. Mark Johnson has agreed to swap.",
     *                 "supervisor_name":      "Jane Smith",
     *                 "approval_date":        "2026-07-02",
     *                 "decision":             "Approved",
     *                 "approved_hours":       "8",
     *                 "supervisor_notes":     "Approved. Ensure replacement is briefed before shift start.",
     *                 "employee_signature":   "John Doe",
     *                 "supervisor_signature": "Jane Smith"
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shift Adjustment Form stored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status",  type="boolean", example=true),
     *             @OA\Property(property="message", type="string",  example="Shift Adjustment Form stored successfully."),
     *             @OA\Property(property="data",    type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status",  type="boolean", example=false),
     *             @OA\Property(property="message", type="string",  example="The employee name field is required.")
     *         )
     *     )
     * )
     */
    public function storeShiftAdjustmentForm(Request $request)
    {
        Log::info('storeShiftAdjustmentForm called with data: ' . json_encode($request->all()));
        $request->validate([
            // Employee Information
            'employee_name'        => 'required|string|max:255',
            'employee_id'          => 'nullable|string|max:100',
            'position_site'        => 'nullable|string|max:255',
            'department'           => 'nullable|string|max:255',

            // Current Shift
            'current_date'         => 'required|date',
            'current_start_time'   => 'nullable|string|max:20',
            'current_end_time'     => 'nullable|string|max:20',
            'current_supervisor'   => 'nullable|string|max:255',
            'current_shift_type'   => 'nullable|string|in:Day,Night,On-call',

            // Requested Adjustment - Checkboxes
            'shift_swap'           => 'nullable|boolean',
            'late_start'           => 'nullable|boolean',
            'coverage_request'     => 'nullable|boolean',
            'early_release'        => 'nullable|boolean',
            'time_off_request'     => 'nullable|boolean',
            'overtime_approval'    => 'nullable|boolean',

            // Requested Adjustment - Details
            'requested_date'       => 'nullable|date',
            'requested_start_time' => 'nullable|string|max:20',
            'requested_end_time'   => 'nullable|string|max:20',
            'replacement_employee' => 'nullable|string|max:255',
            'adjustment_reason'    => 'nullable|string|max:500',
            'additional_details'   => 'nullable|string',

            // Approval Section
            'supervisor_name'      => 'nullable|string|max:255',
            'approval_date'        => 'nullable|date',
            'decision'             => 'nullable|string|in:Approved,Denied,Pending',
            'approved_hours'       => 'nullable|string|max:50',
            'supervisor_notes'     => 'nullable|string',

            // Signatures
            'employee_signature'   => 'nullable|string',
            'supervisor_signature' => 'nullable|string',
        ]);

        $data = $this->formsRepo->storeShiftAdjustmentForm($request);

        return $this->successResponse($data, 'Shift Adjustment Form stored successfully.');
    }
}
