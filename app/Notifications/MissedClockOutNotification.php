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
    protected bool   $forGuard;

    /**
     * @param bool $forGuard  true  => message addressed to the guard themselves
     *                        false => message addressed to an admin/super-admin
     */
    public function __construct(
        string $guardName,
        string $siteName,
        string $endTime,
        int    $minutesPast,
        int    $minutesBefore,
        bool   $isBeforeEnd,
        bool   $forGuard = false
    ) {
        $this->guardName     = $guardName;
        $this->siteName      = $siteName;
        $this->endTime       = $endTime;
        $this->minutesPast   = $minutesPast;
        $this->minutesBefore = $minutesBefore;
        $this->isBeforeEnd   = $isBeforeEnd;
        $this->forGuard      = $forGuard;
    }

    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        if ($this->forGuard) {
            $title = '⏰ Clock-Out Reminder';
            if ($this->isBeforeEnd) {
                $body = "Your shift at {$this->siteName} ends at {$this->endTime} (in {$this->minutesBefore} min). Please clock out.";
            } else {
                $body = "You haven't clocked out from {$this->siteName}. Your shift ended at {$this->endTime} ({$this->minutesPast} min ago). Please clock out now.";
            }
        } else {
            $title = '⚠️ Missed Clock-Out Alert';
            if ($this->isBeforeEnd) {
                $body = "{$this->guardName} has not clocked out from {$this->siteName}. Shift ends at {$this->endTime} (in {$this->minutesBefore} min).";
            } else {
                $body = "{$this->guardName} has not clocked out at {$this->siteName}. Shift ended at {$this->endTime} ({$this->minutesPast} min ago).";
            }
        }

        return (new FcmMessage(notification: new FcmNotification(
            title: $title,
            body: $body,
        )))
        ->data([
            'click_action'    => 'FLUTTER_NOTIFICATION_CLICK',
            'type'            => 'missed_clock_out',
            'audience'        => $this->forGuard ? 'guard' : 'admin',
            'guard_name'      => $this->guardName,
            'site_name'       => $this->siteName,
            'end_time'        => $this->endTime,
            'minutes_past'    => (string) $this->minutesPast,
            'minutes_before'  => (string) $this->minutesBefore,
            'is_before_end'   => $this->isBeforeEnd ? 'true' : 'false',
            'for_guard'       => $this->forGuard ? '1' : '0',
        ])
        // Distinct tag / collapse-id per audience so the guard reminder and the
        // admin alert appear as SEPARATE notifications on the same device
        // (e.g. a user who is both an employee and a SuperAdmin).
        ->custom([
            'android' => [
                'notification' => [
                    'tag' => $this->forGuard ? 'missed_clock_out_guard' : 'missed_clock_out_admin',
                ],
            ],
            'apns' => [
                'headers' => [
                    'apns-collapse-id' => $this->forGuard ? 'missed_clock_out_guard' : 'missed_clock_out_admin',
                ],
            ],
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
            'for_guard'      => $this->forGuard,
        ];
    }
}
