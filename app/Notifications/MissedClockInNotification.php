<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class MissedClockInNotification extends Notification
{
    use Queueable;

    protected string $guardName;
    protected string $siteName;
    protected string $startTime;
    protected int    $minutesLate;

    public function __construct(string $guardName, string $siteName, string $startTime, int $minutesLate)
    {
        $this->guardName   = $guardName;
        $this->siteName    = $siteName;
        $this->startTime   = $startTime;
        $this->minutesLate = $minutesLate;
    }

    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(notification: new FcmNotification(
            title: '⚠️ Missed Clock-In Alert',
            body: "{$this->guardName} has not clocked in at {$this->siteName}. Shift started at {$this->startTime} ({$this->minutesLate} min ago).",
        )))
        ->data([
            'click_action'  => 'FLUTTER_NOTIFICATION_CLICK',
            'type'          => 'missed_clock_in',
            'guard_name'    => $this->guardName,
            'site_name'     => $this->siteName,
            'start_time'    => $this->startTime,
            'minutes_late'  => (string) $this->minutesLate,
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'missed_clock_in',
            'guard_name'   => $this->guardName,
            'site_name'    => $this->siteName,
            'start_time'   => $this->startTime,
            'minutes_late' => $this->minutesLate,
        ];
    }
}
