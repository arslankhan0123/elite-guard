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
     *     path="/api/dispatches/getUserDispatches",
     *     operationId="getUserDispatches",
     *     summary="Get active dispatch tasks assigned to the logged-in guard",
     *     description="Returns dispatches assigned to the authenticated guard which are not Completed or Cancelled and have not already been submitted by this guard.",
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
     *                 @OA\Property(property="dispatches", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=12),
     *                     @OA\Property(property="company_id", type="integer", example=1),
     *                     @OA\Property(property="site_id", type="integer", example=4),
     *                     @OA\Property(property="assigned_guard_ids", type="array", @OA\Items(type="integer"), example={3,7}),
     *                     @OA\Property(property="priority", type="string", enum={"Low","Medium","High","Emergency"}, example="High"),
     *                     @OA\Property(property="caller_type", type="string", enum={"Client","Guard","Emergency Services","Other"}, example="Client"),
     *                     @OA\Property(property="caller_name", type="string", example="John Smith"),
     *                     @OA\Property(property="incident_location", type="string", example="Main entrance"),
     *                     @OA\Property(property="incident_type", type="string", example="Unauthorized access"),
     *                     @OA\Property(property="incident_date", type="string", format="date", example="2026-08-15"),
     *                     @OA\Property(property="incident_time", type="string", format="time", example="21:30:00"),
     *                     @OA\Property(property="incident_details", type="string", example="An unknown person attempted to enter the premises."),
     *                     @OA\Property(property="action_taken", type="string", nullable=true),
     *                     @OA\Property(property="internal_notes", type="string", nullable=true),
     *                     @OA\Property(property="attachment_path", type="string", nullable=true),
     *                     @OA\Property(property="status", type="string", enum={"Pending","In Progress"}, example="Pending"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(property="company", type="object", nullable=true,
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Elite Security")
     *                     ),
     *                     @OA\Property(property="site", type="object", nullable=true,
     *                         @OA\Property(property="id", type="integer", example=4),
     *                         @OA\Property(property="name", type="string", example="Downtown Plaza"),
     *                         @OA\Property(property="address", type="string", nullable=true)
     *                     )
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
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
     *     operationId="submitDispatch",
     *     summary="Submit dispatch task report / action taken",
     *     description="Creates one submission for the authenticated guard. The dispatch becomes Completed after all assigned guards submit; otherwise a Pending dispatch becomes In Progress.",
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
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Dispatch task submitted successfully."),
     *                 @OA\Property(property="submission", type="object",
     *                     @OA\Property(property="id", type="integer", example=25),
     *                     @OA\Property(property="dispatch_id", type="integer", example=12),
     *                     @OA\Property(property="user_id", type="integer", example=3),
     *                     @OA\Property(property="action_taken", type="string", example="Secured the premises and notified local authorities."),
     *                     @OA\Property(property="file_attachment", type="string", format="uri", nullable=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Dispatch task not found or not assigned to the authenticated guard"),
     *     @OA\Response(response=422, description="Validation failed or report already submitted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Error"),
     *             @OA\Property(property="message", type="string", example="You have already submitted a report for this dispatch task."),
     *             @OA\Property(property="data", nullable=true)
     *         )
     *     )
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
