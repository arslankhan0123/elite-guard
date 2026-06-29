<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 0; background-color: #f8fafc; }
        .wrapper { width: 100%; table-layout: fixed; padding-bottom: 40px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; margin-top: 40px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); padding: 40px 20px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.025em; color: #ffffff !important; }
        .header p { color: #ffffff !important; opacity: 0.9; margin-top: 10px; }
        .content { padding: 40px; }
        .greeting { font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #0f172a; }
        .welcome-text { font-size: 15px; color: #475569; margin-bottom: 24px; }
        .credentials-box { background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%); border: 1px solid #c4b5fd; border-radius: 12px; padding: 24px; margin-bottom: 30px; }
        .credentials-title { font-size: 13px; font-weight: 700; color: #7c3aed; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 16px; }
        .credential-item { display: flex; align-items: center; margin-bottom: 14px; padding: 12px 16px; background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; }
        .credential-label { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; width: 80px; flex-shrink: 0; }
        .credential-value { font-size: 15px; color: #1e293b; font-weight: 700; font-family: monospace; letter-spacing: 0.05em; }
        .notice-box { background-color: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px; }
        .notice-box p { margin: 0; font-size: 13px; color: #92400e; }
        .app-url { display: block; text-align: center; margin: 24px 0; padding: 14px 28px; background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); color: #ffffff !important; text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 15px; }
        .footer { text-align: center; padding: 30px; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1 style="color: #ffffff;">ELITE GUARD MANAGEMENT</h1>
                <p style="color: #ffffff;">Welcome to the Team! 🎉</p>
            </div>

            <div class="content">
                <p class="greeting">Hello {{ $user->name }},</p>
                <p class="welcome-text">
                    We are thrilled to welcome you to the Elite Guard Management team! Your account has been created and you can now log in to the employee portal using the credentials below.
                </p>

                <div class="credentials-box">
                    <div class="credentials-title">🔑 Your Login Credentials</div>
                    <div class="credential-item">
                        <span class="credential-label">Email</span>
                        <span class="credential-value">{{ $user->email }}</span>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">Password</span>
                        <span class="credential-value">{{ $user->real_password }}</span>
                    </div>
                </div>

                <div class="notice-box">
                    <p>⚠️ <strong>Important:</strong> Please keep your login credentials confidential. We recommend changing your password after your first login for security purposes.</p>
                </div>

                <a href="{{ config('app.url') }}" class="app-url" style="color: #ffffff;">
                    Login to Employee Portal →
                </a>

                <p style="margin-top: 24px; font-size: 14px; color: #64748b;">
                    If you have any questions or need assistance, please contact your hiring manager or our HR department.
                </p>
                <p><strong>Best regards,</strong><br>Elite Guard Management Team</p>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} Elite Guard Management. All rights reserved.<br>
                This is an automated official communication. Please do not reply.
            </div>
        </div>
    </div>
</body>
</html>
