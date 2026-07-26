<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Site Tour Performance Report</title>
    <style>
        @page { margin: 25px 30px 38px; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: DejaVu Sans, sans-serif; color: #182236; font-size: 9px; }
        .header { background: #201b59; color: #fff; padding: 18px 22px; border-radius: 8px; }
        .header-table, .summary-table, .info-table { width: 100%; border-collapse: collapse; }
        .brand { font-size: 20px; font-weight: bold; letter-spacing: .5px; }
        .report-title { font-size: 19px; font-weight: bold; text-align: right; }
        .report-subtitle { color: #c9c5ff; text-align: right; margin-top: 4px; }
        .accent { height: 4px; width: 90px; background: #7c3aed; margin-top: 8px; }
        .summary { margin: 14px 0; }
        .summary td { width: 16.66%; padding: 10px 8px; text-align: center; background: #f5f3ff; border-right: 3px solid #fff; }
        .metric { display: block; font-size: 18px; font-weight: bold; color: #312e81; }
        .metric-label { display: block; margin-top: 3px; color: #697386; font-size: 7px; text-transform: uppercase; }
        .section-title { margin: 15px 0 7px; padding: 7px 10px; color: #fff; background: #312e81; font-size: 11px; font-weight: bold; border-radius: 4px; }
        .info-table td { width: 25%; padding: 7px 9px; border: 1px solid #e5e7eb; vertical-align: top; }
        .label { display: block; color: #7b8494; font-size: 7px; text-transform: uppercase; margin-bottom: 3px; }
        .value { font-weight: bold; color: #172033; }
        .items { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .items th { background: #ede9fe; color: #312e81; padding: 7px 5px; font-size: 7px; text-transform: uppercase; border: 1px solid #ddd6fe; }
        .items td { padding: 7px 5px; border: 1px solid #e5e7eb; vertical-align: middle; }
        .items tr:nth-child(even) td { background: #fafafa; }
        .center { text-align: center; }
        .status { color: #fff; font-weight: bold; padding: 3px 6px; border-radius: 9px; font-size: 7px; }
        .completed { background: #059669; }
        .partial { background: #d97706; }
        .missed { background: #dc2626; }
        .progress-wrap { width: 58px; height: 7px; background: #e5e7eb; border-radius: 4px; overflow: hidden; display: inline-block; }
        .progress { height: 7px; background: #7c3aed; }
        .small { font-size: 7px; color: #6b7280; }
        .scan-card { margin-bottom: 7px; padding: 7px 9px; border: 1px solid #e5e7eb; border-left: 4px solid #7c3aed; page-break-inside: avoid; }
        .scan-title { font-weight: bold; color: #312e81; margin-bottom: 4px; }
        .scan-list { width: 100%; border-collapse: collapse; }
        .scan-list td { padding: 3px 5px; border-top: 1px solid #f0f1f3; }
        .empty { color: #9ca3af; font-style: italic; }
        .footer { position: fixed; bottom: -25px; left: 0; right: 0; color: #7b8494; font-size: 7px; border-top: 1px solid #e5e7eb; padding-top: 7px; }
        .footer-right { float: right; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="brand">ELITE SECURITY</div>
                    <div class="accent"></div>
                </td>
                <td>
                    <div class="report-title">SITE TOUR PERFORMANCE REPORT</div>
                    <div class="report-subtitle">Report #STR-{{ str_pad($tour->id, 6, '0', STR_PAD_LEFT) }} | Generated {{ now()->format('d M Y, h:i A') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="summary-table summary">
        <tr>
            <td><span class="metric">{{ $summary['total_items'] }}</span><span class="metric-label">Patrol Items</span></td>
            <td><span class="metric">{{ $summary['required_tags'] }}</span><span class="metric-label">Tags Per Item</span></td>
            <td><span class="metric">{{ $summary['completed_items'] }}</span><span class="metric-label">Completed Items</span></td>
            <td><span class="metric">{{ $summary['partial_items'] }}</span><span class="metric-label">Partial Items</span></td>
            <td><span class="metric">{{ $summary['missed_items'] }}</span><span class="metric-label">Missed Items</span></td>
            <td><span class="metric">{{ $summary['overall_completion'] }}%</span><span class="metric-label">Overall Completion</span></td>
        </tr>
    </table>

    <div class="section-title">Site, Shift and Tour Details</div>
    <table class="info-table">
        <tr>
            <td><span class="label">Company</span><span class="value">{{ $tour->site?->company?->name ?? 'N/A' }}</span></td>
            <td><span class="label">Site</span><span class="value">{{ $tour->site?->name ?? 'N/A' }}</span></td>
            <td><span class="label">Site Address</span><span class="value">{{ $tour->site?->address ?: trim(($tour->site?->city ?? '') . ', ' . ($tour->site?->country ?? ''), ', ') ?: 'N/A' }}</span></td>
            <td><span class="label">Assigned Employee</span><span class="value">{{ $tour->user?->name ?? $tour->shift?->schedule?->user?->name ?? 'N/A' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Shift Name</span><span class="value">{{ $tour->shift?->shift_name ?? $tour->name }}</span></td>
            <td><span class="label">Shift Date</span><span class="value">{{ $tour->shift?->date ? \Carbon\Carbon::parse($tour->shift->date)->format('l, d M Y') : ($tour->items->first()?->date?->format('l, d M Y') ?? 'N/A') }}</span></td>
            <td><span class="label">Shift Time</span><span class="value">{{ $tour->shift?->start_time ? \Carbon\Carbon::parse($tour->shift->start_time)->format('h:i A') : 'N/A' }} - {{ $tour->shift?->end_time ? \Carbon\Carbon::parse($tour->shift->end_time)->format('h:i A') : 'N/A' }}</span></td>
            <td><span class="label">Tour Name</span><span class="value">{{ $tour->name }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Interval</span><span class="value">{{ $tour->interval ?? 0 }} minutes</span></td>
            <td><span class="label">Open Time</span><span class="value">{{ $tour->open_time ?? 0 }} minutes</span></td>
            <td><span class="label">Grace Time</span><span class="value">{{ $tour->grace_time ?? 0 }} minutes</span></td>
            <td><span class="label">Tag Type / Scheduled Day</span><span class="value">{{ strtoupper($tour->tag_type) }} / {{ implode(', ', $tour->scheduled_days ?? []) }}</span></td>
        </tr>
    </table>

    <div class="section-title">Interval Performance</div>
    <table class="items">
        <thead>
            <tr>
                <th style="width:4%;">#</th>
                <th style="width:11%;">Date</th>
                <th style="width:10%;">Start</th>
                <th style="width:10%;">End</th>
                <th style="width:11%;">Required</th>
                <th style="width:11%;">Scanned</th>
                <th style="width:11%;">Missing</th>
                <th style="width:18%;">Completion</th>
                <th style="width:14%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportItems as $index => $row)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $row['item']->date?->format('d M Y') ?? 'N/A' }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($row['item']->start_time)->format('h:i A') }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($row['item']->end_time)->format('h:i A') }}</td>
                    <td class="center">{{ $row['required_count'] }}</td>
                    <td class="center">{{ $row['scanned_count'] }}</td>
                    <td class="center">{{ $row['missing_count'] }}</td>
                    <td class="center">
                        <span class="progress-wrap"><span class="progress" style="width:{{ $row['completion'] }}%;"></span></span>
                        <span class="small">{{ $row['completion'] }}%</span>
                    </td>
                    <td class="center"><span class="status {{ strtolower($row['status']) }}">{{ $row['status'] }}</span></td>
                </tr>
            @empty
                <tr><td colspan="9" class="center empty">No interval items were generated for this tour.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>
    <div class="section-title">Tag Scan Evidence by Patrol Item</div>
    @forelse($reportItems as $index => $row)
        <div class="scan-card">
            <div class="scan-title">
                Item {{ $index + 1 }}:
                {{ $row['item']->date?->format('d M Y') ?? 'N/A' }},
                {{ \Carbon\Carbon::parse($row['item']->start_time)->format('h:i A') }} -
                {{ \Carbon\Carbon::parse($row['item']->end_time)->format('h:i A') }}
                | {{ $row['scanned_count'] }}/{{ $row['required_count'] }} scanned
            </div>
            @if($row['scans']->isNotEmpty())
                <table class="scan-list">
                    @foreach($row['scans'] as $scan)
                        <tr>
                            <td style="width:30%;"><strong>{{ $scan->nfcTag?->name ?? 'Unknown Tag' }}</strong></td>
                            <td style="width:25%;">UID: {{ $scan->nfcTag?->uid ?? 'N/A' }}</td>
                            <td style="width:25%;">Scanned: {{ $scan->time ? \Carbon\Carbon::parse($scan->time)->format('h:i:s A') : 'N/A' }}</td>
                            <td style="width:20%;">By: {{ $scan->user?->name ?? $tour->user?->name ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </table>
            @else
                <span class="empty">No NFC tags were scanned during this patrol item.</span>
            @endif
            @if($row['missing_tags']->isNotEmpty())
                <div class="small" style="margin-top:5px;">
                    Missing: {{ $row['missing_tags']->pluck('name')->implode(', ') }}
                </div>
            @endif
        </div>
    @empty
        <div class="empty">No scan evidence is available.</div>
    @endforelse

    <div class="footer">
        Confidential operational report - Elite Security
        <span class="footer-right">Site Tour #{{ $tour->id }}</span>
    </div>
    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_text(735, 570, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 7, array(0.45, 0.49, 0.58));
        }
    </script>
</body>
</html>
