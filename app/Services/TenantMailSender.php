<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class TenantMailSender
{
    /**
     * Send a raw HTML email using the CURRENT tenant's saved SMTP settings
     * (the per-company "SMTP Settings" the admin configures in their profile),
     * instead of the global .env mailer.
     *
     * Throws RuntimeException if this tenant has not configured SMTP.
     */
    public static function sendHtml(string $toEmail, ?string $toName, string $subject, string $html): void
    {
        // Never attempt to send to an empty/invalid address (prevents bounces / undelivered mail)
        if (! filter_var((string) $toEmail, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException("Invalid recipient email: '" . (string) $toEmail . "'.");
        }

        $host        = Setting::get('smtp_host');
        $port        = Setting::get('smtp_port');
        $encryption  = Setting::get('smtp_encryption');
        $fromAddress = Setting::get('smtp_from_address');
        $fromName    = Setting::get('smtp_from_name');

        if (! filled($host) || ! filled($port) || ! filled($fromAddress) || ! filled($fromName)) {
            throw new RuntimeException("This company's SMTP settings are not configured.");
        }

        $encryptedPassword = Setting::get('smtp_password_encrypted');
        $password = filled($encryptedPassword) ? Crypt::decryptString($encryptedPassword) : null;
        $username = Setting::get('smtp_username');

        $scheme = match ($encryption) {
            'ssl'          => 'smtps',
            'tls', 'none'  => 'smtp',
            default        => throw new RuntimeException('The saved SMTP encryption setting is invalid.'),
        };

        config([
            'mail.mailers.tenant_runtime_smtp' => [
                'transport'    => 'smtp',
                'scheme'       => $scheme,
                'host'         => $host,
                'port'         => (int) $port,
                'username'     => filled($username) ? $username : null,
                'password'     => $password,
                'timeout'      => 30,
                'auto_tls'     => $encryption !== 'none',
                'local_domain' => parse_url(config('app.url'), PHP_URL_HOST),
            ],
        ]);

        // Clear any previously resolved instance so new credentials take effect
        Mail::purge('tenant_runtime_smtp');

        Mail::mailer('tenant_runtime_smtp')->html($html, function ($message) use ($toEmail, $toName, $subject, $fromAddress, $fromName) {
            $message->to($toEmail, $toName)
                ->subject($subject)
                ->from($fromAddress, $fromName);
        });
    }
}
