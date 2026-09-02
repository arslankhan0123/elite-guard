<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class PolicyNotification extends Notification
{
    use Queueable;

    protected $policy;
    protected $isUpdate;

    public function __construct($policy, $isUpdate = false)
    {
        $this->policy = $policy;
        $this->isUpdate = $isUpdate;
    }

    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $action = $this->isUpdate ? 'updated' : 'published';
        $title = $this->isUpdate ? 'Policy Updated' : 'New Policy Published';
        $body = "Policy '{$this->policy->type}' has been {$action}. Please review and sign.";

        return (new FcmMessage(notification: new FcmNotification(
            title: $title,
            body: $body,
        )))
        ->data([
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'type' => 'policy_alert',
            'policy_id' => (string)$this->policy->id,
            'policy_type' => (string)$this->policy->type,
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $action = $this->isUpdate ? 'updated' : 'published';
        $title = $this->isUpdate ? 'Policy Updated' : 'New Policy Published';
        $body = "Policy '{$this->policy->type}' has been {$action}. Please review and sign.";

        return [
            'type' => 'policy_alert',
            'policy_id' => $this->policy->id,
            'policy_type' => $this->policy->type,
            'title' => $title,
            'body' => $body,
        ];
    }
}
