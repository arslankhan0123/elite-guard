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
     *     path="/api/policies/signedPolicy",
     *     summary="Sign a policy",
     *     description="Allows a user to sign a specific policy by providing their agreement, a signature, and optionally a signed document file.",
     *     tags={"Policies"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"policy_id","agreed"},
     *                 @OA\Property(property="policy_id", type="integer", example=1, description="ID of the policy to be signed"),
     *                 @OA\Property(property="agreed", type="string", enum={"yes", "no"}, example="yes", description="Whether the user agrees to the policy"),
     *                 @OA\Property(property="document", type="string", format="binary", description="Optional signed document file (PDF/Image)"),
     *                 @OA\Property(property="signature", type="string", example="John Doe", description="Digital signature (text or encoded signature)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Policy signed successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Policy signed successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="signedPolicy", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error or Already Signed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You have already signed this policy.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
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
