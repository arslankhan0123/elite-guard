<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class TaxDocumentNotification extends Notification
{
    use Queueable;

    protected $taxDoc;
    protected $isUpdate;

    public function __construct($taxDoc, $isUpdate = false)
    {
        $this->taxDoc = $taxDoc;
        $this->isUpdate = $isUpdate;
    }

    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $action = $this->isUpdate ? 'updated' : 'uploaded';
        $title = $this->isUpdate ? 'Tax Document Updated' : 'New Tax Document Available';
        $body = "Tax document '{$this->taxDoc->type}' has been {$action}. Please check your tax documents.";

        return (new FcmMessage(notification: new FcmNotification(
            title: $title,
            body: $body,
        )))
        ->data([
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'type' => 'tax_doc_alert',
            'tax_doc_id' => (string)$this->taxDoc->id,
            'tax_doc_type' => (string)$this->taxDoc->type,
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $action = $this->isUpdate ? 'updated' : 'uploaded';
        $title = $this->isUpdate ? 'Tax Document Updated' : 'New Tax Document Available';
        $body = "Tax document '{$this->taxDoc->type}' has been {$action}. Please check your tax documents.";

        return [
            'type' => 'tax_doc_alert',
            'tax_doc_id' => $this->taxDoc->id,
            'tax_doc_type' => $this->taxDoc->type,
            'title' => $title,
            'body' => $body,
        ];
    }
}
