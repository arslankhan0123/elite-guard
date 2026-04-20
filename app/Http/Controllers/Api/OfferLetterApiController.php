<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\OfferLetterRepository;
use App\Repositories\OrientationRepository;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class OfferLetterApiController extends Controller
{
    use ApiResponser;

    protected $offerLetterRepo;

    public function __construct(OfferLetterRepository $offerLetterRepo)
    {
        $this->offerLetterRepo = $offerLetterRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/offer-letter/user",
     *     summary="Get offer letter for the authenticated user",
     *     tags={"Offer Letter"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Offer letter fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Offer letter fetched successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Offer letter retrieved successfully"),
     *                 @OA\Property(property="offerLetter", type="object", nullable=true,
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=5),
     *                     @OA\Property(property="job_title", type="string", example="Security Guard"),
     *                     @OA\Property(property="joining_date", type="string", format="date", example="2024-05-01"),
     *                     @OA\Property(property="salary", type="string", example="18"),
     *                     @OA\Property(property="description", type="string", example="Job description here..."),
     *                     @OA\Property(property="is_email_sent", type="boolean", example=true),
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
    public function getUserOfferLetter()
    {
        $result = $this->offerLetterRepo->getUserOfferLetter();
        return $this->successResponse($result, 'Offer letter fetched successfully.');
    }
}
