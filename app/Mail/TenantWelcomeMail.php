<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public Tenant $tenant;
    public bool $isNew;
    public ?string $password;

    /**
     * Create a new message instance.
     */
    public function __construct(Tenant $tenant, bool $isNew = true, ?string $password = null)
    {
        $this->tenant   = $tenant;
        $this->isNew    = $isNew;
        $this->password = $password;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $subject = $this->isNew
            ? 'Welcome to Elite Guard – Tenant Account Created'
            : 'Elite Guard – Your Tenant Account Has Been Updated';

        return $this->subject($subject)
                    ->view('emails.tenant_welcome');
    }
}
