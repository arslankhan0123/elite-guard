<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class PanicNotification extends Notification
{
    use Queueable;

    protected $senderName;

    /**
     * Create a new notification instance.
     */
    public function __construct($senderName)
    {
        $this->senderName = $senderName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(notification: new FcmNotification(
            title: 'Panic Alert!',
            body: "Emergency: {$this->senderName} has triggered a panic alert. Immediate attention required.",
        )))
        ->data([
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'type' => 'panic_alert',
            'sender_name' => $this->senderName,
        ]);
    }
}
