<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\SendReport;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class MissedClockOutNotification extends Notification
{
    use Queueable;

    protected string  $guardName;
    protected string  $siteName;
    protected string  $endTime;
    protected int     $minutesPast;
    protected int     $minutesBefore;
    protected bool    $isBeforeEnd;
    protected bool    $forGuard;
    protected ?string $tag;

    /**
     * @param bool        $forGuard true => addressed to the guard; false => admin-worded
     * @param string|null $tag      Unique Android tag / iOS collapse-id => no override
     */
    public function __construct(
        string $guardName,
        string $siteName,
        string $endTime,
        int    $minutesPast,
        int    $minutesBefore,
        bool   $isBeforeEnd,
        bool   $forGuard = false,
        ?string $tag = null
    ) {
        $this->guardName     = $guardName;
        $this->siteName      = $siteName;
        $this->endTime       = $endTime;
        $this->minutesPast   = $minutesPast;
        $this->minutesBefore = $minutesBefore;
        $this->isBeforeEnd   = $isBeforeEnd;
        $this->forGuard      = $forGuard;
        $this->tag           = $tag;
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

        $tag = $this->tag ?? ($this->forGuard ? 'missed_clock_out_guard' : 'missed_clock_out_admin');

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
            'tag'             => $tag,
            'notification_id' => (string) abs(crc32($tag)),
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

    /**
     * Send the push DIRECTLY via Firebase and log the REAL success / failure.
     */
    public static function pushTo(
        object $user,
        string $guardName,
        string $siteName,
        string $endTime,
        int $minutesPast,
        int $minutesBefore,
        bool $isBeforeEnd,
        bool $forGuard = false,
        ?string $tag = null
    ): bool {
        $prefix = '[MissedClockOutNotification] ';
        $name   = $user->name ?? 'unknown';
        $id     = $user->id ?? 'n/a';
        $token  = $user->fcm_token ?? null;

        if (empty($token)) {
            Log::warning($prefix . "SKIP: user '{$name}' (id={$id}) has no fcm_token.");
            return false;
        }

        $tokenShort = substr((string) $token, 0, 14) . '...';

        try {
            $message = (new self($guardName, $siteName, $endTime, $minutesPast, $minutesBefore, $isBeforeEnd, $forGuard, $tag))->toFcm($user);

            /** @var Messaging $messaging */
            $messaging = app(Messaging::class);

            $report = $messaging->sendMulticast($message, [$token]);

            $successCount = $report->count() - count($report->failures()->getItems());

            if (! $report->hasFailures() && $successCount > 0) {
                Log::info($prefix . "SUCCESS: push delivered to '{$name}' (id={$id}, token={$tokenShort}).");
                return true;
            }

            $reason = 'unknown failure';
            foreach ($report->failures()->getItems() as $failure) {
                /** @var SendReport $failure */
                $err = $failure->error();
                $reason = $err ? $err->getMessage() : 'unknown failure';
                if ($failure->messageWasSentToUnknownToken()) {
                    $reason .= ' (token is unregistered/stale — user must re-open the app)';
                } elseif ($failure->messageTargetWasInvalid()) {
                    $reason .= ' (token is invalid/malformed)';
                }
                break;
            }

            Log::error($prefix . "FAILED: push to '{$name}' (id={$id}, token={$tokenShort}) — {$reason}");
            return false;
        } catch (\Throwable $e) {
            Log::error($prefix . "EXCEPTION sending push to '{$name}' (id={$id}, token={$tokenShort}): "
                . $e->getMessage() . ' @ ' . basename($e->getFile()) . ':' . $e->getLine());
            return false;
        }
    }
}
