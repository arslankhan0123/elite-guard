<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chronological System Activity Report</title>
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
        .info-table {
            width: 100%;
            border-collapse: collapse
        }

        .brand-cell {
            white-space: nowrap
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            object-fit: contain;
            vertical-align: middle;
            margin-right: 8px
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
            table-layout: fixed;
            margin-top: 10px;
        }

        .items th {
            background: #ede9fe;
            color: #312e81;
            padding: 5px 3px;
            font-size: 8px;
            text-transform: uppercase;
            border: 1px solid #ddd6fe
        }

        .items td {
            padding: 5px 3px;
            border: 1px solid #e5e7eb;
            vertical-align: middle
        }

        .items tr:nth-child(even) td {
            background: #fafafa
        }

        .center {
            text-align: center
        }

        .badge {
            display: inline-block;
            padding: 2px 4px;
            font-weight: bold;
            border-radius: 3px;
            font-size: 7.5px;
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
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

        .scan-grid { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 2px; }
        .scan-grid-cell { width: 33.333%; max-width: 33.333%; padding: 1px; vertical-align: top; }
        .scan-evidence-card { width: 100%; height: 85px; border-collapse: collapse; table-layout: fixed; border: 1px solid #ddd6fe; background: #fff; page-break-inside: avoid; }
        .scan-photo-cell { width: 55%; height: 85px; padding: 2px; background: #fff; vertical-align: middle; }
        .scan-data-cell { width: 45%; height: 85px; padding: 4px 6px; background: #fff; color: #374151; font-size: 8.5px; line-height: 1.35; word-wrap: break-word; vertical-align: middle; }
        .scan-data-only-cell { width: 100%; }
        .scan-tag { color: #312e81; font-size: 9.5px; font-weight: bold; margin-bottom: 3px; }
        .evidence-image { display: block; width: 100%; height: 81px; max-width: 100%; object-fit: cover; border-radius: 3px; }
        .scan-grid-empty { background: transparent !important; border: 0 !important; }
    </style>
</head>

<body>
    @php
        $pdfImageCache = [];
        $resolvePdfImage = function ($image) use (&$pdfImageCache) {
            if (empty($image)) return null;
            if (array_key_exists($image, $pdfImageCache)) {
                return $pdfImageCache[$image];
            }

            $path = parse_url($image, PHP_URL_PATH) ?: $image;
            $path = '/' . ltrim(str_replace('\\', '/', $path), '/');

            if (\Illuminate\Support\Str::startsWith($path, '/storage/')) {
                $storagePath = preg_replace('#^/storage/#', '', $path);

                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)) {
                    $contents = \Illuminate\Support\Facades\Storage::disk('public')->get($storagePath);
                    $mimeType = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($storagePath) ?: 'image/jpeg';

                    return $pdfImageCache[$image] = 'data:' . $mimeType . ';base64,' . base64_encode($contents);
                }

                return $pdfImageCache[$image] = null;
            }

            $localPath = public_path(ltrim($path, '/'));
            if (is_file($localPath)) {
                $mimeType = mime_content_type($localPath) ?: 'image/jpeg';
                return $pdfImageCache[$image] = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($localPath));
            }

            return $pdfImageCache[$image] = null;
        };
    @endphp

    <div class="header">
        <table class="header-table">
            <tr>
                <td class="brand-cell">
                    <div style="display: inline-block; width: 56px; height: 56px; background-color: #ffffff; border-radius: 50%; text-align: center; vertical-align: middle; margin-right: 12px;">
                        <img src="{{ public_path('logo.png') }}" alt="Elite Guard" style="width: 45px; height: 45px; margin-top: 5.5px; object-fit: contain; vertical-align: middle;">
                    </div>
                    <div class="brand-block">
                        <div class="brand" style="font-size: 18px; font-weight: bold; letter-spacing: 0.5px; color: #ffffff; line-height: 1.1;">ELITE GUARD INC.</div>
                        <div style="font-size: 9.5px; color: #ffffff; margin-top: 3px; line-height: 1.35;">
                            3961 52 Ave NE #2104, Calgary, AB T3J 0J7<br>
                            Phone: +1 (403) 830-7772 &bull; Email: Info@eliteguardinc.ca
                        </div>
                    </div>
                </td>
                <td>
                    <div class="report-title">CHRONOLOGICAL ACTIVITY REPORT</div>
                    <div class="report-subtitle">Generated {{ now()->format('d M Y, h:i A') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Report Scope & Parameters</div>
    <table class="info-table">
        <tr>
            <td style="width: 20%;"><span class="label">Date Range</span><span class="value">
                {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - 
                {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </span></td>
            <td style="width: 20%;"><span class="label">Selected User</span><span class="value">{{ $userObj?->name ?? 'All Guards' }}</span></td>
            <td style="width: 20%;"><span class="label">Selected Sites</span><span class="value">
                @if(isset($sitesObj) && $sitesObj->count() > 0)
                    {{ implode(', ', $sitesObj->pluck('name')->toArray()) }}
                @else
                    All Sites
                @endif
            </span></td>
            <td style="width: 20%;"><span class="label">Weekly Runsheet</span><span class="value">{{ $weeklyRunSheetObj?->name ?? 'All Runsheets' }}</span></td>
            <td style="width: 20%;"><span class="label">Time Range</span><span class="value">
                {{ $startTime ? \Carbon\Carbon::parse($startTime)->format('h:i A') : '00:00 AM' }} - 
                {{ $endTime ? \Carbon\Carbon::parse($endTime)->format('h:i A') : '11:59 PM' }}
            </span></td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 4%">#</th>
                <th style="width: 10%">Date</th>
                <th style="width: 15%">Interval (Time)</th>
                <th style="width: 15%">User</th>
                <th style="width: 15%">Site</th>
                <th style="width: 20%">Tour / Patrol Name</th>
                <th style="width: 7%" class="center">Req.</th>
                <th style="width: 7%" class="center">Scan.</th>
                <th style="width: 7%" class="center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $report)
            @php 
                $statusColor = match($report['status']) {
                    'Completed' => '#059669',
                    'Partial' => '#312e81',
                    default => '#dc2626'
                };
            @endphp
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($report['date'])->format('d M Y') }}</td>
                <td class="center" style="font-weight: bold; color: #201b59;">
                    {{ \Carbon\Carbon::parse($report['start_time'])->format('h:i A') }} - 
                    {{ \Carbon\Carbon::parse($report['end_time'])->format('h:i A') }}
                </td>
                <td>{{ $report['user'] }}</td>
                <td>{{ $report['site'] }}</td>
                <td>
                    <span class="badge" style="margin-right: 4px;">{{ $report['type'] }}</span>
                    <strong>{{ $report['name'] }}</strong>
                </td>
                <td class="center">{{ $report['required_count'] }}</td>
                <td class="center" style="font-weight: bold; color: #059669;">{{ $report['scanned_count'] }}</td>
                <td class="center"><span style="color: #fff; background: {{ $statusColor }}; padding: 2px 5px; border-radius: 4px; font-weight: bold; font-size: 7.5px;">{{ $report['status'] }}</span></td>
            </tr>
            <tr style="background: #fafaff;">
                <td></td>
                <td colspan="8" style="padding: 6px 10px; border-top: 0;">
                    <div style="border-left: 2px solid #7c3aed; padding-left: 8px;">
                        <div style="margin-bottom: 4px;">
                            <strong style="color: #312e81;">Evidence (Scanned Checkpoints):</strong>
                            @if(count($report['scans']) > 0)
                                <table class="scan-grid">
                                    @foreach(collect($report['scans'])->chunk(3) as $scanRow)
                                        <tr>
                                            @foreach($scanRow as $scan)
                                                @php
                                                    $imageSource = !empty($scan['image']) ? $resolvePdfImage($scan['image']) : null;
                                                @endphp
                                                <td class="scan-grid-cell" style="border: 0; background: transparent; padding: 2px;">
                                                    <table class="scan-evidence-card">
                                                        <tr>
                                                            <td class="scan-data-cell{{ !$imageSource ? ' scan-data-only-cell' : '' }}" @if(!$imageSource) colspan="2" @endif>
                                                                <div class="scan-tag">{{ $scan['name'] ?? 'Unknown Tag' }}</div>
                                                                <div><strong>UID:</strong> {{ $scan['uid'] ?? 'N/A' }}</div>
                                                                <div><strong>Time:</strong> {{ $scan['time'] ? \Carbon\Carbon::parse($scan['time'])->format('h:i:s A') : 'N/A' }}</div>
                                                                <div><strong>By:</strong> {{ $scan['user'] ?? 'N/A' }}</div>
                                                            </td>
                                                            @if($imageSource)
                                                                <td class="scan-photo-cell">
                                                                    <img class="evidence-image" src="{{ $imageSource }}" alt="Evidence">
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    </table>
                                                </td>
                                            @endforeach
                                            @for($emptyCell = $scanRow->count(); $emptyCell < 3; $emptyCell++)
                                                <td class="scan-grid-cell scan-grid-empty" style="border: 0; background: transparent; padding: 2px;"></td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                </table>
                            @else
                                <span style="color: #6b7280; font-style: italic;">No NFC tags scanned</span>
                            @endif
                        </div>
                        <div>
                            <strong style="color: #dc2626;">Missing Checkpoints:</strong>
                            @if(count($report['missing_tags']) > 0)
                                <span style="color: #b91c1c; font-weight: bold;">
                                    {{ implode(', ', $report['missing_tags']) }}
                                </span>
                            @else
                                <span style="color: #059669; font-weight: bold;">None</span>
                            @endif
                        </div>
                        @if(!empty($report['images']) && count($report['images']) > 0)
                        <div style="margin-top: 6px; padding-top: 4px; border-top: 1px solid #e5e7eb;">
                            <strong style="color: #4b5563;">Tour Images:</strong>
                            <div style="margin-top: 4px;">
                                @foreach($report['images'] as $imgUrl)
                                    @php
                                        $pdfImg = $resolvePdfImage($imgUrl);
                                    @endphp
                                    @if($pdfImg)
                                        <img src="{{ $pdfImg }}" alt="Tour Image" style="width: 75px; height: 75px; object-fit: cover; border-radius: 3px; border: 1px solid #ddd6fe; margin-right: 6px; margin-bottom: 4px; display: inline-block; vertical-align: top;">
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="center" style="padding: 20px; color: #6b7280;">No activity scans found for this date range.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Confidential operational report - Elite Guard Inc.<span class="footer-right">Generated on {{ now()->format('Y-m-d') }}</span></div>
    <script type="text/php">if(isset($pdf)){$pdf->page_text(735,570,"Page {PAGE_NUM} of {PAGE_COUNT}",null,7,array(.45,.49,.58));}</script>
</body>

</html>
