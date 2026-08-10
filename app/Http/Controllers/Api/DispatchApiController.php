<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dispatch;
use App\Traits\ApiResponser;
use App\Models\DispatchSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DispatchApiController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Get(
     *     path="/api/dispatches",
     *     summary="Get active dispatch tasks assigned to the logged-in guard",
     *     tags={"Dispatch Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Assigned dispatch tasks fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Assigned dispatch tasks fetched successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Assigned dispatch tasks fetched successfully."),
     *                 @OA\Property(property="dispatches", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Fetch dispatches assigned to the user that are NOT Completed/Cancelled and NOT yet submitted by this user
        $dispatches = Dispatch::where(function($query) use ($userId) {
                $query->whereJsonContains('assigned_guard_ids', $userId)
                      ->orWhereJsonContains('assigned_guard_ids', (string)$userId);
            })
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->whereDoesntHave('submissions', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['company', 'site'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->successResponse([
            'status' => true,
            'message' => 'Assigned dispatch tasks fetched successfully.',
            'dispatches' => $dispatches
        ], 'Assigned dispatch tasks fetched successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/dispatches/{id}/submit",
     *     summary="Submit dispatch task report / action taken",
     *     tags={"Dispatch Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the dispatch task",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"action_taken"},
     *                 @OA\Property(property="action_taken", type="string", example="Secured the premises and notified local authorities."),
     *                 @OA\Property(property="attachment", type="string", format="binary", description="Optional file attachment (Max 10MB)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dispatch task submitted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Dispatch task submitted successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Dispatch task not found or not assigned to user")
     * )
     */
    public function submit(Request $request, $id)
    {
        $userId = $request->user()->id;

        $dispatch = Dispatch::where(function($query) use ($userId) {
                $query->whereJsonContains('assigned_guard_ids', $userId)
                      ->orWhereJsonContains('assigned_guard_ids', (string)$userId);
            })
            ->findOrFail($id);

        // Check if user has already submitted a report for this dispatch task
        $alreadySubmitted = DispatchSubmission::where('dispatch_id', $id)
            ->where('user_id', $userId)
            ->exists();

        if ($alreadySubmitted) {
            return $this->errorResponse('You have already submitted a report for this dispatch task.', null, 422);
        }

        $request->validate([
            'action_taken' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:10240',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('documents/dispatch_attachments', 'public');
        }

        $submission = DispatchSubmission::create([
            'dispatch_id' => $dispatch->id,
            'user_id' => $userId,
            'action_taken' => $request->action_taken,
            'file_attachment' => $attachmentPath ? url(Storage::url($attachmentPath)) : null,
        ]);

        // Check if all assigned guards have submitted
        $assignedGuardIds = $dispatch->assigned_guard_ids ?? [];
        $submissionCount = DispatchSubmission::where('dispatch_id', $dispatch->id)->count();

        if (count($assignedGuardIds) > 0 && $submissionCount >= count($assignedGuardIds)) {
            $dispatch->update(['status' => 'Completed']);
        } else {
            // Otherwise, update status to 'In Progress' if it is 'Pending'
            if ($dispatch->status === 'Pending') {
                $dispatch->update(['status' => 'In Progress']);
            }
        }

        return $this->successResponse([
            'status' => true,
            'message' => 'Dispatch task submitted successfully.',
            'submission' => $submission
        ], 'Dispatch task submitted successfully.');
    }
}
