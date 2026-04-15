<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PanicNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class PanicApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/panic",
     *     summary="Trigger panic notifications",
     *     description="Sends a panic notification to all currently logged-in users (active sessions) who have an FCM token. Includes the sender's name in the message.",
     *     tags={"Panic"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Panic notifications sent successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Panic notifications sent successfully to all registered devices.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function panicNotifications(Request $request)
    {
        $sender = $request->user();

        // Fetch all users who have an FCM token
        // $users = User::whereNotNull('fcm_token')->get();

        // Fetch all users who have an FCM token AND an active session
        $users = User::whereNotNull('fcm_token')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('sessions')
                    ->whereRaw('sessions.user_id = users.id');
            })
            ->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new PanicNotification($sender->name));
        }

        return response()->json([
            'status' => true,
            'message' => 'Panic notifications sent successfully to all registered devices.',
        ]);
    }
}
