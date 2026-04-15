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
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $result = $this->orientationRepo->getAllOrientations();
        $result['orientations'] = $result['orientations']->where('status', true)->values();
        return $this->successResponse($result, 'Orientations fetched successfully.');
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
     *                 @OA\Property(property="document", type="string", format="binary", description="Signed orientation file (PDF/Image)")
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
     *         description="Validation Error or Already Signed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You have already signed this orientation.")
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
}
