@extends('dashboardLayouts.main')
@section('title', 'Invoices')

@section('breadcrumbTitle', 'Invoice Listing')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Invoices</li>
@endsection

@section('content')
<style>
    .stat-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px 20px;
        text-align: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
    }
    .stat-card .stat-value {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .stat-card .stat-label {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 12px;
    }
    .stat-card .stat-count {
        font-size: 0.85rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    .invoice-control-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    .status-badge-active {
        background-color: #e0f2fe;
        color: #0284c7;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 6px;
    }
    .status-badge-overdue {
        background-color: #fee2e2;
        color: #ef4444;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 6px;
    }
    .status-badge-paid {
        background-color: #d1fae5;
        color: #10b981;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 6px;
    }
    .status-badge-draft {
        background-color: #f3f4f6;
        color: #6b7280;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .btn-create-invoice {
        background-color: #2563eb;
        color: #ffffff;
        font-weight: 600;
        border-radius: 8px;
        padding: 8px 16px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .btn-create-invoice:hover {
        background-color: #1d4ed8;
        color: #ffffff;
    }
</style>

@php
    function formatStatAmount($num) {
        if ($num >= 1000000) {
            return '$ ' . number_format($num / 1000000, 1) . 'M';
        } elseif ($num >= 1000) {
            return '$ ' . number_format($num / 1000, 1) . 'K';
        }
        return '$ ' . number_format($num, 2);
    }
@endphp

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="fw-bold text-dark mb-0">Invoice Stats</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-value text-primary">{{ formatStatAmount($totalInvoicesSum) }}</div>
                            <div class="stat-label text-primary">Total Invoices</div>
                            <div class="stat-count">{{ $totalInvoicesCount }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-value text-danger">{{ formatStatAmount($totalOverdueSum) }}</div>
                            <div class="stat-label text-danger">Total Overdue</div>
                            <div class="stat-count">{{ $totalOverdueCount }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-value text-warning">{{ formatStatAmount($paidSum) }}</div>
                            <div class="stat-label text-warning">Paid</div>
                            <div class="stat-count">{{ $paidCount }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-value text-secondary">{{ formatStatAmount($draftSum) }}</div>
                            <div class="stat-label text-secondary">Draft Invoices</div>
                            <div class="stat-count">{{ $draftCount }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-check-all me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-alert-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(Auth::user()->hasAdminPermission('invoices', 'create'))
                <div class="invoice-control-bar">
                    <div class="d-flex align-items-center gap-2">
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-outline-secondary dropdown-toggle rounded-pill px-3" type="button" id="actionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Action
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="actionDropdown">
                                <li><a class="dropdown-item" href="{{ route('invoices.create') }}">Create Invoice</a></li>
                            </ul>
                        </div>
                        <a href="{{ route('invoices.create') }}" class="btn-create-invoice ms-2">
                            <i class="fa fa-plus"></i> Create Invoice
                        </a>
                    </div>
                </div>
                @endif

                <div class="table-responsive">
                    <table id="custom-table" class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="30"><input type="checkbox" id="selectAll"></th>
                                <th>Date</th>
                                <th>Invoice</th>
                                <th>Client</th>
                                <th>Total</th>
                                <th>Amount Due</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th width="80">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                            <tr>
                                <td><input type="checkbox" name="selected_invoices[]" value="{{ $invoice->id }}"></td>
                                <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</td>
                                <td class="fw-bold text-dark">
                                    <a href="{{ route('invoices.show', $invoice->id) }}" class="text-decoration-none text-dark">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td>
                                    {{ $invoice->company ? $invoice->company->name : 'N/A' }}
                                    @if($invoice->site)
                                        <br><small class="text-muted"><i class="fa fa-map-marker-alt me-1"></i>{{ $invoice->site->name }}</small>
                                    @endif
                                </td>
                                <td class="fw-semibold">$ {{ number_format($invoice->total, 2) }}</td>
                                <td class="fw-semibold text-danger">$ {{ number_format($invoice->amount_due, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</td>
                                <td>
                                    @php $st = strtolower($invoice->calculated_status); @endphp
                                    @if($st === 'paid')
                                        <span class="status-badge-paid">Paid</span>
                                    @elseif($st === 'draft')
                                        <span class="status-badge-draft">Draft</span>
                                    @elseif($st === 'overdue')
                                        <span class="status-badge-overdue">Overdue</span>
                                    @else
                                        <span class="status-badge-active">Active</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Invoice Actions">
                                        @if(Auth::user()->hasAdminPermission('invoices', 'view'))
                                        <a class="text-decoration-none text-dark me-2" href="{{ route('invoices.show', $invoice->id) }}" data-bs-toggle="tooltip" title="View Invoice">
                                            <button class="view_btn">
                                            </button>
                                        </a>
                                        <a class="text-decoration-none text-dark me-2" href="{{ route('invoices.downloadPdf', $invoice->id) }}" data-bs-toggle="tooltip" title="Download PDF">
                                            <button class="btn btn-sm btn-danger d-flex align-items-center justify-content-center" style="width: 1.875rem; height: 1.875rem; padding: 0; border-radius: 7px;" type="button">
                                                <i data-feather="file-text" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        </a>
                                        <a class="text-decoration-none text-dark me-2" href="{{ route('invoices.sendEmail', $invoice->id) }}" data-bs-toggle="tooltip" title="Send Email to Client" onclick="return confirm('Send invoice email to {{ $invoice->company ? $invoice->company->email : 'client' }}?')">
                                            <button class="btn btn-sm btn-primary d-flex align-items-center justify-content-center" style="width: 1.875rem; height: 1.875rem; padding: 0; border-radius: 7px; background-color: #2563eb; border-color: #2563eb;" type="button">
                                                <i data-feather="mail" style="width: 14px; height: 14px;"></i>
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
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fa fa-file-invoice fa-2x mb-2 d-block text-secondary"></i>
                                    No invoices found. <a href="{{ route('invoices.create') }}">Create your first invoice</a>.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
