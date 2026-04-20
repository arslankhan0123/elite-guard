<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\EmployeeOfferLetter;

class OfferLetterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $offer;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, EmployeeOfferLetter $offer)
    {
        $this->user = $user;
        $this->offer = $offer;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Official Offer Letter - Elite Guard Management')
                    ->view('emails.offer_letter');
    }
}
