<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\AvailabilityRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class AvailabilityApiController extends Controller
{
    use ApiResponser;

    protected $availabilityRepo;

    public function __construct(AvailabilityRepository $availabilityRepo)
    {
        $this->availabilityRepo = $availabilityRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/availabilities",
     *     summary="List all personal availabilities",
     *     description="Fetches a list of availability submissions for the authenticated employee.",
     *     tags={"Availability"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Availabilities retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Availabilities fetched."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Availabilities retrieved successfully"),
     *                 @OA\Property(property="availabilities", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="date", type="string", example="2026-05-01"),
     *                         @OA\Property(property="shift", type="string", example="Morning"),
     *                         @OA\Property(property="user_notes", type="string", example="Available for extra hours"),
     *                         @OA\Property(property="admin_notes", type="string", nullable=true),
     *                         @OA\Property(property="status", type="string", example="pending")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $result = $this->availabilityRepo->getUserAvailabilities();
        return $this->successResponse($result, 'Availabilities fetched.');
    }

    /**
     * @OA\Post(
     *     path="/api/availabilities/store",
     *     summary="Submit new availability",
     *     description="Employee submits their availability for a specific date and shift.",
     *     tags={"Availability"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"date", "shift"},
     *             @OA\Property(property="date", type="string", format="date", example="2026-05-01"),
     *             @OA\Property(property="shift", type="string", example="Morning", description="Morning, Afternoon, Evening, Night"),
     *             @OA\Property(property="user_notes", type="string", example="Can work anytime")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Availability submitted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Availability submitted successfully"),
     *                 @OA\Property(property="availability", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'shift' => 'required|string',
            'user_notes' => 'nullable|string'
        ]);

        $result = $this->availabilityRepo->createAvailability($request->all());
        return $this->successResponse($result, 'Availability submitted successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/availabilities/update/{id}",
     *     summary="Update pending availability",
     *     description="Update a previously submitted availability if it is still pending.",
     *     tags={"Availability"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="date", type="string", format="date"),
     *             @OA\Property(property="shift", type="string"),
     *             @OA\Property(property="user_notes", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated successfully.")
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'nullable|date|after_or_equal:today',
            'shift' => 'nullable|string',
            'user_notes' => 'nullable|string'
        ]);

        $result = $this->availabilityRepo->updateAvailability($id, $request->all());

        if (!$result['status']) {
            return $this->errorResponse($result['message'], null, $result['code'] ?? 422);
        }

        return $this->successResponse($result, 'Availability updated successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/availabilities/delete/{id}",
     *     summary="Delete pending availability",
     *     tags={"Availability"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted successfully.")
     * )
     */
    public function destroy($id)
    {
        $result = $this->availabilityRepo->deleteAvailability($id);

        if (!$result['status']) {
            return $this->errorResponse($result['message'], null, $result['code'] ?? 422);
        }

        return $this->successResponse($result, 'Availability deleted successfully.');
    }
}
