<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ScheduleUpdatedNotification extends Notification
{
    use Queueable;

    protected $weekDates;
    protected $isUpdate;

    /**
     * Create a new notification instance.
     */
    public function __construct($weekDates, $isUpdate = false)
    {
        $this->weekDates = $weekDates;
        $this->isUpdate = $isUpdate;
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
        $title = $this->isUpdate ? 'Schedule Updated' : 'New Schedule Assigned';
        $body = $this->isUpdate 
            ? "Your shifts for the week ({$this->weekDates}) have been updated. Please check your app for details."
            : "Your shifts for the upcoming week ({$this->weekDates}) have been assigned. Please check your app.";

        return (new FcmMessage(notification: new FcmNotification(
            title: $title,
            body: $body,
        )))
        ->data([
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'type' => 'schedule_update',
            'week_dates' => $this->weekDates,
        ]);
    }
}
