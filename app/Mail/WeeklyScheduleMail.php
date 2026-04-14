<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Carbon\Carbon;

class WeeklyScheduleMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $weekStart;
    public $weekEnd;
    public $schedules;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $weekStart, $schedules)
    {
        $this->user = $user;
        $this->weekStart = Carbon::parse($weekStart);
        $this->weekEnd = $this->weekStart->copy()->endOfWeek(Carbon::SUNDAY);
        $this->schedules = $schedules;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Weekly Guard Schedule: ' . $this->weekStart->format('d M') . ' - ' . $this->weekEnd->format('d M, Y'))
                    ->view('emails.weekly_schedule');
    }
}
