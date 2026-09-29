<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 0; background-color: #f8fafc; }
        .wrapper { width: 100%; table-layout: fixed; padding-bottom: 40px; }
        .container { max-width: 620px; margin: 40px auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%); padding: 44px 20px 36px; text-align: center; color: #ffffff; }
        .header-icon { font-size: 48px; margin-bottom: 12px; }
        .header h1 { margin: 0 0 8px; font-size: 22px; font-weight: 800; letter-spacing: -0.025em; }
        .header p { margin: 0; opacity: 0.85; font-size: 14px; }
        .badge-pill { display: inline-block; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); border-radius: 50px; padding: 4px 16px; font-size: 12px; font-weight: 600; margin-top: 14px; letter-spacing: 0.04em; text-transform: uppercase; }
        .content { padding: 40px; }
        .greeting { font-size: 17px; font-weight: 700; margin-bottom: 16px; color: #0f172a; }
        .welcome-text { font-size: 14px; color: #475569; margin-bottom: 28px; line-height: 1.8; }
        .info-card { background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%); border: 1px solid #c4b5fd; border-radius: 14px; padding: 26px; margin-bottom: 28px; }
        .info-card-title { font-size: 11px; font-weight: 800; color: #7c3aed; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
        .info-row { display: flex; align-items: flex-start; margin-bottom: 12px; padding: 11px 14px; background: #fff; border-radius: 10px; border: 1px solid #e2e8f0; }
        .info-row:last-child { margin-bottom: 0; }
        .info-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; min-width: 110px; flex-shrink: 0; padding-top: 2px; }
        .info-value { font-size: 14px; color: #1e293b; font-weight: 600; }
        .status-badge { display: inline-block; padding: 3px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; text-transform: capitalize; }
        .status-active { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .status-inactive { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }
        .status-suspended { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .notice-box { background-color: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 10px; padding: 16px 20px; margin-bottom: 28px; }
        .notice-box p { margin: 0; font-size: 13px; color: #1e40af; line-height: 1.7; }
        .cta-btn { display: block; text-align: center; margin: 0 0 28px; padding: 15px 28px; background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%); color: #ffffff !important; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 15px; letter-spacing: 0.01em; }
        .divider { border: none; border-top: 1px solid #f1f5f9; margin: 28px 0; }
        .footer { text-align: center; padding: 24px 30px; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; background: #fafafa; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <div class="header-icon">🏢</div>
                <h1>ELITE GUARD MANAGEMENT</h1>
                <p>{{ $isNew ? 'New Tenant Account Created' : 'Tenant Account Updated' }}</p>
                <div class="badge-pill">{{ $isNew ? '✅ Account Activated' : '🔄 Account Updated' }}</div>
            </div>

            <div class="content">
                <p class="greeting">Hello {{ $tenant->contact_person }},</p>

                @if($isNew)
                <p class="welcome-text">
                    Welcome to <strong>Elite Guard Management</strong>! Your tenant account has been successfully created by our MasterAdmin team.
                    Below are the details of your newly registered account. Please review and keep this information safe.
                </p>
                @else
                <p class="welcome-text">
                    Your tenant account with <strong>Elite Guard Management</strong> has been updated. Below you'll find the latest details for your account.
                    If you did not request these changes, please contact us immediately.
                </p>
                @endif

                <div class="info-card">
                    <div class="info-card-title">🔑 Account Details</div>

                    <div class="info-row">
                        <span class="info-label">Company</span>
                        <span class="info-value">{{ $tenant->company_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Contact</span>
                        <span class="info-value">{{ $tenant->contact_person }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ $tenant->email }}</span>
                    </div>
                    @if($tenant->phone)
                    <div class="info-row">
                        <span class="info-label">Phone</span>
                        <span class="info-value">{{ $tenant->phone }}</span>
                    </div>
                    @endif
                    @if($tenant->city || $tenant->country)
                    <div class="info-row">
                        <span class="info-label">Location</span>
                        <span class="info-value">{{ collect([$tenant->city, $tenant->country])->filter()->implode(', ') }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">Plan</span>
                        <span class="info-value">{{ ucfirst($tenant->plan) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            <span class="status-badge status-{{ $tenant->status }}">{{ ucfirst($tenant->status) }}</span>
                        </span>
                    </div>
                    @if($tenant->subscription_starts_at)
                    <div class="info-row">
                        <span class="info-label">Subscription</span>
                        <span class="info-value">
                            {{ $tenant->subscription_starts_at->format('d M Y') }}
                            @if($tenant->subscription_ends_at)
                                → {{ $tenant->subscription_ends_at->format('d M Y') }}
                            @endif
                        </span>
                    </div>
                    @endif
                    @if($password)
                    <div class="info-row" style="background:#f0fdf4; border-color:#bbf7d0;">
                        <span class="info-label" style="color:#166534;">Password</span>
                        <span class="info-value" style="color:#15803d; font-weight:700; font-family:monospace; font-size:15px;">{{ $password }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Assigned Role</span>
                        <span class="info-value" style="color:#4338ca; font-weight:700;">SuperAdmin</span>
                    </div>
                    @endif
                </div>

                <div class="notice-box">
                    <p>ℹ️ <strong>Note:</strong> For any queries regarding your account, billing, or platform access, please reach out to our support team directly. Do not reply to this email.</p>
                </div>

                <a href="{{ config('app.url') }}" class="cta-btn" style="color: #ffffff;">
                    Visit Elite Guard Platform →
                </a>

                <p style="font-size: 14px; color: #64748b; margin: 0;">
                    Best regards,<br>
                    <strong>Elite Guard Management Team</strong>
                </p>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} Elite Guard Management. All rights reserved.<br>
                This is an automated official communication. Please do not reply.
            </div>
        </div>
    </div>
</body>
</html>
