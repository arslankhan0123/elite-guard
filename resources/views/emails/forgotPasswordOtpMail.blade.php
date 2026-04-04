<!DOCTYPE html>
<html>

<head>
    <title>Elite Guard Password Reset Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
        }

        .card {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            text-align: center;
            border: 1px solid #e0e0e0;
        }

        .header {
            background: linear-gradient(to right, #D61DD2, #10D8F7);
            color: #ffffff;
            padding: 15px;
            font-size: 20px;
            font-weight: bold;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .otp {
            font-size: 24px;
            font-weight: bold;
            color: #007BFF;
            background: #f1f8ff;
            display: inline-block;
            padding: 12px 24px;
            margin: 20px 0;
            border-radius: 8px;
            border: 2px dashed #007BFF;
        }

        .footer {
            font-size: 12px;
            color: #888888;
            margin-top: 20px;
        }

        a {
            color: #007BFF;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="header">Elite Guard Password Reset Request</div>
            <p>Dear {{ $name }},</p>
            <p>Your OTP code for Elite Guard to reset password is:</p>
            <div class="otp">{{ $otp }}</div>
            <p>Enter this code to proceed with password reset. This OTP is valid for 10 minutes.</p>
            <p>If you didn't request a password reset, please contact our support team at
                <a href="mailto:support@quicknews.global">support@quicknews.global</a>.
            </p>
            <p>Best regards,<br><strong>Elite Guard Team</strong></p>
            <div class="footer">
                &copy; {{ date('Y') }} Elite Guard LLC. All Rights Reserved.
            </div>
        </div>
    </div>
</body>

</html>