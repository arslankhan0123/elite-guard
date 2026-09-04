<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #334155;
            font-size: 13px;
            line-height: 1.4;
            margin: 0;
            padding: 20px 10px;
            background-color: #f8fafc;
        }
        .email-container {
            max-width: 750px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }
        
        /* Sky Header Box */
        .card-header-sky {
            background-color: #e0f2fe;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 16px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-circle {
            width: 56px;
            height: 56px;
            background-color: #ffffff;
            border-radius: 50%;
            text-align: center;
            border: 1px solid #bae6fd;
            display: inline-block;
        }
        .company-title {
            font-size: 20px;
            font-weight: 800;
            color: #0369a1;
            letter-spacing: 0.02em;
            margin-bottom: 2px;
            line-height: 1.2;
        }
        .company-info {
            font-size: 11px;
            color: #334155;
            line-height: 1.4;
        }
        .report-badge {
            font-size: 16px;
            font-weight: 800;
            color: #0369a1;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-align: right;
        }
        .report-id-text {
            color: #0284c7;
            font-size: 12px;
            font-weight: 700;
            margin-top: 2px;
            text-align: right;
        }
        .status-pill {
            display: inline-block;
            padding: 3px 10px;
            font-size: 10px;
            font-weight: 800;
            border-radius: 4px;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .status-paid { background-color: #d1fae5; color: #047857; }
        .status-draft { background-color: #f1f5f9; color: #475569; }
        .status-overdue { background-color: #fee2e2; color: #dc2626; }
        .status-active { background-color: #e0f2fe; color: #0369a1; }

        /* Card container */
        .card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 16px;
        }
        .card-title {
            font-size: 11px;
            font-weight: 800;
            color: #0369a1;
            background-color: #f0f9ff;
            border: 1px solid #e0f2fe;
            padding: 6px 12px;
            margin-bottom: 12px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 8px 10px;
            border: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .meta-label {
            font-weight: 800;
            color: #64748b;
            font-size: 10px;
            text-transform: uppercase;
            background-color: #f8fafc;
            width: 18%;
        }
        .meta-value {
            color: #0f172a;
            font-size: 12px;
            width: 32%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .items-table th {
            background-color: #0284c7;
            color: #ffffff;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 10.5px;
            padding: 8px 10px;
            border: 1px solid #0284c7;
            text-align: left;
        }
        .items-table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            font-size: 12px;
            color: #334155;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .notes-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 12px;
            color: #475569;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
            margin-top: 16px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Sky Blue Header Card -->
        <div class="card-header-sky">
            <table class="header-table">
                <tr>
                    <td style="width: 65px;">
                        <div class="logo-circle">
                            <img src="{{ url('logo.png') }}" alt="Elite Guard Logo" style="width: 44px; height: 44px; margin-top: 6px; object-fit: contain;">
                        </div>
                    </td>
                    <td>
                        <div class="company-title">ELITE GUARD INC.</div>
                        <div class="company-info">
                            3961 52 Ave NE #2104, Calgary, AB T3J 0J7<br>
                            Phone: +1 (403) 830-7772 &bull; Email: Info@eliteguardinc.ca
                        </div>
                    </td>
                    <td style="text-align: right;">
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
                    <td class="meta-value"><strong style="color: #dc2626; font-size: 13px;">$ {{ number_format($invoice->amount_due, 2) }}</strong></td>
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
                                <strong style="color: #0369a1; display: block; margin-bottom: 4px;">Notes:</strong>
                                {{ $invoice->notes }}
                            </div>
                        @else
                            &nbsp;
                        @endif
                    </td>
                    <td style="width: 45%; vertical-align: top; border: 0;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="color: #64748b; padding: 4px 0; font-size: 13px; border: 0;">Subtotal:</td>
                                <td style="text-align: right; padding: 4px 0; font-size: 13px; font-weight: bold; color: #0f172a; border: 0;">$ {{ number_format($invoice->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="color: #64748b; padding: 4px 0; font-size: 13px; border-bottom: 2px solid #0369a1;">Tax:</td>
                                <td style="text-align: right; padding: 4px 0; font-size: 13px; font-weight: bold; color: #0f172a; border-bottom: 2px solid #0369a1;">$ {{ number_format($invoice->tax_total, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="color: #0369a1; padding: 8px 0 0 0; font-size: 16px; font-weight: bold; border: 0;">Total:</td>
                                <td style="text-align: right; color: #0369a1; padding: 8px 0 0 0; font-size: 16px; font-weight: bold; border: 0;">$ {{ number_format($invoice->total, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Elite Guard Inc. &bull; 3961 52 Ave NE #2104, Calgary, AB T3J 0J7 &bull; Phone: +1 (403) 830-7772
        </div>
    </div>
</body>
</html>
