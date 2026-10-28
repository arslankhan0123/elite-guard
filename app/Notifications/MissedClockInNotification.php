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

    protected string  $guardName;
    protected string  $siteName;
    protected string  $startTime;
    protected int     $minutesLate;
    protected bool    $forGuard;
    protected ?string $tag;

    /**
     * @param bool        $forGuard true => addressed to the guard; false => admin-worded
     * @param string|null $tag      Android notification tag / iOS collapse-id.
     *                              Pass a UNIQUE value so each push shows separately
     *                              (previous notifications are NOT overridden).
     */
    public function __construct(string $guardName, string $siteName, string $startTime, int $minutesLate, bool $forGuard = false, ?string $tag = null)
    {
        $this->guardName   = $guardName;
        $this->siteName    = $siteName;
        $this->startTime   = $startTime;
        $this->minutesLate = $minutesLate;
        $this->forGuard    = $forGuard;
        $this->tag         = $tag;
    }

    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        if ($this->forGuard) {
            $title = '⏰ Clock-In Reminder';
            $body  = "You haven't clocked in for your shift at {$this->siteName}. It started at {$this->startTime} ({$this->minutesLate} min ago). Please clock in now.";
        } else {
            $title = '⚠️ Missed Clock-In Alert';
            $body  = "{$this->guardName} has not clocked in at {$this->siteName}. Shift started at {$this->startTime} ({$this->minutesLate} min ago).";
        }

        // Unique tag => every push shows separately (no override). Falls back to a
        // fixed tag only if no unique tag was provided.
        $tag = $this->tag ?? ($this->forGuard ? 'missed_clock_in_guard' : 'missed_clock_in_admin');

        return (new FcmMessage(notification: new FcmNotification(
            title: $title,
            body: $body,
        )))
        ->data([
            'click_action'  => 'FLUTTER_NOTIFICATION_CLICK',
            'type'          => 'missed_clock_in',
            'audience'      => $this->forGuard ? 'guard' : 'admin',
            'guard_name'    => $this->guardName,
            'site_name'     => $this->siteName,
            'start_time'    => $this->startTime,
            'minutes_late'  => (string) $this->minutesLate,
            'for_guard'     => $this->forGuard ? '1' : '0',
            'tag'           => $tag,
        ])
        ->custom([
            'android' => [
                'notification' => [
                    'tag' => $tag,
                ],
            ],
            'apns' => [
                'headers' => [
                    'apns-collapse-id' => $tag,
                ],
            ],
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
            'for_guard'    => $this->forGuard,
        ];
    }
}
