@extends('dashboardLayouts.main')

@section('title', 'Reports | Elite Guard')
@section('breadcrumbTitle', 'Reports Management')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-md rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(255, 255, 255, 0.2);">
                                    <i class="mdi mdi-database-outline text-white font-size-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-white-50 mb-1 fw-medium">Total {{ ucfirst(str_replace('_', ' ', $type)) }}</p>
                                <h3 class="mb-0 text-white fw-bold">{{ $results->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-md rounded-circle d-flex align-items-center justify-content-center bg-success-subtle">
                                    <i class="mdi mdi-calendar-check text-success font-size-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1 fw-medium">Report Period</p>
                                <h5 class="mb-0 fw-bold text-dark">{{ $date_filter ? ucfirst(str_replace('_', ' ', $date_filter)) : 'All Time' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-md rounded-circle d-flex align-items-center justify-content-center bg-info-subtle">
                                    <i class="mdi mdi-clock-outline text-info font-size-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1 fw-medium">Generated On</p>
                                <h5 class="mb-0 fw-bold text-dark">{{ now()->format('M d, Y H:i') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="mdi mdi-file-chart-outline me-2"></i>Generate Reports
                    </h5>
                    <div class="d-flex gap-2">
                        <button onclick="window.print()" class="btn btn-light border-0 shadow-sm fw-bold px-3 d-flex align-items-center" style="border-radius: 10px; height: 40px;">
                            <i class="mdi mdi-printer me-1 text-primary"></i> <span class="d-none d-sm-inline">Print</span>
                        </button>
                        <button id="exportCsv" class="btn btn-soft-success border-0 shadow-sm fw-bold px-3 d-flex align-items-center" style="border-radius: 10px; height: 40px;">
                            <i class="mdi mdi-file-excel me-1"></i> <span class="d-none d-sm-inline">Export CSV</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('reports.index') }}" method="GET" id="reportFilterForm" class="row g-3 align-items-end mb-4">
                    <div class="col-md-6">
                        <label for="type" class="form-label fw-semibold text-muted">Report Type</label>
                        <select name="type" id="type" class="form-select border-0 bg-light shadow-none" onchange="this.form.submit()" style="height: 50px; border-radius: 12px; font-weight: 500;">
                            <option value="companies" {{ $type == 'companies' ? 'selected' : '' }}>Companies Report</option>
                            <option value="sites" {{ $type == 'sites' ? 'selected' : '' }}>Sites Report</option>
                            <option value="nfc_tags" {{ $type == 'nfc_tags' ? 'selected' : '' }}>NFC Tags Report</option>
                            <option value="employees" {{ $type == 'employees' ? 'selected' : '' }}>Employees Report</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="date_filter" class="form-label fw-semibold text-muted">Date Range</label>
                        <select name="date_filter" id="date_filter" class="form-select border-0 bg-light shadow-none" onchange="this.form.submit()" style="height: 50px; border-radius: 12px; font-weight: 500;">
                            <option value="">All Time</option>
                            <option value="today" {{ $date_filter == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="yesterday" {{ $date_filter == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                            <option value="current_week" {{ $date_filter == 'current_week' ? 'selected' : '' }}>Current Week</option>
                            <option value="previous_week" {{ $date_filter == 'previous_week' ? 'selected' : '' }}>Previous Week</option>
                            <option value="current_month" {{ $date_filter == 'current_month' ? 'selected' : '' }}>Current Month</option>
                            <option value="previous_month" {{ $date_filter == 'previous_month' ? 'selected' : '' }}>Previous Month</option>
                        </select>
                    </div>
                </form>

                <hr class="my-4 opacity-25">

                <div class="table-responsive">
                    <table id="custom-table" class="table align-middle table-nowrap mb-0">
                        <thead>
                            @if($type == 'companies')
                                <tr>
                                    <th>#</th>
                                    <th>Logo</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                </tr>
                            @elseif($type == 'sites')
                                <tr>
                                    <th>#</th>
                                    <th>Site Name</th>
                                    <th>Company</th>
                                    <th>City/Country</th>
                                    <th>Phone</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                </tr>
                            @elseif($type == 'nfc_tags')
                                <tr>
                                    <th>#</th>
                                    <th>Tag Name</th>
                                    <th>UID</th>
                                    <th>Site</th>
                                    <th>Company</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                </tr>
                            @elseif($type == 'employees')
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Joined At</th>
                                    <th>Status</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @forelse($results as $index => $item)
                                @if($type == 'companies')
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if($item->logo)
                                                <img src="{{ Storage::url($item->logo) }}" alt="" class="rounded-circle avatar-sm border p-1" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="avatar-sm d-inline-block">
                                                    <span class="avatar-title rounded-circle bg-primary-subtle text-primary font-size-16" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                        {{ substr($item->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="fw-bold">{{ $item->name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ $item->phone }}</td>
                                        <td>{{ $item->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge {{ $item->status ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-3 py-2">
                                                {{ $item->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                @elseif($type == 'sites')
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-bold">{{ $item->name }}</td>
                                        <td>{{ $item->company->name ?? 'N/A' }}</td>
                                        <td>{{ $item->city }}, {{ $item->country }}</td>
                                        <td>{{ $item->phone }}</td>
                                        <td>{{ $item->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge {{ $item->status ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-3 py-2">
                                                {{ $item->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                @elseif($type == 'nfc_tags')
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-bold">{{ $item->name }}</td>
                                        <td><code>{{ $item->uid }}</code></td>
                                        <td>{{ $item->site->name ?? 'N/A' }}</td>
                                        <td>{{ $item->site->company->name ?? 'N/A' }}</td>
                                        <td>{{ $item->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge {{ $item->status ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-3 py-2">
                                                {{ $item->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                @elseif($type == 'employees')
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-bold">{{ $item->user->name ?? 'N/A' }}</td>
                                        <td>{{ $item->user->email ?? 'N/A' }}</td>
                                        <td><span class="badge bg-info-subtle text-info">{{ $item->user->role ?? 'N/A' }}</span></td>
                                        <td>{{ $item->phone }}</td>
                                        <td>{{ $item->joining_date ? date('M d, Y', strtotime($item->joining_date)) : 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $item->status ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-3 py-2">
                                                {{ $item->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="mdi mdi-alert-circle-outline font-size-24 d-block mb-3"></i>
                                            <h5>No records found for this criteria.</h5>
                                        </div>
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

@section('scripts')
<style>
    @media print {
        .topnav, .card-header, #reportFilterForm, .breadcrumb, footer, .btn-close {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .page-content {
            padding: 0 !important;
            margin: 0 !important;
        }
        .container-fluid {
            padding: 0 !important;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const exportBtn = document.getElementById('exportCsv');
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                let table = document.getElementById('custom-table');
                let csv = [];
                // Get headers
                let headerRow = [];
                let headers = table.querySelectorAll('thead th');
                headers.forEach(header => {
                    headerRow.push('"' + header.innerText.trim().replace(/"/g, '""') + '"');
                });
                csv.push(headerRow.join(","));

                // Get body
                let rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    let rowData = [];
                    let cols = row.querySelectorAll('td');
                    if (cols.length > 0) {
                        cols.forEach(col => {
                            let data = col.innerText.replace(/(\r\n|\n|\r)/gm, "").trim();
                            data = data.replace(/"/g, '""');
                            rowData.push('"' + data + '"');
                        });
                        csv.push(rowData.join(","));
                    }
                });

                let csv_string = csv.join("\n");
                let filename = 'report_' + '{{ $type }}' + '_' + '{{ $date_filter ?: "all_time" }}' + '.csv';
                let blob = new Blob([csv_string], { type: 'text/csv;charset=utf-8;' });
                if (navigator.msSaveBlob) { // IE 10+
                    navigator.msSaveBlob(blob, filename);
                } else {
                    let link = document.createElement("a");
                    if (link.download !== undefined) {
                        let url = URL.createObjectURL(blob);
                        link.setAttribute("href", url);
                        link.setAttribute("download", filename);
                        link.style.visibility = 'hidden';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                }
            });
        }
    });
</script>
@endsection
