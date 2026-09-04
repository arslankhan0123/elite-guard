<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 14px 18px 24px 18px;
        }
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #334155;
            font-size: 10px;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .card-header-sky {
            background-color: #e0f2fe;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 8px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            padding: 0;
            vertical-align: middle;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #0369a1;
            letter-spacing: 0.02em;
            margin-bottom: 2px;
            line-height: 1.1;
        }
        .company-info {
            font-size: 9.5px;
            color: #334155;
            line-height: 1.35;
        }
        .report-title-cell {
            text-align: right;
        }
        .report-badge {
            font-size: 14px;
            font-weight: bold;
            color: #0369a1;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .report-id-text {
            color: #0284c7;
            font-size: 10px;
            font-weight: bold;
            margin-top: 2px;
        }
        .status-pill {
            display: inline-block;
            padding: 2px 8px;
            font-size: 8.5px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .status-paid { background-color: #d1fae5; color: #047857; }
        .status-draft { background-color: #f1f5f9; color: #475569; }
        .status-overdue { background-color: #fee2e2; color: #b91c1c; }
        .status-active { background-color: #e0f2fe; color: #0369a1; }

        .card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 8px;
        }
        .card-title {
            font-size: 9.5px;
            font-weight: bold;
            color: #0369a1;
            background-color: #f0f9ff;
            border: 1px solid #e0f2fe;
            padding: 4px 8px;
            margin-bottom: 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 6px 8px;
            border: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .meta-label {
            font-weight: bold;
            color: #64748b;
            font-size: 8px;
            text-transform: uppercase;
            background-color: #f8fafc;
            width: 15%;
        }
        .meta-value {
            color: #0f172a;
            font-size: 9.5px;
            width: 35%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .items-table th {
            background-color: #0369a1;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5px;
            padding: 6px 8px;
            border: 1px solid #0284c7;
            text-align: left;
        }
        .items-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 9.5px;
            color: #334155;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .notes-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 10px;
            font-size: 9px;
            color: #475569;
        }

        .footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <!-- Sky Blue Header Card -->
    <div class="card-header-sky">
        <table class="header-table">
            <tr>
                <td style="width: 65px;">
                    <div style="display: inline-block; width: 56px; height: 56px; background-color: #ffffff; border-radius: 50%; text-align: center; vertical-align: middle; border: 1px solid #bae6fd;">
                        <img src="{{ public_path('logo.png') }}" alt="Logo" style="width: 45px; height: 45px; margin-top: 5.5px; object-fit: contain; vertical-align: middle;">
                    </div>
                </td>
                <td>
                    <div class="company-title">ELITE GUARD INC.</div>
                    <div class="company-info">
                        3961 52 Ave NE #2104, Calgary, AB T3J 0J7<br>
                        Phone: +1 (403) 830-7772 &bull; Email: Info@eliteguardinc.ca
                    </div>
                </td>
                <td class="report-title-cell">
                    <div class="report-badge">{{ strtoupper($invoice->title) }}</div>
                    <div class="report-id-text">INVOICE #{{ $invoice->invoice_number }}</div>
                    @php $st = strtolower($invoice->calculated_status); @endphp
                    <span class="status-pill {{ $st === 'paid' ? 'status-paid' : ($st === 'draft' ? 'status-draft' : ($st === 'overdue' ? 'status-overdue' : 'status-active')) }}">
                        {{ ucfirst($st) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Billed Information Card -->
    <div class="card">
        <div class="card-title">Billed Information</div>
        <table class="meta-table">
            <tr>
                <td class="meta-label">Billed By</td>
                <td class="meta-value">
                    <strong>Elite Guard Inc.</strong><br>
                    2104-3961 52 Ave NE, Calgary, AB T3J 0K7, Canada<br>
                    Phone: +14039090602
                </td>
                <td class="meta-label">Billed To</td>
                <td class="meta-value">
                    <strong>{{ $invoice->company ? $invoice->company->name : 'N/A' }}</strong><br>
                    @if($invoice->site)
                        Site: {{ $invoice->site->name }}<br>
                        {{ $invoice->site->address }}<br>
                    @elseif($invoice->company)
                        {{ $invoice->company->address }}<br>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Invoice Details Card -->
    <div class="card">
        <div class="card-title">Invoice Details</div>
        <table class="meta-table">
            <tr>
                <td class="meta-label">Invoice Date</td>
                <td class="meta-value"><strong>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</strong></td>
                <td class="meta-label">Payment Due</td>
                <td class="meta-value"><strong>{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</strong></td>
            </tr>
            <tr>
                <td class="meta-label">PO/SO Number</td>
                <td class="meta-value">{{ $invoice->po_so_number ?: 'N/A' }}</td>
                <td class="meta-label">Amount Due</td>
                <td class="meta-value"><strong style="color: #dc2626;">$ {{ number_format($invoice->amount_due, 2) }}</strong></td>
            </tr>
            @if($invoice->summary)
            <tr>
                <td class="meta-label">Summary</td>
                <td class="meta-value" colspan="3">{{ $invoice->summary }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Products & Services Card -->
    <div class="card">
        <div class="card-title">Products & Services</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 44%;">Product / Service</th>
                    <th style="width: 14%;" class="text-center">Qty</th>
                    <th style="width: 14%;" class="text-right">Rate</th>
                    <th style="width: 14%;" class="text-right">Tax</th>
                    <th style="width: 14%;" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td><strong>{{ $item->product_service }}</strong></td>
                    <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-right">$ {{ number_format($item->rate, 2) }}</td>
                    <td class="text-right">$ {{ number_format($item->tax, 2) }}</td>
                    <td class="text-right"><strong>$ {{ number_format($item->amount, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Summary & Totals Section -->
    <div class="card">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 55%; vertical-align: top; padding-right: 15px; border: 0;">
                    @if($invoice->notes)
                        <div class="notes-box">
                            <strong style="color: #0369a1; display: block; margin-bottom: 3px;">Notes:</strong>
                            {{ $invoice->notes }}
                        </div>
                    @else
                        &nbsp;
                    @endif
                </td>
                <td style="width: 45%; vertical-align: top; border: 0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="color: #64748b; padding: 4px 0; font-size: 10px; border: 0;">Subtotal:</td>
                            <td style="text-align: right; padding: 4px 0; font-size: 10px; font-weight: bold; color: #0f172a; border: 0;">$ {{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; padding: 4px 0; font-size: 10px; border-bottom: 2px solid #0369a1;">Tax:</td>
                            <td style="text-align: right; padding: 4px 0; font-size: 10px; font-weight: bold; color: #0f172a; border-bottom: 2px solid #0369a1;">$ {{ number_format($invoice->tax_total, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="color: #0369a1; padding: 6px 0 0 0; font-size: 13px; font-weight: bold; border: 0;">Total:</td>
                            <td style="text-align: right; color: #0369a1; padding: 6px 0 0 0; font-size: 13px; font-weight: bold; border: 0;">$ {{ number_format($invoice->total, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Elite Guard Inc. &bull; Confidential Operational Billing Invoice &bull; Generated Automatically
    </div>

</body>
</html>
