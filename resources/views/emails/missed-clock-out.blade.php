<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Missed Clock-Out Alert</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:30px 0;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);">

        <!-- Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#d97706,#b45309);padding:32px 40px;text-align:center;">
            <h1 style="margin:0;color:#fff;font-size:22px;font-weight:700;letter-spacing:-0.3px;">
              ⏰ Missed Clock-Out Alert
            </h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.8);font-size:14px;">Elite Guard Security Management</p>
          </td>
        </tr>

        <!-- Body -->
        <tr>
          <td style="padding:36px 40px;">
            <p style="margin:0 0 20px;color:#374151;font-size:15px;line-height:1.6;">
              Hello Admin,
            </p>
            <p style="margin:0 0 24px;color:#374151;font-size:15px;line-height:1.6;">
              A guard has <strong style="color:#d97706;">not clocked out</strong> for their scheduled shift. Please review the details below:
            </p>

            <!-- Details Card -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:24px;">
              <tr>
                <td style="padding:20px 24px;">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;">
                        <span style="color:#6b7280;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Guard</span>
                        <br>
                        <span style="color:#111827;font-size:15px;font-weight:600;">{{ $guardName }}</span>
                      </td>
                    </tr>
                    <tr>
                      <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;">
                        <span style="color:#6b7280;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Site</span>
                        <br>
                        <span style="color:#111827;font-size:15px;font-weight:600;">{{ $siteName }}</span>
                      </td>
                    </tr>
                    <tr>
                      <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;">
                        <span style="color:#6b7280;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Shift</span>
                        <br>
                        <span style="color:#111827;font-size:15px;font-weight:600;">{{ $shiftName }}</span>
                      </td>
                    </tr>
                    <tr>
                      <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;">
                        <span style="color:#6b7280;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Scheduled End Time</span>
                        <br>
                        <span style="color:#111827;font-size:15px;font-weight:600;">{{ $shiftDate }} at {{ $endTime }}</span>
                      </td>
                    </tr>
                    <tr>
                      <td style="padding:8px 0;">
                        <span style="color:#6b7280;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Status</span>
                        <br>
                        <span style="color:#d97706;font-size:15px;font-weight:700;">{{ $timeContext }}</span>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <p style="margin:0 0 24px;color:#6b7280;font-size:14px;line-height:1.6;">
              Please take appropriate action. This alert is automatically generated when no clock-out is detected within 10 minutes before the scheduled shift end time.
            </p>

            <!-- CTA Button -->
            <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
              <tr>
                <td align="center" style="border-radius:8px;background:#d97706;">
                  <a href="{{ $dashboardUrl }}" style="display:inline-block;padding:12px 28px;color:#fff;text-decoration:none;font-size:15px;font-weight:600;border-radius:8px;">
                    View Dashboard →
                  </a>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background:#f8fafc;padding:20px 40px;text-align:center;border-top:1px solid #e2e8f0;">
            <p style="margin:0;color:#9ca3af;font-size:12px;">
              © {{ date('Y') }} Elite Guard Security Management · Automated Alert System
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
