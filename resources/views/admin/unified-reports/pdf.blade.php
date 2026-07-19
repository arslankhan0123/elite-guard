<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 15px 20px;
        }
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .header-bar {
            border-bottom: 2px solid #1e1b4b;
            padding-bottom: 10px;
            margin-bottom: 12px;
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
            max-height: 45px;
            margin-right: 10px;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e1b4b;
            letter-spacing: 0.02em;
        }
        .company-subtitle {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .report-title-cell {
            text-align: right;
        }
        .report-badge {
            font-size: 12px;
            font-weight: bold;
            color: #1e1b4b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .report-id-text {
            color: #4f46e5;
            font-size: 10px;
            font-weight: bold;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #1e1b4b;
            background-color: #f1f5f9;
            padding: 4px 8px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-left: 3px solid #4f46e5;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .meta-table td {
            padding: 4px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .meta-label {
            font-weight: bold;
            color: #64748b;
            font-size: 9px;
            text-transform: uppercase;
            width: 15%;
            background-color: #f8fafc;
        }
        .meta-value {
            color: #0f172a;
            width: 35%;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .details-table td {
            padding: 5px 8px;
            border: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .field-name {
            font-weight: bold;
            color: #475569;
            font-size: 9.5px;
            text-transform: uppercase;
            background-color: #f8fafc;
            width: 20%;
            border-right: 1px solid #e2e8f0 !important;
        }
        .field-value {
            color: #0f172a;
            width: 30%;
        }
        .patrol-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .patrol-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5px;
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }
        .patrol-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            color: #334155;
        }
        .signature-section {
            margin-top: 15px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 200px;
            text-align: center;
            border-top: 1px dashed #cbd5e1;
            padding-top: 5px;
            margin-top: 10px;
        }
        .signature-img {
            max-height: 35px;
            margin-bottom: 2px;
        }
        .signature-name {
            font-family: 'Dancing Script', cursive, Georgia, serif;
            font-size: 14px;
            color: #4f46e5;
            font-weight: bold;
        }
        .footer {
            clear: both;
            text-align: center;
            font-size: 8.5px;
            color: #94a3b8;
            margin-top: 15px;
            padding-top: 5px;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

    <!-- Header bar -->
    <div class="header-bar">
        <table class="header-table">
            <tr>
                <td style="width: 55px;">
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

    <!-- Metadata Section -->
    <div class="section-title">Submission Info</div>
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

    <!-- Details Section -->
    <div class="section-title">Record Details</div>
    <table class="details-table">
        <tbody>
            @php
                $hiddenFields = ['id', 'user_id', 'created_at', 'updated_at', 'deleted_at', 'documents', 'signature'];
                $attributes = collect($report->getAttributes())->except($hiddenFields);
                $chunks = $attributes->chunk(2);
            @endphp
            @foreach($chunks as $chunk)
                <tr>
                    @foreach($chunk as $key => $value)
                        @php
                            $isJson = is_array($value) || is_object($value);
                        @endphp
                        <td class="field-name">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                        <td class="field-value">
                            @if($isJson)
                                <pre style="margin: 0; font-family: monospace; font-size: 8px;">{{ json_encode($value) }}</pre>
                            @elseif(in_array(strtolower((string)$value), ['1', '0', 'true', 'false']) && !is_numeric($value))
                                <span style="font-weight: bold; color: {{ $value ? '#16a34a' : '#dc2626' }}">{{ $value ? 'Yes' : 'No' }}</span>
                            @else
                                {{ $value ?? 'N/A' }}
                            @endif
                        </td>
                    @endforeach
                    @if($chunk->count() < 2)
                        <td class="field-name" style="background-color: transparent;"></td>
                        <td class="field-value" style="background-color: transparent;"></td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Patrol Logs / Entries (if any) -->
    @if(method_exists($report, 'patrolLogs') && $report->patrolLogs && $report->patrolLogs->count() > 0)
        <div class="section-title">Patrol Logs</div>
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
    @endif

    @if(method_exists($report, 'patrolEntries') && $report->patrolEntries && $report->patrolEntries->count() > 0)
        <div class="section-title">Patrol Entries</div>
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
                        <td style="font-weight: bold; width: 25%;">{{ $entry->time_range ?? 'N/A' }}</td>
                        <td>{{ $entry->summary ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Signature Section -->
    @if(isset($report->signature) && $report->signature)
        <div class="signature-section">
            <div class="signature-box">
                @if(strpos($report->signature, 'data:image') === 0)
                    <img src="{{ $report->signature }}" class="signature-img" alt="Digital Signature">
                @else
                    <div class="signature-name">{{ $report->signature }}</div>
                @endif
                <div style="font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: bold; letter-spacing: 0.05em;">Authorized Signature</div>
            </div>
        </div>
    @endif

    <div class="footer">
        Elite Guard Inc. &bull; Confidential Incident & Activity Report &bull; Generated Automatically
    </div>

</body>
</html>
