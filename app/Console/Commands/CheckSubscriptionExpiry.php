<?php

namespace App\Console\Commands;

use App\Models\Master\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckSubscriptionExpiry extends Command
{
    protected $signature   = 'subscriptions:check-expiry';
    protected $description = 'Check tenant subscription/trial expiry, send email notifications and deactivate expired tenants';

    public function handle(): int
    {
        $this->info('Checking subscription expiry...');

        $now = now();

        // -------------------------------------------------------
        // 1. Deactivate tenants whose subscription/trial has ended
        // -------------------------------------------------------
        $expired = Tenant::on('master')
            ->where('is_active', true)
            ->whereNotNull('subscription_ends_at')
            ->where('subscription_ends_at', '<', $now)
            ->get();

        foreach ($expired as $tenant) {
            $tenant->update([
                'is_active'        => false,
                'expiry_notified'  => true,
            ]);

            $this->sendExpiryEmail($tenant, 'deactivated');

            $this->info("Deactivated: {$tenant->name} (expired {$tenant->subscription_ends_at->toDateString()})");
            Log::info("Tenant deactivated due to subscription expiry", [
                'tenant_id'   => $tenant->id,
                'tenant_name' => $tenant->name,
                'ended_at'    => $tenant->subscription_ends_at,
            ]);
        }

        // -------------------------------------------------------
        // 2. Send warning email to tenants expiring within 3 days
        //    (only once — skip if already notified)
        // -------------------------------------------------------
        $expiringSoon = Tenant::on('master')
            ->where('is_active', true)
            ->where('expiry_notified', false)
            ->whereNotNull('subscription_ends_at')
            ->whereBetween('subscription_ends_at', [$now, $now->copy()->addDays(3)])
            ->get();

        foreach ($expiringSoon as $tenant) {
            $tenant->update(['expiry_notified' => true]);

            $this->sendExpiryEmail($tenant, 'warning');

            $this->info("Warning sent: {$tenant->name} (expires {$tenant->subscription_ends_at->toDateString()})");
            Log::info("Subscription expiry warning sent", [
                'tenant_id'   => $tenant->id,
                'tenant_name' => $tenant->name,
                'ends_at'     => $tenant->subscription_ends_at,
            ]);
        }

        $this->info('Done.');
        return self::SUCCESS;
    }

    private function sendExpiryEmail(Tenant $tenant, string $type): void
    {
        try {
            $subject = $type === 'deactivated'
                ? "Your Elite Guard subscription has expired — account deactivated"
                : "Your Elite Guard subscription expires in 3 days";

            $planLabel = match ($tenant->subscription_type) {
                'monthly' => 'Monthly ($350 CAD)',
                'yearly'  => 'Yearly ($3,990 CAD)',
                default   => 'Trial',
            };

            $bodyLines = $type === 'deactivated'
                ? [
                    "Your <strong>{$planLabel}</strong> subscription for <strong>{$tenant->name}</strong> expired on <strong>{$tenant->subscription_ends_at->format('d M Y')}</strong>.",
                    "Your account has been <span style='color:#dc3545;'>deactivated</span>.",
                    "To reactivate your account, please contact us to renew your subscription.",
                ]
                : [
                    "Your <strong>{$planLabel}</strong> subscription for <strong>{$tenant->name}</strong> will expire on <strong>{$tenant->subscription_ends_at->format('d M Y')}</strong>.",
                    "Please renew your subscription to avoid any service interruption.",
                    "Contact us to renew: <a href='mailto:support@eliteguardinc.com'>support@eliteguardinc.com</a>",
                ];

            $html = view('emails.subscription-expiry', [
                'tenant'    => $tenant,
                'type'      => $type,
                'subject'   => $subject,
                'planLabel' => $planLabel,
                'bodyLines' => $bodyLines,
            ])->render();

            Mail::mailer('master_smtp')->html($html, function ($message) use ($tenant, $subject) {
                $message->to($tenant->admin_email, $tenant->name)
                        ->subject($subject)
                        ->from(
                            env('MASTER_MAIL_FROM_ADDRESS', config('mail.from.address')),
                            env('MASTER_MAIL_FROM_NAME', 'Elite Guard')
                        );
            });
        } catch (\Throwable $e) {
            Log::error("Failed to send subscription expiry email to {$tenant->admin_email}: " . $e->getMessage());
            $this->warn("Email failed for {$tenant->name}: " . $e->getMessage());
        }
    }
}
