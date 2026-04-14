<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\NumberRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class NumberApiController extends Controller
{
    use ApiResponser;
    protected $numberRepo;

    public function __construct(NumberRepository $numberRepo)
    {
        $this->numberRepo = $numberRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/numbers",
     *     summary="Get active numbers for the Application",
     *     description="Fetches numbers specifically categorized for 'Application' usage.",
     *     tags={"Numbers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Numbers fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Numbers fetched."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Numbers retrieved successfully"),
     *                 @OA\Property(property="numbers", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $data = $this->numberRepo->getApplicationNumbers();

        return $this->successResponse($data, 'Numbers fetched.');
    }
}
