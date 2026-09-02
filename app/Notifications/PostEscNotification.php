<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use Illuminate\Support\Str;

class PostEscNotification extends Notification
{
    use Queueable;

    protected $post;
    protected $isUpdate;

    public function __construct($post, $isUpdate = false)
    {
        $this->post = $post;
        $this->isUpdate = $isUpdate;
    }

    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $title = $this->isUpdate ? "Post & ESC Updated: {$this->post->subject}" : "Post & ESC Order: {$this->post->subject}";
        $body = Str::limit(strip_tags($this->post->long_description ?? $this->post->subject), 120);

        return (new FcmMessage(notification: new FcmNotification(
            title: $title,
            body: $body,
        )))
        ->data([
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'type' => 'post_esc_alert',
            'post_esc_id' => (string)$this->post->id,
            'subject' => (string)$this->post->subject,
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $title = $this->isUpdate ? "Post & ESC Updated: {$this->post->subject}" : "Post & ESC Order: {$this->post->subject}";
        $body = Str::limit(strip_tags($this->post->long_description ?? $this->post->subject), 120);

        return [
            'type' => 'post_esc_alert',
            'post_esc_id' => $this->post->id,
            'subject' => $this->post->subject,
            'title' => $title,
            'body' => $body,
        ];
    }
}
