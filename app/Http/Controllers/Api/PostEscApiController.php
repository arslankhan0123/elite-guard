<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\PostEscRepository;
use App\Traits\ApiResponser;

class PostEscApiController extends Controller
{
    use ApiResponser;

    public function __construct(private readonly PostEscRepository $postEscRepo)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/post-esc",
     *     summary="Get all Post & ESC records",
     *     tags={"Post & ESC"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Post & ESC data fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Post & ESC data fetched successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Post & ESC data fetched successfully."),
     *                 @OA\Property(property="posts", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="date", type="string", format="date", example="2026-08-04"),
     *                     @OA\Property(property="subject", type="string", example="Post orders update"),
     *                     @OA\Property(property="long_description", type="string", example="Updated Post & ESC instructions..."),
     *                     @OA\Property(property="pdf_path", type="string", nullable=true, example="documents/post-esc/instructions.pdf"),
     *                     @OA\Property(property="pdf_url", type="string", format="uri", nullable=true, example="http://127.0.0.1:8000/storage/documents/post-esc/instructions.pdf"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-08-04T20:37:13Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-08-04T20:37:13Z")
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index()
    {
        $data = $this->postEscRepo->getPostEsc();

        return $this->successResponse($data, 'Post & ESC data fetched successfully.');
    }
}
