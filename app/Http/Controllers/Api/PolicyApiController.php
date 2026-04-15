<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\PolicyRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

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
        return $this->successResponse($policies, 'Policies fetched successfully.');
    }
}
