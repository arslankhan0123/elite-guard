<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { 
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; 
            line-height: 1.6; 
            color: #334155; 
            margin: 0; 
            padding: 0; 
            background-color: #f8fafc;
        }
        .wrapper {
            background-color: #f8fafc;
            padding: 40px 20px;
        }
        .container { 
            max-width: 1100px; 
            margin: 0 auto; 
            background: #ffffff;
            border-radius: 24px; 
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            border: 1px solid #e2e8f0;
        }
        .header { 
            background-color: #0f172a; 
            color: #ffffff;
            text-align: center; 
            padding: 60px 20px;
        }
        .header h1 { 
            margin: 0; 
            font-size: 36px; 
            font-weight: 800;
            letter-spacing: -0.05em;
            color: #ffffff;
            text-transform: uppercase;
        }
        .header p { 
            margin: 12px 0 0;
            font-size: 18px;
            color: #94a3b8;
            font-weight: 500;
            letter-spacing: 0.1em;
        }
        .content {
            padding: 50px;
        }
        .greeting {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 15px;
        }
        .intro {
            color: #64748b;
            margin-bottom: 50px;
            font-size: 17px;
        }
        .day-section {
            margin-bottom: 60px;
        }
        .day-heading { 
            font-size: 20px;
            font-weight: 900;
            color: #1e40af;
            background-color: #eff6ff;
            padding: 18px 30px;
            border-radius: 16px;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            margin-bottom: 30px;
            border-bottom: 5px solid #3b82f6;
            display: block;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        /* Grid Layout for Shifts */
        .shifts-container {
            display: block;
            width: 100%;
            text-align: left;
        }
        .site-card { 
            background: #ffffff; 
            padding: 24px; 
            border-radius: 18px; 
            margin-bottom: 25px; 
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 10px -1px rgba(0, 0, 0, 0.08);
            display: inline-block;
            vertical-align: top;
            width: 31%; /* Slightly less than 33% to account for spacing */
            margin-right: 2%;
            box-sizing: border-box;
            position: relative;
        }
        /* No margin for the last item in a row of 3 */
        .site-card:nth-child(3n) {
            margin-right: 0;
        }
        .site-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 25px;
            bottom: 25px;
            width: 6px;
            background: #cbd5e1;
            border-radius: 0 6px 6px 0;
        }
        .shift-title { 
            font-weight: 800; 
            color: #0f172a; 
            font-size: 18px;
            margin-bottom: 8px;
            padding-left: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .shift-time-badge {
            display: inline-block;
            background: #f1f5f9;
            color: #475569;
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 20px;
            margin-left: 10px;
            border: 1px solid #e2e8f0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-left: 10px;
        }
        .info-label {
            color: #94a3b8;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            width: 75px;
            padding: 5px 0;
            vertical-align: top;
        }
        .info-value {
            color: #334155;
            font-size: 14px;
            font-weight: 600;
            padding: 5px 0;
            line-height: 1.4;
        }
        .notes-box { 
            background: #fffbeb; 
            border: 2px dashed #fcd34d; 
            padding: 35px; 
            border-radius: 20px; 
            margin-top: 60px;
        }
        .notes-title {
            color: #92400e;
            font-weight: 900;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 15px;
            display: block;
        }
        .footer { 
            text-align: center; 
            padding: 50px 20px; 
            font-size: 14px; 
            color: #94a3b8; 
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
        }
        @media only screen and (max-width: 900px) {
            .site-card { width: 48%; margin-right: 3%; }
            .site-card:nth-child(2n) { margin-right: 0; }
            .site-card:nth-child(3n) { margin-right: 3%; } /* Reset nth-child(3n) */
        }
        @media only screen and (max-width: 600px) {
            .content { padding: 30px 20px; }
            .site-card { width: 100%; margin-right: 0; }
            .day-heading { padding: 15px 20px; font-size: 17px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Weekly Roster</h1>
                <p>{{ $weekStart->format('d M') }} — {{ $weekEnd->format('d M, Y') }}</p>
            </div>

            <div class="content">
                <div class="greeting">Hello {{ $user->name }},</div>
                <div class="intro">
                    Your duty schedule for the upcoming week has been finalized. Please note your assigned shifts below:
                </div>

                @php
                    $groupedShifts = $schedule->shifts->sortBy('date')->groupBy(function($shift) {
                        return \Carbon\Carbon::parse($shift->date)->format('Y-m-d');
                    });
                @endphp

                @foreach($groupedShifts as $dateString => $dayShifts)
                    <div class="day-section">
                        <div class="day-heading">
                            📅 {{ \Carbon\Carbon::parse($dateString)->format('l, d F') }}
                        </div>
                        
                        <div class="shifts-container">
                            @foreach($dayShifts as $shift)
                                <div class="site-card">
                                    <div class="shift-title" title="{{ $shift->shift_name }}">{{ $shift->shift_name }}</div>
                                    <div class="shift-time-badge">
                                        ⏰ {{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }} — {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}
                                    </div>
                                    
                                    <table class="info-table">
                                        <tr>
                                            <td class="info-label">Site</td>
                                            <td class="info-value">{{ $shift->site->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="info-label">Company</td>
                                            <td class="info-value">{{ $shift->site->company->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="info-label">Location</td>
                                            <td class="info-value">{{ $shift->site->address }}, {{ $shift->site->city }}</td>
                                        </tr>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @if($schedule->notes)
                    <div class="notes-box">
                        <span class="notes-title">Weekly Instructions</span>
                        <div style="color: #78350f; font-size: 16px; line-height: 1.6; font-weight: 500;">{{ $schedule->notes }}</div>
                    </div>
                @endif

                <div style="margin-top: 60px; padding: 30px; background: #f8fafc; border-radius: 20px; color: #64748b; font-size: 15px; border: 1px solid #edf2f7;">
                    <p style="margin-top: 0;"><strong>Important Notice:</strong></p>
                    <ul style="margin-bottom: 0; padding-left: 20px; line-height: 1.8;">
                        <li>Punctuality is mandatory. Arrive 15 minutes early.</li>
                        <li>Maintain full uniform standards at all times.</li>
                        <li>Report any incidents to your supervisor immediately.</li>
                    </ul>
                </div>
            </div>

            <div class="footer">
                <strong style="color: #475569; font-size: 16px;">Elite Guard Inc.</strong><br>
                Professional Security Services<br>
                &copy; {{ date('Y') }} All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
