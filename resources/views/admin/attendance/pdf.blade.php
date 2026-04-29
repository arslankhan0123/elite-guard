<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elite Guard - Official Attendance Report</title>
    <style>
        /* PDF Specific Reset */
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #2c3e50;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        /* Top Accent Bar */
        .top-accent {
            height: 8px;
            background-color: #1a237e;
            width: 100%;
        }

        /* Header Layout */
        .header {
            padding: 40px 50px 20px;
        }
        .logo-section {
            float: left;
            width: 50%;
        }
        .meta-section {
            float: right;
            width: 50%;
            text-align: right;
        }
        .logo-img {
            max-height: 70px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #1a237e;
            margin-top: 10px;
            letter-spacing: 1px;
        }
        .report-label {
            font-size: 28px;
            font-weight: 300;
            color: #bdc3c7;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .report-id {
            font-size: 12px;
            color: #7f8c8d;
        }

        /* Summary Dashboard */
        .dashboard {
            margin: 0 50px 30px;
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px 40px;
            border: 1px solid #ebedef;
        }
        .dash-item {
            float: left;
            width: 25%;
            border-right: 1px solid #ebedef;
            padding: 0 15px;
        }
        .dash-item:last-child {
            border-right: none;
        }
        .dash-val {
            font-size: 16px;
            font-weight: bold;
            color: #1a237e;
            display: block;
        }
        .dash-label {
            font-size: 9px;
            color: #95a5a6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Table Styling */
        .content {
            padding: 0 50px;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }
        th {
            background-color: #f1f4f9;
            color: #1a237e;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 12px 15px;
            border: none;
        }
        th:first-child {
            border-top-left-radius: 40px;
            border-bottom-left-radius: 40px;
        }
        th:last-child {
            border-top-right-radius: 40px;
            border-bottom-right-radius: 40px;
        }
        tr.data-row td {
            background-color: #fff;
            border-top: 1px solid #f1f4f9;
            border-bottom: 1px solid #f1f4f9;
            padding: 12px 15px;
        }
        tr.data-row td:first-child {
            border-left: 1px solid #f1f4f9;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }
        tr.data-row td:last-child {
            border-right: 1px solid #f1f4f9;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }
        .emp-name {
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
        }
        .site-info {
            color: #7f8c8d;
            font-size: 9px;
        }
        .duration-pill {
            background-color: #e8eaf6;
            color: #1a237e;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: bold;
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 4px;
            color: #fff;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
        }
        .bg-active { background-color: #27ae60; }
        .bg-completed { background-color: #95a5a6; }

        /* Grand Total */
        .grand-total {
            margin-top: 30px;
            background-color: #1a237e;
            color: #fff;
            padding: 15px 40px;
            border-radius: 40px;
            text-align: right;
        }
        .total-text {
            font-size: 12px;
            opacity: 0.8;
            margin-right: 15px;
        }
        .total-val {
            font-size: 22px;
            font-weight: bold;
        }

        /* Signatures */
        .signatures {
            margin-top: 50px;
            padding: 0 50px;
        }
        .sig-box {
            float: left;
            width: 30%;
            text-align: center;
        }
        .sig-line {
            border-top: 1px solid #bdc3c7;
            margin-top: 40px;
            padding-top: 5px;
            color: #7f8c8d;
            font-size: 9px;
        }

        /* Utils */
        .clearfix { clear: both; }
        .footer {
            position: absolute;
            bottom: 30px;
            width: 100%;
            text-align: center;
            color: #bdc3c7;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="top-accent"></div>

    <div class="header">
        <div class="logo-section">
            @if(file_exists(public_path('logo.png')))
                <img src="{{ public_path('logo.png') }}" class="logo-img">
            @else
                <div class="company-name">ELITE GUARD</div>
            @endif
        </div>
        <div class="meta-section">
            <div class="report-label">Attendance Report</div>
            <div class="report-id">Ref: EG-{{ now()->format('Ymd-His') }}</div>
            <div class="report-id">Date: {{ now()->format('F d, Y') }}</div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="dashboard">
        <div class="dash-item">
            <span class="dash-val">{{ count($attendances) }}</span>
            <span class="dash-label">Total Logs</span>
        </div>
        <div class="dash-item">
            <span class="dash-val">{{ $attendances->where('status', 'active')->count() }}</span>
            <span class="dash-label">Active Shifts</span>
        </div>
        <div class="dash-item">
            <span class="dash-val">{{ $attendances->where('status', 'completed')->count() }}</span>
            <span class="dash-label">Completed</span>
        </div>
        <div class="dash-item">
            <span class="dash-val">{{ now()->format('H:i') }}</span>
            <span class="dash-label">Export Time</span>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="content">
        <table>
            <thead>
                <tr>
                    <th align="left">Employee Details</th>
                    <th align="left">Location & Shift</th>
                    <th align="center">Time Log</th>
                    <th align="center">Duration</th>
                    <th align="center">Status</th>
                </tr>
            </thead>
            <tbody>
                @php $totalMinutes = 0; @endphp
                @foreach($attendances as $attendance)
                    <tr class="data-row">
                        <td>
                            <span class="emp-name">{{ $attendance->user->name }}</span><br>
                            <span class="site-info">{{ $attendance->user->email }}</span>
                        </td>
                        <td>
                            <span class="emp-name">{{ $attendance->shift->site->name ?? 'N/A' }}</span><br>
                            <span class="site-info">{{ $attendance->shift->shift_name ?? 'N/A' }}</span>
                        </td>
                        <td align="center">
                            <span class="site-info">In: {{ $attendance->clock_in_at ? $attendance->clock_in_at->format('H:i') : '-' }}</span><br>
                            <span class="site-info">Out: {{ $attendance->clock_out_at ? $attendance->clock_out_at->format('H:i') : '-' }}</span>
                        </td>
                        <td align="center">
                            @if($attendance->clock_in_at && $attendance->clock_out_at)
                                @php
                                    $diff = $attendance->clock_in_at->diff($attendance->clock_out_at);
                                    $totalMinutes += ($diff->h * 60) + $diff->i + ($diff->days * 24 * 60);
                                @endphp
                                <span class="duration-pill">{{ $diff->format('%hh %im') }}</span>
                            @else
                                <span class="site-info">-</span>
                            @endif
                        </td>
                        <td align="center">
                            @if($attendance->status == 'active')
                                <span class="status-badge bg-active">Active</span>
                            @else
                                <span class="status-badge bg-completed">Completed</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="grand-total">
            @php
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;
            @endphp
            <span class="total-text">TOTAL ACCUMULATED WORK HOURS</span>
            <span class="total-val">{{ $hours }}h {{ $minutes }}m</span>
        </div>
    </div>

    <div class="signatures">
        <div class="sig-box">
            <div class="sig-line">Prepared By</div>
        </div>
        <div class="sig-box" style="margin-left: 5%;">
            <div class="sig-line">Manager Review</div>
        </div>
        <div class="sig-box" style="float: right;">
            <div class="sig-line">Company Stamp</div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="footer">
        Elite Guard Security Management - Confidential Document - Page 1 of 1
    </div>
</body>
</html>
