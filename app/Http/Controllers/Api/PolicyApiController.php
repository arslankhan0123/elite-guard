<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\PolicyRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PolicyApiController extends Controller
{
    use ApiResponser;

    protected $policyRepo;

    public function __construct(PolicyRepository $policyRepo)
    {
        $this->policyRepo = $policyRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/policies",
     *     summary="Get all policies",
     *     tags={"Policies"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Policies fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Policies fetched successfully."),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="type", type="string", example="Employment Policies"),
     *                     @OA\Property(property="status", type="boolean", example=true),
     *                     @OA\Property(property="document", type="string", example="http://example.com/documents/policies/filename.pdf"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index()
    {
        $policies = $this->policyRepo->getAllPolicies();
        
        $userId = Auth::id();
        foreach ($policies['policies'] as $policy) {
            $policy->signed_policy = \App\Models\SignedPolicy::where('user_id', $userId)
                ->where('policy_id', $policy->id)
                ->first();
        }

        return $this->successResponse($policies, 'Policies fetched successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/policies/signed",
     *     summary="Sign a policy",
     *     tags={"Policies"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="policy_id", type="string", example="1"),
     *                 @OA\Property(property="agreed", type="string", example="yes"),
     *                 @OA\Property(property="document", type="string", format="binary", description="Signed document file (PDF/Image)"),
     *                 @OA\Property(property="signature", type="string", description="Digital signature (Base64 image, text, etc.)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Policy signed successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Policy signed successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error or Already Signed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You have already signed this policy.")
     *         )
     *     )
     * )
     */
    public function signedPolicy(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'policy_id' => 'required|exists:policies,id',
            'agreed' => 'required|string|in:yes,no',
            // 'document' => 'required|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:5120',
            'signature' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $result = $this->policyRepo->storeSignedPolicies($request);

        if (!$result['status']) {
            return response()->json($result, 400);
        }

        return $this->successResponse($result, $result['message']);
    }
}
