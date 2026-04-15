<?php

namespace App\Listeners;

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\FcmChannel;

class DeleteExpiredNotificationTokens
{
    /**
     * Handle the event.
     */
    public function handle(NotificationFailed $event): void
    {
        if ($event->channel == FcmChannel::class) {
            $report = Arr::get($event->data, 'report');

            $token = null;
            $reason = $report ? $report->error() : 'Unknown error';

            if ($report && $report->target() instanceof \Kreait\Firebase\Messaging\RegistrationToken) {
                $token = $report->target()->value();
            }

            Log::error('❌ FCM Notification failed', [
                'user_id' => $event->notifiable->id ?? null,
                'token'   => $token,
                'reason'  => $reason,
            ]);

            // Remove token if invalid
            if ($token && $event->notifiable->fcm_token === $token) {
                $event->notifiable->update(['fcm_token' => null]);

                Log::info("🗑️ Invalid FCM token removed", [
                    'user_id' => $event->notifiable->id ?? null,
                    'token'   => $token,
                ]);
            }
        }
    }
}
