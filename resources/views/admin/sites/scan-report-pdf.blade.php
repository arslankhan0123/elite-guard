<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Site Checkpoint Performance Report</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12px 16px 24px
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #182236;
            font-size: 9px
        }

        .header {
            background: #201b59;
            color: #fff;
            padding: 7px 14px;
            border-radius: 5px
        }

        .header-table,
        .summary-table,
        .info-table {
            width: 100%;
            border-collapse: collapse
        }

        .brand-cell {
            white-space: nowrap
        }

        .brand-logo {
            width: 30px;
            height: 30px;
            object-fit: contain;
            vertical-align: middle;
            margin-right: 7px
        }

        .brand-block {
            display: inline-block;
            vertical-align: middle
        }

        .brand {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: .5px
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            text-align: right
        }

        .report-subtitle {
            color: #c9c5ff;
            text-align: right;
            margin-top: 2px;
            font-size: 8px
        }

        .accent {
            height: 2px;
            width: 65px;
            background: #7c3aed;
            margin-top: 4px
        }

        .summary {
            margin: 6px 0
        }

        .summary td {
            width: 16.66%;
            padding: 5px;
            text-align: center;
            background: #f5f3ff;
            border-right: 2px solid #fff
        }

        .metric {
            display: block;
            font-size: 14px;
            font-weight: bold;
            color: #312e81
        }

        .metric-label {
            display: block;
            margin-top: 1px;
            color: #697386;
            font-size: 8px;
            text-transform: uppercase
        }

        .section-title {
            margin: 7px 0 4px;
            padding: 4px 7px;
            color: #fff;
            background: #312e81;
            font-size: 10px;
            font-weight: bold;
            border-radius: 3px
        }

        .info-table td {
            width: 25%;
            padding: 4px 6px;
            border: 1px solid #e5e7eb;
            vertical-align: top
        }

        .label {
            display: block;
            color: #7b8494;
            font-size: 7.5px;
            text-transform: uppercase;
            margin-bottom: 1px
        }

        .value {
            font-weight: bold;
            color: #172033
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed
        }

        .items th {
            background: #ede9fe;
            color: #312e81;
            padding: 4px 3px;
            font-size: 8px;
            text-transform: uppercase;
            border: 1px solid #ddd6fe
        }

        .items td {
            padding: 4px 3px;
            border: 1px solid #e5e7eb;
            vertical-align: middle
        }

        .items tr:nth-child(even) td {
            background: #fafafa
        }

        .items .evidence-row td {
            background: #fafaff;
            padding: 2px 5px 3px;
            border-top: 0
        }

        .evidence-box {
            border-left: 2px solid #7c3aed;
            padding: 2px 5px;
            line-height: 1.25
        }

        .evidence-label {
            color: #312e81;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            margin-right: 5px
        }

        .evidence-line,
        .reason-line {
            color: #4b5563;
            font-size: 8px
        }

        .reason-line {
            margin-bottom: 3px
        }

        .evidence-image {
            display: block;
            width: 100%;
            height: 82px;
            max-width: 100%;
            object-fit: cover;
            border-radius: 3px
        }

        .scan-grid { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 2px; }
        .items .scan-grid td { border: 0; background: transparent; padding: 2px; vertical-align: top; }
        .items .scan-grid .scan-grid-cell { width: 33.333%; max-width: 33.333%; }
        .scan-evidence-card { width: 100%; border-collapse: collapse; table-layout: fixed; border: 1px solid #ddd6fe; background: #fff; page-break-inside: avoid; }
        .items .scan-evidence-card .scan-photo-cell { width: 55%; padding: 2px; background: #fff; }
        .items .scan-evidence-card .scan-data-cell { width: 45%; padding: 6px; background: #fff; color: #374151; font-size: 9px; line-height: 1.55; word-wrap: break-word; }
        .scan-tag { color: #312e81; font-size: 10px; font-weight: bold; margin-bottom: 4px; }
        .scan-data-cell strong { color: #172033; font-weight: bold; }
        .no-photo { height: 82px; line-height: 82px; text-align: center; color: #6b7280; background: #f3f4f6; border-radius: 3px; font-size: 9px; }
        }

        .missing-line {
            color: #b91c1c;
            margin: 3px 0 0 2px
        }

        .center {
            text-align: center
        }

        .status {
            color: #fff;
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 7px;
            font-size: 8px
        }

        .status.completed {
            background: #059669
        }

        .status.partial {
            background: #312e81
        }

        .status.missed {
            background: #dc2626
        }

        .progress-wrap {
            width: 48px;
            height: 5px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
            display: inline-block
        }

        .progress {
            display: block;
            height: 5px
        }

        .completion-value {
            font-weight: bold;
            margin-left: 3px
        }

        .completion-value.completed {
            color: #059669
        }

        .completion-value.partial {
            color: #312e81
        }

        .completion-value.missed {
            color: #dc2626
        }

        .empty {
            color: #9ca3af;
            font-style: italic
        }

        .footer {
            position: fixed;
            bottom: -17px;
            left: 0;
            right: 0;
            color: #7b8494;
            font-size: 8px;
            border-top: 1px solid #e5e7eb;
            padding-top: 4px
        }

        .footer-right {
            float: right
        }
    </style>
</head>

<body>
    @php
        $resolvePdfImage = function ($image) {
            $path = parse_url($image, PHP_URL_PATH) ?: $image;

            if (\Illuminate\Support\Str::startsWith($path, ['/storage/', 'storage/'])) {
                return public_path(ltrim($path, '/'));
            }

            return $image;
        };
    @endphp
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="brand-cell"><img class="brand-logo" src="{{ public_path('logo.png') }}" alt="Elite Guard">
                    <div class="brand-block">
                        <div class="brand">ELITE SECURITY</div>
                        <div class="accent"></div>
                    </div>
                </td>
                <td>
                    <div class="report-title">SITE CHECKPOINT PERFORMANCE REPORT</div>
                    <div class="report-subtitle">Report #SSR-{{ str_pad($site->id,6,'0',STR_PAD_LEFT) }} | Generated {{ now()->format('d M Y, h:i A') }}</div>
                </td>
            </tr>
        </table>
    </div>
    <table class="summary-table summary">
        <tr>
            <td><span class="metric">{{ $summary['total_items'] }}</span><span class="metric-label">Site Items</span></td>
            <td><span class="metric">{{ $summary['required_tags'] }}</span><span class="metric-label">Tags Per Item</span></td>
            <td><span class="metric">{{ $summary['completed_items'] }}</span><span class="metric-label">Completed Items</span></td>
            <td><span class="metric">{{ $summary['partial_items'] }}</span><span class="metric-label">Partial Items</span></td>
            <td><span class="metric">{{ $summary['missed_items'] }}</span><span class="metric-label">Missed Items</span></td>
            <td><span class="metric">{{ $summary['overall_completion'] }}%</span><span class="metric-label">Overall Completion</span></td>
        </tr>
    </table>
    <div class="section-title">Site and Report Details</div>
    <table class="info-table">
        <tr>
            <td><span class="label">Company</span><span class="value">{{ $site->company?->name ?? 'N/A' }}</span></td>
            <td><span class="label">Site</span><span class="value">{{ $site->name }}</span></td>
            <td><span class="label">Site Address</span><span class="value">{{ $site->address ?: trim(($site->city ?? '').', '.($site->country ?? ''),', ') ?: 'N/A' }}</span></td>
            <td><span class="label">Selected User</span><span class="value">{{ $selectedUser?->name ?? 'All Users' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Start Date</span><span class="value">{{ \Carbon\Carbon::parse($filters['start_date'])->format('l, d M Y') }}</span></td>
            <td><span class="label">End Date</span><span class="value">{{ \Carbon\Carbon::parse($filters['end_date'])->format('l, d M Y') }}</span></td>
            <td><span class="label">Search Filter</span><span class="value">{{ $filters['search'] ?? 'None' }}</span></td>
            <td><span class="label">Report Status</span><span class="value">Final / Generated</span></td>
        </tr>
    </table>
    <div class="section-title">Site Item Performance</div>
    <table class="items">
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:11%">Date</th>
                <th style="width:10%">Start</th>
                <th style="width:10%">End</th>
                <th style="width:11%">Required</th>
                <th style="width:11%">Scanned</th>
                <th style="width:11%">Missing</th>
                <th style="width:18%">Completion</th>
                <th style="width:14%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportItems as $row)
            @php $progressColor=match($row['status']){'Completed'=>'#059669','Partial'=>'#312e81',default=>'#dc2626'}; @endphp
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td class="center">{{ $row['item']->date?->format('d M Y') ?? 'N/A' }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($row['item']->start_time)->format('h:i A') }}</td>
                <td class="center">{{ $row['item']->end_time ? \Carbon\Carbon::parse($row['item']->end_time)->format('h:i A') : '—' }}</td>
                <td class="center">{{ $row['required_count'] }}</td>
                <td class="center">{{ $row['scanned_count'] }}</td>
                <td class="center">{{ $row['missing_count'] }}</td>
                <td class="center"><span class="progress-wrap"><span class="progress" style="width:{{ max(3,$row['completion']) }}%;background:{{ $progressColor }}"></span></span><span class="completion-value {{ strtolower($row['status']) }}">{{ $row['completion'] }}%</span></td>
                <td class="center"><span class="status {{ strtolower($row['status']) }}">{{ $row['status'] }}</span></td>
            </tr>
            <tr class="evidence-row">
                <td colspan="9">
                    <div class="evidence-box">
                        <div class="reason-line"><span class="evidence-label">Site Item:</span>{{ ucfirst($row['item']->type) }} @if(filled($row['item']->reason)) | {{ $row['item']->reason }} @endif</div>
                        <span class="evidence-label">Evidence:</span>
                        @include('admin.sites.partials.scan-evidence-grid', ['scans' => $row['scans'], 'fallbackUser' => $row['item']->user?->name])
                        <div class="missing-line"><strong>Missing:</strong> {{ $row['missing_tags']->isNotEmpty() ? $row['missing_tags']->pluck('name')->implode(', ') : 'None' }}</div>
                    </div>
                </td>
            </tr>
            @empty<tr>
                <td colspan="9" class="center empty">No site items found for the selected filters.</td>
            </tr>@endforelse
        </tbody>
    </table>
    <div class="footer">Confidential operational report - Elite Security<span class="footer-right">Site #{{ $site->id }} | {{ $site->name }}</span></div>
    <script type="text/php">if(isset($pdf)){$pdf->page_text(735,570,"Page {PAGE_NUM} of {PAGE_COUNT}",null,7,array(.45,.49,.58));}</script>
</body>

</html>
