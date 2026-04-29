<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Attendance Report - Elite Guard</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-container {
            padding: 20px;
            border-bottom: 3px solid #1a237e;
            margin-bottom: 20px;
        }
        .logo {
            float: left;
            width: 150px;
        }
        .report-title {
            float: right;
            text-align: right;
        }
        .report-title h1 {
            margin: 0;
            color: #1a237e;
            font-size: 24px;
            text-transform: uppercase;
        }
        .report-title p {
            margin: 5px 0 0;
            color: #666;
            font-size: 12px;
        }
        .clearfix {
            clear: both;
        }
        .info-section {
            padding: 0 20px;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            width: 30%;
            float: left;
            margin-right: 2%;
        }
        .info-box:last-child {
            margin-right: 0;
        }
        .info-label {
            font-weight: bold;
            color: #1a237e;
            display: block;
            margin-bottom: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #1a237e;
            color: white;
            text-align: left;
            padding: 10px 8px;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        tr:nth-child(even) {
            background-color: #fcfcfc;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
        }
        .badge-active { background-color: #4caf50; }
        .badge-completed { background-color: #757575; }
        
        .total-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #1a237e;
            color: white;
            text-align: right;
        }
        .total-label {
            font-size: 14px;
            margin-right: 10px;
        }
        .total-value {
            font-size: 20px;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
        .text-muted { color: #888; }
        .small { font-size: 9px; }
    </style>
</head>
<body>
    <div class="header-container">
        <div class="logo">
            @if(file_exists(public_path('logo.png')))
                <img src="{{ public_path('logo.png') }}" style="max-height: 60px;">
            @else
                <h2 style="color: #1a237e; margin: 0;">ELITE GUARD</h2>
            @endif
        </div>
        <div class="report-title">
            <h1>Attendance Report</h1>
            <p>Generated on: {{ now()->format('M d, Y h:i A') }}</p>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="info-section">
        <div class="info-box">
            <span class="info-label">Report Period</span>
            <span>All Records</span>
        </div>
        <div class="info-box">
            <span class="info-label">Total Records</span>
            <span>{{ count($attendances) }} Entries</span>
        </div>
        <div class="info-box">
            <span class="info-label">Status</span>
            <span>Official Report</span>
        </div>
        <div class="clearfix"></div>
    </div>

    <div style="padding: 0 20px;">
        <table>
            <thead>
                <tr>
                    <th width="20%">Employee</th>
                    <th width="20%">Site (Company)</th>
                    <th width="15%">Shift Name</th>
                    <th width="15%">Clock In</th>
                    <th width="15%">Clock Out</th>
                    <th width="10%">Duration</th>
                    <th width="5%">Status</th>
                </tr>
            </thead>
            <tbody>
                @php $totalMinutes = 0; @endphp
                @forelse($attendances as $attendance)
                    <tr>
                        <td>
                            <strong>{{ $attendance->user->name }}</strong><br>
                            <span class="text-muted small">{{ $attendance->user->email }}</span>
                        </td>
                        <td>
                            {{ $attendance->shift->site->name ?? 'N/A' }}<br>
                            <span class="text-muted small">({{ $attendance->shift->site->company->name ?? 'N/A' }})</span>
                        </td>
                        <td>{{ $attendance->shift->shift_name ?? 'N/A' }}</td>
                        <td>{{ $attendance->clock_in_at ? $attendance->clock_in_at->format('Y-m-d H:i') : 'N/A' }}</td>
                        <td>{{ $attendance->clock_out_at ? $attendance->clock_out_at->format('Y-m-d H:i') : '-' }}</td>
                        <td>
                            @if($attendance->clock_in_at && $attendance->clock_out_at)
                                @php
                                    $diff = $attendance->clock_in_at->diff($attendance->clock_out_at);
                                    $totalMinutes += ($diff->h * 60) + $diff->i + ($diff->days * 24 * 60);
                                    echo $diff->format('%hh %im');
                                @endphp
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($attendance->status == 'active')
                                <span class="badge badge-active">In</span>
                            @else
                                <span class="badge badge-completed">Done</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="total-section">
        @php
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
        @endphp
        <span class="total-label">TOTAL ACCUMULATED DURATION:</span>
        <span class="total-value">{{ $hours }}h {{ $minutes }}m</span>
    </div>

    <div class="footer">
        This is an electronically generated report by Elite Guard Management System. 
        &copy; {{ date('Y') }} Elite Guard. Page 1 of 1
    </div>
</body>
</html>
