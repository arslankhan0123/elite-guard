<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class OpenShiftNotification extends Notification
{
    use Queueable;

    protected $openShift;
    protected $isUpdate;

    public function __construct($openShift, $isUpdate = false)
    {
        $this->openShift = $openShift;
        $this->isUpdate = $isUpdate;
    }

    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $siteName = $this->openShift->site?->name ?? 'N/A';
        $title = $this->isUpdate ? 'Open Shift Updated' : 'New Open Shift Available!';
        $body = "{$this->openShift->shift_name} at {$siteName} on {$this->openShift->date} ({$this->openShift->start_time} - {$this->openShift->end_time}). Open app to claim.";

        return (new FcmMessage(notification: new FcmNotification(
            title: $title,
            body: $body,
        )))
        ->data([
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'type' => 'open_shift_alert',
            'open_shift_id' => (string)$this->openShift->id,
            'site_name' => (string)$siteName,
            'shift_name' => (string)$this->openShift->shift_name,
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $siteName = $this->openShift->site?->name ?? 'N/A';
        $title = $this->isUpdate ? 'Open Shift Updated' : 'New Open Shift Available!';
        $body = "{$this->openShift->shift_name} at {$siteName} on {$this->openShift->date} ({$this->openShift->start_time} - {$this->openShift->end_time}). Open app to claim.";

        return [
            'type' => 'open_shift_alert',
            'open_shift_id' => $this->openShift->id,
            'site_name' => $siteName,
            'shift_name' => $this->openShift->shift_name,
            'title' => $title,
            'body' => $body,
        ];
    }
}
