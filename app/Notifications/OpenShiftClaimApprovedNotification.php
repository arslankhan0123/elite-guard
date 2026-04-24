<?php

namespace App\Notifications;

use App\Models\OpenShift;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
use NotificationChannels\Firebase\FirebaseChannel;
use NotificationChannels\Firebase\FirebaseMessage;

class OpenShiftClaimApprovedNotification extends Notification
{
    use Queueable;

    protected OpenShift $openShift;

    public function __construct(OpenShift $openShift)
    {
        $this->openShift = $openShift;
    }

    public function via($notifiable): array
    {
        return ['firebase'];
    }

    public function toFirebase($notifiable): FirebaseMessage
    {
        $date = \Carbon\Carbon::parse($this->openShift->date)->format('D, d M Y');
        $time = substr($this->openShift->start_time, 0, 5) . ' - ' . substr($this->openShift->end_time, 0, 5);

        return (new FirebaseMessage)
            ->withNotification(FcmNotification::create(
                '✅ Open Shift Approved!',
                "Your claim for {$this->openShift->shift_name} on {$date} ({$time}) has been approved."
            ))
            ->withData([
                'type'          => 'open_shift_approved',
                'open_shift_id' => (string) $this->openShift->id,
            ]);
    }
}
