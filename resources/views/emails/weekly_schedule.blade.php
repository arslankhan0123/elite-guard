<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 8px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #2c3e50; margin-bottom: 5px; }
        .header p { color: #7f8c8d; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 50px; background: #e8f4fd; color: #2980b9; font-size: 14px; font-weight: bold; }
        .site-card { background: #f9f9f9; padding: 15px; border-radius: 6px; margin-bottom: 15px; border-left: 4px solid #3498db; }
        .site-name { font-weight: bold; color: #2c3e50; font-size: 16px; margin-bottom: 5px; }
        .site-company { color: #7f8c8d; font-size: 13px; margin-bottom: 5px; }
        .site-address { color: #34495e; font-size: 14px; display: flex; align-items: center; }
        .footer { text-align: center; margin-top: 40px; font-size: 12px; color: #bdc3c7; }
        .notes { font-style: italic; color: #7f8c8d; margin-top: 10px; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Weekly Schedule</h1>
            <p>{{ $weekStart->format('d M') }} - {{ $weekEnd->format('d M, Y') }}</p>
        </div>

        <p>Hello <strong>{{ $user->name }}</strong>,</p>
        <p>You have been assigned to <strong>{{ $schedules->count() }}</strong> sites for the upcoming week. Please find the details below:</p>

        @foreach($schedules as $schedule)
            <div class="site-card">
                <div class="site-name">{{ $schedule->site->name }}</div>
                <div class="site-company">Company: {{ $schedule->site->company->name ?? 'N/A' }}</div>
                <div class="site-address">
                    Address: {{ $schedule->site->address }}, {{ $schedule->site->city }}, {{ $schedule->site->country }}
                </div>
                @if($schedule->notes)
                    <div class="notes">Notes: {{ $schedule->notes }}</div>
                @endif
            </div>
        @endforeach

        <p>Please ensure you are present at your assigned sites on time. If you have any questions, contact your supervisor.</p>

        <div class="footer">
            &copy; {{ date('Y') }} Elite Guard Management. All rights reserved.
        </div>
    </div>
</body>
</html>
