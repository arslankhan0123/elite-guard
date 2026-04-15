<?php

namespace App\Listeners;

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\FcmChannel;
use Illuminate\Support\Arr;

class LogFcmNotification
{
    /**
     * Handle the event.
     */
    public function handle(NotificationFailed $event): void
    {
        if ($event->channel === FcmChannel::class) {
            $report = Arr::get($event->data, 'report');

            // Default nulls
            $token = null;
            $reason = $report ? $report->error() : 'Unknown error';

            // Extract token if target is a RegistrationToken
            if ($report && $report->target() instanceof \Kreait\Firebase\Messaging\RegistrationToken) {
                $token = $report->target()->value();
            }

            Log::error('❌ FCM Notification failed', [
                'user_id' => $event->notifiable->id ?? null,
                'token'   => $token,
                'reason'  => $reason,
            ]);
        }
    }
}
