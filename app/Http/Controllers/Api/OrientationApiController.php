<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\OrientationRepository;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class OrientationApiController extends Controller
{
    use ApiResponser;

    protected $orientationRepo;

    public function __construct(OrientationRepository $orientationRepo)
    {
        $this->orientationRepo = $orientationRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/orientations",
     *     summary="Get all active orientations",
     *     tags={"Orientations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Orientations fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Orientations fetched successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="type", type="string", example="Safety"),
     *                 @OA\Property(property="passing_percentage", type="integer", example=80),
     *                 @OA\Property(property="questions", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="question_text", type="string", example="What is..."),
     *                     @OA\Property(property="options", type="array", @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="option_text", type="string", example="Opt 1"),
     *                         @OA\Property(property="is_correct", type="boolean", example=false),
     *                         @OA\Property(property="is_user_selected", type="boolean", example=true, description="True if the user picked this option in their last attempt.")
     *                     ))
     *                 )),
     *                 @OA\Property(property="last_attempt", type="object", nullable=true,
     *                     @OA\Property(property="score", type="number", format="float", example=85.0),
     *                     @OA\Property(property="is_passed", type="boolean", example=true)
     *                 )
     *             ))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $orientations = $this->orientationRepo->getAllOrientations();
        $orientations['orientations'] = $orientations['orientations']->where('status', true)->values();
        return $this->successResponse($orientations, 'Orientations fetched successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/orientations/signed",
     *     summary="Sign an orientation",
     *     tags={"Orientations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="orientation_id", type="string", example="1"),
     *                 @OA\Property(property="agreed", type="string", example="yes"),
     *                 @OA\Property(property="document", type="string", format="binary", description="Signed orientation file (PDF/Image)"),
     *                 @OA\Property(property="signature", type="string", description="Digital signature (Base64 image, text, etc.)"),
     *                 @OA\Property(
     *                     property="answers", 
     *                     type="array", 
     *                     @OA\Items(
     *                         @OA\Property(property="question_id", type="integer", example=1),
     *                         @OA\Property(property="option_id", type="integer", example=5)
     *                     ),
     *                     description="Quiz answers array"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Orientation signed successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Orientation signed successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error, Already Signed, or Quiz Failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You did not achieve the required passing score.")
     *         )
     *     )
     * )
     */
    public function signedOrientation(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'orientation_id' => 'required|exists:orientations,id',
            'agreed' => 'required|string|in:yes,no',
            'document' => 'required|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:5120',
            'signature' => 'sometimes|nullable|string',
            'answers' => 'nullable|array',
            'answers.*.question_id' => 'required_with:answers|integer|exists:orientation_questions,id',
            'answers.*.option_id' => 'required_with:answers|integer|exists:orientation_options,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $result = $this->orientationRepo->storeSignedOrientations($request);

        if (!$result['status']) {
            return response()->json($result, 400);
        }

        return $this->successResponse($result['signedOrientation'], $result['message']);
    }

    /**
     * @OA\Post(
     *     path="/api/orientations/submit-quiz",
     *     summary="Submit orientation quiz answers and calculate score",
     *     description="Allows users to submit their answers for an orientation quiz. It calculates the score, determines pass/fail status, saves the attempt, and provides feedback on incorrect questions.",
     *     tags={"Orientations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"orientation_id","answers"},
     *             @OA\Property(property="orientation_id", type="integer", example=5),
     *             @OA\Property(
     *                 property="answers", 
     *                 type="array", 
     *                 @OA\Items(
     *                     @OA\Property(property="question_id", type="integer", example=12),
     *                     @OA\Property(property="option_id", type="integer", example=20)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Quiz result returned successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="You passed the quiz!"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="attempt_id", type="integer", example=1),
     *                 @OA\Property(property="score", type="number", format="float", example=85.5),
     *                 @OA\Property(property="passing_percentage", type="integer", example=80),
     *                 @OA\Property(property="is_passed", type="boolean", example=true),
     *                 @OA\Property(property="incorrect_questions", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="question_id", type="integer", example=12),
     *                         @OA\Property(property="question_text", type="string", example="What is..."),
     *                         @OA\Property(property="user_provided_option_id", type="integer", example=20)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The orientation id field is required.")
     *         )
     *     )
     * )
     */
    public function submitQuiz(Request $request)
    {
        // 1. Basic validation (Hata diya exists rule kyunke repo handle karega)
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'orientation_id' => 'required|exists:orientations,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required',
            'answers.*.option_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        // 2. Repository handle karega scoring aur individual ID matching
        $result = $this->orientationRepo->submitQuizAttempt($request);

        return response()->json($result, 200);
    }
}
