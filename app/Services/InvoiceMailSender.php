<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class InvoiceMailSender
{
    public function send(Invoice $invoice, string $recipientEmail): void
    {
        $host = Setting::get('smtp_host');
        $port = Setting::get('smtp_port');
        $encryption = Setting::get('smtp_encryption');
        $fromAddress = Setting::get('smtp_from_address');
        $fromName = Setting::get('smtp_from_name');

        if (! filled($host) || ! filled($port) || ! filled($fromAddress) || ! filled($fromName)) {
            throw new RuntimeException('Configure and save this company\'s SMTP settings before sending invoices.');
        }

        $encryptedPassword = Setting::get('smtp_password_encrypted');
        $password = filled($encryptedPassword) ? Crypt::decryptString($encryptedPassword) : null;
        $username = Setting::get('smtp_username');
        $scheme = match ($encryption) {
            'ssl' => 'smtps',
            'tls', 'none' => 'smtp',
            default => throw new RuntimeException('The saved SMTP encryption setting is invalid.'),
        };

        config([
            'mail.mailers.tenant_invoice_smtp' => [
                'transport' => 'smtp',
                'scheme' => $scheme,
                'host' => $host,
                'port' => (int) $port,
                'username' => filled($username) ? $username : null,
                'password' => $password,
                'timeout' => 30,
                'auto_tls' => $encryption !== 'none',
                'local_domain' => parse_url(config('app.url'), PHP_URL_HOST),
            ],
        ]);

        Mail::purge('tenant_invoice_smtp');

        Mail::mailer('tenant_invoice_smtp')
            ->to($recipientEmail)
            ->send((new InvoiceMail($invoice))->from($fromAddress, $fromName));
    }
}
