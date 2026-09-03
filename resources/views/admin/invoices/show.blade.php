@extends('dashboardLayouts.main')
@section('title', 'Invoice #' . $invoice->invoice_number)

@section('breadcrumbTitle', 'Invoice Details')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Invoices</a></li>
<li class="breadcrumb-item active">Invoice #{{ $invoice->invoice_number }}</li>
@endsection

@section('content')
<style>
    .card-header-sky {
        background-color: #e0f2fe;
        border: 1px solid #bae6fd;
        border-radius: 10px;
        padding: 20px 24px;
        margin-bottom: 20px;
    }
    .company-title-sky {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0369a1;
        letter-spacing: 0.02em;
        line-height: 1.2;
    }
    .company-info-sky {
        font-size: 0.875rem;
        color: #334155;
        line-height: 1.5;
    }
    .section-card-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0369a1;
        background-color: #f0f9ff;
        border: 1px solid #e0f2fe;
        padding: 8px 14px;
        margin-bottom: 16px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .table-sky-header th {
        background-color: #0369a1 !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        border-color: #0284c7 !important;
        padding: 10px 14px;
    }
    .meta-box-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        background-color: #f8fafc;
        padding: 8px 12px;
        width: 18%;
    }
    .meta-box-val {
        font-size: 0.95rem;
        color: #0f172a;
        padding: 8px 12px;
        width: 32%;
    }
</style>

