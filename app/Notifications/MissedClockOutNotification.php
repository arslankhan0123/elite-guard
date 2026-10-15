<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class MissedClockOutNotification extends Notification
{
    use Queueable;

    protected string $guardName;
    protected string $siteName;
    protected string $endTime;
    protected int    $minutesPast;
    protected int    $minutesBefore;
    protected bool   $isBeforeEnd;

    public function __construct(
        string $guardName,
        string $siteName,
        string $endTime,
        int    $minutesPast,
        int    $minutesBefore,
        bool   $isBeforeEnd
    ) {
        $this->guardName     = $guardName;
        $this->siteName      = $siteName;
        $this->endTime       = $endTime;
        $this->minutesPast   = $minutesPast;
        $this->minutesBefore = $minutesBefore;
        $this->isBeforeEnd   = $isBeforeEnd;
    }

    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        if ($this->isBeforeEnd) {
            $body = "⏰ Reminder: Please clock out from {$this->siteName}. Shift ends at {$this->endTime} (in {$this->minutesBefore} min).";
        } else {
            $body = "⚠️ {$this->guardName} has not clocked out at {$this->siteName}. Shift ended at {$this->endTime} ({$this->minutesPast} min ago).";
        }

        return (new FcmMessage(notification: new FcmNotification(
            title: '⏰ Clock-Out Reminder',
            body: $body,
        )))
        ->data([
            'click_action'    => 'FLUTTER_NOTIFICATION_CLICK',
            'type'            => 'missed_clock_out',
            'guard_name'      => $this->guardName,
            'site_name'       => $this->siteName,
            'end_time'        => $this->endTime,
            'minutes_past'    => (string) $this->minutesPast,
            'minutes_before'  => (string) $this->minutesBefore,
            'is_before_end'   => $this->isBeforeEnd ? 'true' : 'false',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'missed_clock_out',
            'guard_name'     => $this->guardName,
            'site_name'      => $this->siteName,
            'end_time'       => $this->endTime,
            'minutes_past'   => $this->minutesPast,
            'minutes_before' => $this->minutesBefore,
            'is_before_end'  => $this->isBeforeEnd,
        ];
    }
}
