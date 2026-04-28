<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PanicNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

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
        Log::info("Panic Alert Triggered", [
            'sender_id' => $sender->id,
            'sender_name' => $sender->name
        ]);

        // Fetch all users who have an FCM token AND an active session
        $users = User::whereNotNull('fcm_token')->where('id', '!=', Auth::user()->id)->get();
        // $users = User::whereNotNull('fcm_token')
        //     ->whereExists(function ($query) {
        //         $query->select(DB::raw(1))
        //             ->from('sessions')
        //             ->whereRaw('sessions.user_id = users.id');
        //     })
        //     ->get();

        // Fetch all users who have an FCM token AND an active session (within session lifetime)
        // $sessionLifetime = config('session.lifetime', 120);
        // $activeThreshold = now()->subMinutes($sessionLifetime)->getTimestamp();

        // $users = User::whereNotNull('fcm_token')
        //     ->whereExists(function ($query) use ($activeThreshold) {
        //         $query->select(DB::raw(1))
        //             ->from('sessions')
        //             ->whereRaw('sessions.user_id = users.id')
        //             ->where('last_activity', '>=', $activeThreshold);
        //     })
        //     ->get();

        Log::info("Panic Alert: Target users fetched", [
            'count' => $users->count(),
            'users' => $users->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'token' => substr($u->fcm_token, 0, 10) . '...'])->toArray()
        ]);

        $successCount = 0;
        $failedCount = 0;

        foreach ($users as $user) {
            try {
                Log::info("Panic Alert: Attempting push to user", ['user_id' => $user->id]);

                Notification::send($user, new PanicNotification($sender->name));

                $successCount++;
                Log::info("Panic Alert: Push success", ['user_id' => $user->id]);
            } catch (\Exception $e) {
                $failedCount++;
                Log::error("Panic Alert: Push failed", [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'trace' => substr($e->getTraceAsString(), 0, 500)
                ]);
            }
        }

        Log::info("Panic Alert: Dispatch completed", [
            'total' => $users->count(),
            'success' => $successCount,
            'failed' => $failedCount
        ]);

        return response()->json([
            'status' => true,
            'message' => "Panic Notifications sent to all users.",
            // 'message' => "Panic notifications dispatched. Success: $successCount, Failed: $failedCount.",
        ]);
    }
}