<div class="row mb-3 no-print">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fa fa-arrow-left me-1"></i> Back to Invoices
        </a>
        <div class="btn-group" role="group" aria-label="Invoice Actions">
            @if(Auth::user()->hasAdminPermission('invoices', 'view'))
            <a class="text-decoration-none text-dark me-2" href="{{ route('invoices.downloadPdf', $invoice->id) }}" data-bs-toggle="tooltip" title="Download PDF">
                <button class="btn btn-sm btn-danger d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; padding: 0; border-radius: 6px;" type="button">
                    <i data-feather="file-text" style="width: 18px; height: 18px;"></i>
                </button>
            </a>
            @endif
            @if(Auth::user()->hasAdminPermission('invoices', 'update'))
            <a class="text-decoration-none me-2 text-dark ml-1" href="{{ route('invoices.edit', $invoice->id) }}" data-bs-toggle="tooltip" title="Edit Invoice">
                <button class="editBtn">
                    <svg height="1em" viewBox="0 0 512 512">
                        <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                    </svg>
                </button>
            </a>
            @endif
            @if(Auth::user()->hasAdminPermission('invoices', 'delete'))
            <a href="{{ route('invoices.delete', $invoice->id) }}" class="bin-button ml-1" data-bs-toggle="tooltip" title="Delete Invoice" onclick="return confirm('Are you sure you want to delete this invoice?')">
                <svg class="bin-top" viewBox="0 0 39 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <line y1="5" x2="39" y2="5" stroke="white" stroke-width="4"></line>
                    <line x1="12" y1="1.5" x2="26.0357" y2="1.5" stroke="white" stroke-width="3"></line>
                </svg>
                <svg class="bin-bottom" viewBox="0 0 33 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="path-1-inside-1_8_19" fill="white">
                        <path d="M0 0H33V35C33 37.2091 31.2091 39 29 39H4C1.79086 39 0 37.2091 0 35V0Z"></path>
                    </mask>
                    <path d="M0 0H33H0ZM37 35C37 39.4183 33.4183 43 29 43H4C-0.418278 43 -4 39.4183 -4 35H4H29H37ZM4 43C-0.418278 43 -4 39.4183 -4 35V0H4V35V43ZM37 0V35C37 39.4183 33.4183 43 29 43V35V0H37Z" fill="white" mask="url(#path-1-inside-1_8_19)"></path>
                    <path d="M12 6L12 29" stroke="white" stroke-width="4"></path>
                    <path d="M21 6V29" stroke="white" stroke-width="4"></path>
                </svg>
            </a>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">

        <!-- Sky Blue Header Banner Card -->
        <div class="card-header-sky shadow-sm">
            <div class="row align-items-center">
                <div class="col-md-7 d-flex align-items-center">
                    <div style="width: 64px; height: 64px; background-color: #ffffff; border-radius: 50%; text-align: center; line-height: 60px; border: 1px solid #bae6fd;" class="me-3 flex-shrink-0">
                        <img src="{{ asset('logo.png') }}" alt="Elite Guard Logo" style="width: 48px; height: 48px; object-fit: contain; vertical-align: middle;">
                    </div>
                    <div>
                        <div class="company-title-sky">ELITE GUARD INC.</div>
                        <div class="company-info-sky">
                            3961 52 Ave NE #2104, Calgary, AB T3J 0J7<br>
                            Phone: +1 (403) 830-7772 &bull; Email: Info@eliteguardinc.ca
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <h4 class="fw-bold text-primary mb-0">{{ strtoupper($invoice->title) }}</h4>
                    <div class="fw-bold text-info fs-5 mt-1">INVOICE #{{ $invoice->invoice_number }}</div>
                    @php $st = strtolower($invoice->status); @endphp
                    <span class="badge bg-{{ $st === 'paid' ? 'success' : ($st === 'draft' ? 'secondary' : 'danger') }} fs-6 mt-2 px-3 py-1">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Billed Information Card -->
        <div class="card border shadow-sm mb-4">
            <div class="card-body">
                <div class="section-card-title"><i class="mdi mdi-account-group me-1"></i> Billed Information</div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <tbody>
                            <tr>
                                <td class="meta-box-label">Billed By</td>
                                <td class="meta-box-val">
                                    <strong>Elite Guard Inc.</strong><br>
                                    2104-3961 52 Ave NE, Calgary, AB T3J 0K7, Canada<br>
                                    Phone: +14039090602
                                </td>
                                <td class="meta-box-label">Billed To</td>
                                <td class="meta-box-val">
                                    <strong>{{ $invoice->company ? $invoice->company->name : 'N/A' }}</strong><br>
                                    @if($invoice->site)
                                        Site: {{ $invoice->site->name }}<br>
                                        {{ $invoice->site->address }}<br>
                                    @elseif($invoice->company)
                                        {{ $invoice->company->address }}<br>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Invoice Details Card -->
        <div class="card border shadow-sm mb-4">
            <div class="card-body">
                <div class="section-card-title"><i class="mdi mdi-information-outline me-1"></i> Invoice Details</div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <tbody>
                            <tr>
                                <td class="meta-box-label">Invoice Date</td>
                                <td class="meta-box-val"><strong>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</strong></td>
                                <td class="meta-box-label">Payment Due</td>
                                <td class="meta-box-val"><strong>{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</strong></td>
                            </tr>
                            <tr>
                                <td class="meta-box-label">PO/SO Number</td>
                                <td class="meta-box-val">{{ $invoice->po_so_number ?: 'N/A' }}</td>
                                <td class="meta-box-label">Amount Due</td>
                                <td class="meta-box-val"><strong class="text-danger fs-6">$ {{ number_format($invoice->amount_due, 2) }}</strong></td>
                            </tr>
                            @if($invoice->summary)
                            <tr>
                                <td class="meta-box-label">Summary</td>
                                <td class="meta-box-val" colspan="3">{{ $invoice->summary }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Products & Services Card -->
        <div class="card border shadow-sm mb-4">
            <div class="card-body">
                <div class="section-card-title"><i class="mdi mdi-cube-outline me-1"></i> Products & Services</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-sky-header">
                            <tr>
                                <th>Product / Service</th>
                                <th class="text-center" width="120">Qty</th>
                                <th class="text-end" width="150">Rate</th>
                                <th class="text-end" width="150">Tax</th>
                                <th class="text-end" width="160">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                            <tr>
                                <td class="fw-bold text-dark">{{ $item->product_service }}</td>
                                <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                                <td class="text-end">$ {{ number_format($item->rate, 2) }}</td>
                                <td class="text-end">$ {{ number_format($item->tax, 2) }}</td>
                                <td class="text-end fw-bold text-dark">$ {{ number_format($item->amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Notes & Summary Totals Card -->
        <div class="card border shadow-sm mb-5">
            <div class="card-body">
                <div class="row align-items-start">
                    <div class="col-md-7 mb-3 mb-md-0">
                        @if($invoice->notes)
                            <div class="p-3 rounded bg-light border">
                                <strong class="text-primary d-block mb-1"><i class="mdi mdi-note-text-outline me-1"></i> Notes:</strong>
                                <p class="text-muted mb-0 font-size-13">{{ $invoice->notes }}</p>
                            </div>
                        @else
                            &nbsp;
                        @endif
                    </div>
                    <div class="col-md-5">
                        <div class="table-responsive">
                            <table class="table table-borderless font-size-15 mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-secondary text-end py-1" width="50%">Subtotal:</td>
                                        <td class="text-end fw-bold py-1 text-dark" width="50%">$ {{ number_format($invoice->subtotal, 2) }}</td>
                                    </tr>
                                    <tr class="border-bottom border-2 border-primary">
                                        <td class="text-secondary text-end py-1">Tax:</td>
                                        <td class="text-end fw-bold py-1 text-dark">$ {{ number_format($invoice->tax_total, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-primary text-end pt-2 fs-5">Total:</td>
                                        <td class="text-end fw-bold text-primary pt-2 fs-5">$ {{ number_format($invoice->total, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
