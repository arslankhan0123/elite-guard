<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 12px 18px;
        }
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #334155;
            font-size: 10px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        .card-header-sky {
            background-color: #e0f2fe; /* Light Sky Blue */
            border: 1px solid #bae6fd;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 6px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            padding: 0;
            vertical-align: middle;
        }
        .logo-img {
            max-height: 35px;
            margin-right: 8px;
        }
        .company-title {
            font-size: 15px;
            font-weight: bold;
            color: #0369a1;
            letter-spacing: 0.01em;
        }
        .company-subtitle {
            font-size: 8px;
            color: #0284c7;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .report-title-cell {
            text-align: right;
        }
        .report-badge {
            font-size: 10.5px;
            font-weight: bold;
            color: #0369a1;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .report-id-text {
            color: #0284c7;
            font-size: 8.5px;
            font-weight: bold;
        }
        .card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 10px;
            margin-bottom: 6px;
        }
        .card-title {
            font-size: 9px;
            font-weight: bold;
            color: #0369a1;
            background-color: #f0f9ff;
            border: 1px solid #e0f2fe;
            padding: 3px 6px;
            margin-bottom: 5px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 5px;
            border: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .meta-label {
            font-weight: bold;
            color: #64748b;
            font-size: 8px;
            text-transform: uppercase;
            width: 15%;
            background-color: #f8fafc;
        }
        .meta-value {
            color: #0f172a;
            width: 35%;
        }
        .split-layout {
            width: 100%;
            border-collapse: collapse;
        }
        .split-layout td {
            padding: 0;
            vertical-align: top;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 3px 5px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .field-name {
            font-weight: bold;
            color: #64748b;
            font-size: 8px;
            text-transform: uppercase;
            width: 40%;
        }
        .field-value {
            color: #0f172a;
            width: 60%;
        }
        .patrol-table {
            width: 100%;
            border-collapse: collapse;
        }
        .patrol-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7.5px;
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }
        .patrol-table td {
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
            color: #334155;
        }
        .signature-section {
            width: 100%;
            margin-top: 5px;
        }
        .signature-box {
            float: right;
            width: 150px;
            text-align: center;
            border-top: 1px dashed #cbd5e1;
            padding-top: 2px;
            margin-top: 5px;
        }
        .signature-img {
            max-height: 25px;
            margin-bottom: 1px;
        }
        .signature-name {
            font-family: 'Dancing Script', cursive, Georgia, serif;
            font-size: 12px;
            color: #0284c7;
            font-weight: bold;
        }
        .footer {
            clear: both;
            text-align: center;
            font-size: 7.5px;
            color: #94a3b8;
            margin-top: 8px;
            padding-top: 3px;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

    <!-- Sky Blue Header Card -->
    <div class="card-header-sky">
        <table class="header-table">
            <tr>
                <td style="width: 45px;">
                    <img src="{{ public_path('logo.png') }}" class="logo-img" alt="Logo">
                </td>
                <td>
                    <div class="company-title">ELITE GUARD INC.</div>
                    <div class="company-subtitle">Premium Security Services & Solutions</div>
                </td>
                <td class="report-title-cell">
                    <div class="report-badge">{{ $title }}</div>
                    <div class="report-id-text">RECORD ID: #{{ $report->id }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Submission Card -->
    <div class="card">
        <div class="card-title">Submission Information</div>
        <table class="meta-table">
            <tr>
                <td class="meta-label">Date Generated</td>
                <td class="meta-value">{{ now()->format('M d, Y, h:i A') }}</td>
                <td class="meta-label">Submitted At</td>
                <td class="meta-value">{{ $report->created_at->format('M d, Y, h:i A') }}</td>
            </tr>
            @if($report->user)
                <tr>
                    <td class="meta-label">Reported By</td>
                    <td class="meta-value">{{ $report->user->name }}</td>
                    <td class="meta-label">Email</td>
                    <td class="meta-value">{{ $report->user->email }}</td>
                </tr>
            @endif
        </table>
    </div>

    <!-- Details Card (Split Layout) -->
    <div class="card">
        <div class="card-title">Record Details</div>
        @php
            $hiddenFields = ['id', 'user_id', 'created_at', 'updated_at', 'deleted_at', 'documents', 'signature'];
            $attributes = collect($report->getAttributes())->except($hiddenFields);
            $halfCount = ceil($attributes->count() / 2);
            $leftSide = $attributes->take($halfCount);
            $rightSide = $attributes->slice($halfCount);
        @endphp
        <table class="split-layout">
            <tr>
                <td style="width: 48%; padding-right: 2%;">
                    <table class="details-table">
                        <tbody>
                            @foreach($leftSide as $key => $value)
                                @php $isJson = is_array($value) || is_object($value); @endphp
                                <tr>
                                    <td class="field-name">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                    <td class="field-value">
                                        @if($isJson)
                                            <pre style="margin: 0; font-family: monospace; font-size: 7px;">{{ json_encode($value) }}</pre>
                                        @elseif(in_array(strtolower((string)$value), ['1', '0', 'true', 'false']) && !is_numeric($value))
                                            <span style="font-weight: bold; color: {{ $value ? '#16a34a' : '#dc2626' }}">{{ $value ? 'Yes' : 'No' }}</span>
                                        @else
                                            {{ $value ?? 'N/A' }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
                <td style="width: 48%; padding-left: 2%; border-left: 1px solid #f1f5f9;">
                    <table class="details-table">
                        <tbody>
                            @foreach($rightSide as $key => $value)
                                @php $isJson = is_array($value) || is_object($value); @endphp
                                <tr>
                                    <td class="field-name">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                    <td class="field-value">
                                        @if($isJson)
                                            <pre style="margin: 0; font-family: monospace; font-size: 7px;">{{ json_encode($value) }}</pre>
                                        @elseif(in_array(strtolower((string)$value), ['1', '0', 'true', 'false']) && !is_numeric($value))
                                            <span style="font-weight: bold; color: {{ $value ? '#16a34a' : '#dc2626' }}">{{ $value ? 'Yes' : 'No' }}</span>
                                        @else
                                            {{ $value ?? 'N/A' }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Patrol Logs Card -->
    @if(method_exists($report, 'patrolLogs') && $report->patrolLogs && $report->patrolLogs->count() > 0)
        <div class="card">
            <div class="card-title">Patrol Logs</div>
            <table class="patrol-table">
                <thead>
                    <tr>
                        <th>Round</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Findings / Observations</th>
                        <th>Initials</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->patrolLogs as $log)
                        <tr>
                            <td style="font-weight: bold;">{{ $log->round ?? 'N/A' }}</td>
                            <td>{{ $log->date }}</td>
                            <td>{{ $log->start_time }} - {{ $log->end_time }}</td>
                            <td>{{ $log->area_patrolled_findings ?? 'N/A' }}</td>
                            <td>{{ $log->initials ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Patrol Entries Card -->
    @if(method_exists($report, 'patrolEntries') && $report->patrolEntries && $report->patrolEntries->count() > 0)
        <div class="card">
            <div class="card-title">Patrol Entries</div>
            <table class="patrol-table">
                <thead>
                    <tr>
                        <th>Time Range</th>
                        <th>Summary of Observations</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->patrolEntries as $entry)
                        <tr>
                            <td style="font-weight: bold; width: 22%;">{{ $entry->time_range ?? 'N/A' }}</td>
                            <td>{{ $entry->summary ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Signature Card / Section -->
    @if(isset($report->signature) && $report->signature)
        <div class="signature-section">
            <div class="signature-box">
                @if(strpos($report->signature, 'data:image') === 0)
                    <img src="{{ $report->signature }}" class="signature-img" alt="Digital Signature">
                @else
                    <div class="signature-name">{{ $report->signature }}</div>
                @endif
                <div style="font-size: 7px; color: #64748b; text-transform: uppercase; font-weight: bold; letter-spacing: 0.05em;">Authorized Signature</div>
            </div>
        </div>
    @endif

    <div class="footer">
        Elite Guard Inc. &bull; Confidential Incident & Activity Report &bull; Generated Automatically
    </div>

</body>
</html>
