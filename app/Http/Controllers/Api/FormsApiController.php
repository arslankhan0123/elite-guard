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
     *             @OA\Property(property="signature", type="string", example="Signature Text")
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
     *                 @OA\Property(property="signature", type="string", example="Signature Text"),
     *                 @OA\Property(property="cosmetic_issues", type="string", example="No issues"),
     *                 @OA\Property(property="tires", type="string", example="Good"),
     *                 @OA\Property(property="windows", type="string", example="Clear"),
     *                 @OA\Property(property="staff_care", type="string", example="Clean"),
     *                 @OA\Property(property="dash_lights_gauges", type="string", example="Normal"),
     *                 @OA\Property(property="documents", type="string", format="binary", description="File upload (pdf, jpg, png, etc.)"),
     *                 @OA\Property(property="engine", type="string", example="Smooth"),
     *                 @OA\Property(property="oil_life_percentage", type="string", example="90%"),
     *                 @OA\Property(property="equipment", type="string", example="All present"),
     *                 @OA\Property(property="bwc_used_for_inspection", type="string", example="Yes")
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
        $request->validate([
            'date' => 'required|string',
            'time' => 'required|string',
            'vehicle' => 'required|string',
            'odometer_reading' => 'required|string',
            'fuel' => 'required|string',
            'assigned_site' => 'required|string',
            'driver' => 'required|string',
            'signature' => 'nullable|string',
            'documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $data = $this->formsRepo->storeDailyVehicleChecklist($request);

        return $this->successResponse($data, 'Daily Vehicle Checklist stored successfully.');
    }
}
