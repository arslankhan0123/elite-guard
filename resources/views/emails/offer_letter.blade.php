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
        .details-box { background-color: #f1f5f9; border-radius: 12px; padding: 24px; margin-bottom: 30px; }
        .detail-item { margin-bottom: 15px; }
        .detail-label { font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .detail-value { font-size: 16px; color: #1e293b; font-weight: 600; }
        .description-title { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 12px; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; }
        .description-content { font-size: 15px; color: #334155; white-space: pre-line; background: #fff; border: 1px solid #f1f5f9; padding: 15px; border-radius: 8px; }
        .footer { text-align: center; padding: 30px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1 style="color: #ffffff;">ELITE GUARD MANAGEMENT</h1>
                <p style="color: #ffffff;">Official Employment Offer</p>
            </div>
            
            <div class="content">
                <p class="greeting">Hello {{ $user->name }},</p>
                <p>We are excited to formally offer you a position with Elite Guard Management. Below are the details regarding your employment:</p>
                
                <div class="details-box">
                    <div class="detail-item">
                        <div class="detail-label">Job Position</div>
                        <div class="detail-value">{{ $offer->job_title ?? 'Security Personnel' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Joining Date</div>
                        <div class="detail-value">{{ $offer->joining_date ? \Carbon\Carbon::parse($offer->joining_date)->format('F d, Y') : 'To be determined' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Salary / Wage</div>
                        <div class="detail-value">{{ $offer->salary ?? 'Competitive' }}</div>
                    </div>
                </div>
                
                @if($offer->description)
                    <div class="description-title">Terms & Job Description</div>
                    <div class="description-content">{{ $offer->description }}</div>
                @endif
                
                <p style="margin-top: 30px;">If you have any questions, please feel free to reach out to our HR department or your hiring manager.</p>
                
                <p>We look forward to having you on board!</p>
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
