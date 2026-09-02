<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use Illuminate\Support\Str;

class NoticeBoardNotification extends Notification
{
    use Queueable;

    protected $notice;
    protected $isUpdate;

    public function __construct($notice, $isUpdate = false)
    {
        $this->notice = $notice;
        $this->isUpdate = $isUpdate;
    }

    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $title = $this->isUpdate ? "Notice Updated: {$this->notice->subject}" : "New Notice: {$this->notice->subject}";
        $body = Str::limit(strip_tags($this->notice->long_description ?? $this->notice->subject), 120);

        return (new FcmMessage(notification: new FcmNotification(
            title: $title,
            body: $body,
        )))
        ->data([
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'type' => 'notice_board_alert',
            'notice_id' => (string)$this->notice->id,
            'subject' => (string)$this->notice->subject,
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $title = $this->isUpdate ? "Notice Updated: {$this->notice->subject}" : "New Notice: {$this->notice->subject}";
        $body = Str::limit(strip_tags($this->notice->long_description ?? $this->notice->subject), 120);

        return [
            'type' => 'notice_board_alert',
            'notice_id' => $this->notice->id,
            'subject' => $this->notice->subject,
            'title' => $title,
            'body' => $body,
        ];
    }
}
