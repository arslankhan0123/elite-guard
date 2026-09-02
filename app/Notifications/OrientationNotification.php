<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class OrientationNotification extends Notification
{
    use Queueable;

    protected $orientation;
    protected $isUpdate;

    public function __construct($orientation, $isUpdate = false)
    {
        $this->orientation = $orientation;
        $this->isUpdate = $isUpdate;
    }

    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $action = $this->isUpdate ? 'updated' : 'added';
        $title = $this->isUpdate ? 'Orientation Updated' : 'New Orientation Added';
        $body = "Orientation '{$this->orientation->type}' has been {$action}. Passing required: {$this->orientation->passing_percentage}%.";

        return (new FcmMessage(notification: new FcmNotification(
            title: $title,
            body: $body,
        )))
        ->data([
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'type' => 'orientation_alert',
            'orientation_id' => (string)$this->orientation->id,
            'orientation_type' => (string)$this->orientation->type,
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $action = $this->isUpdate ? 'updated' : 'added';
        $title = $this->isUpdate ? 'Orientation Updated' : 'New Orientation Added';
        $body = "Orientation '{$this->orientation->type}' has been {$action}. Passing required: {$this->orientation->passing_percentage}%.";

        return [
            'type' => 'orientation_alert',
            'orientation_id' => $this->orientation->id,
            'orientation_type' => $this->orientation->type,
            'title' => $title,
            'body' => $body,
        ];
    }
}
