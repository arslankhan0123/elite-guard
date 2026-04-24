<?php

namespace App\Notifications;

use App\Models\OpenShift;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
use NotificationChannels\Firebase\FirebaseChannel;
use NotificationChannels\Firebase\FirebaseMessage;

class OpenShiftClaimRejectedNotification extends Notification
{
    use Queueable;

    protected OpenShift $openShift;
    protected ?string $adminNote;

    public function __construct(OpenShift $openShift, ?string $adminNote = null)
    {
        $this->openShift = $openShift;
        $this->adminNote = $adminNote;
    }

    public function via($notifiable): array
    {
        return ['firebase'];
    }

    public function toFirebase($notifiable): FirebaseMessage
    {
        $date = \Carbon\Carbon::parse($this->openShift->date)->format('D, d M Y');
        $body = "Your claim for {$this->openShift->shift_name} on {$date} was not approved.";
        
        if ($this->adminNote) {
            $body .= " Reason: " . $this->adminNote;
        }

        return (new FirebaseMessage)
            ->withNotification(FcmNotification::create(
                '❌ Open Shift Claim Update',
                $body
            ))
            ->withData([
                'type'          => 'open_shift_rejected',
                'open_shift_id' => (string) $this->openShift->id,
            ]);
    }
}
