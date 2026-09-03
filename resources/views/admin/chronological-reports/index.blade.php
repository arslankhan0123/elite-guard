@extends('dashboardLayouts.main')
@section('title', 'System Reports')

@section('breadcrumbTitle', 'System Reports')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">System Reports</li>
@endsection

@section('content')
<style>
    /* Styling the Select2 Multiple dropdown to match Bootstrap form-select style and height */
    .select2-container--default .select2-selection--multiple {
        background-color: #f8f9fa !important;
        border: 1px solid #eff0f2 !important;
        border-radius: 6px !important;
        min-height: 38px !important;
        padding: 2px 6px !important;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #86b7fe !important;
        outline: 0 !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #00249c !important;
        color: white !important;
        border: 1px solid #00249c !important;
        border-radius: 4px !important;
        padding: 1px 8px !important;
        margin: 2px 4px 2px 0 !important;
        font-size: 12px !important;
        display: inline-flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white !important;
        margin-right: 5px !important;
        order: -1;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ff5f5f !important;
    }
    .select2-container--default .select2-search--inline .select2-search__field {
        margin: 2px 0 !important;
        height: 26px !important;
        font-family: inherit;
    }
    .select2-container {
        width: 100% !important;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 fw-bold text-dark"><i class="mdi mdi-chart-timeline-variant text-primary me-2"></i>System Scans & Patrol Activity Log</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Search & Filters -->
                <form action="{{ route('chronological-reports.index') }}" method="GET" class="row g-3 mb-4 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-muted">User/Guard</label>
                        <select name="user_id" class="form-select bg-light">
                            <option value="">All Guards</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $selectedUser == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-muted">Sites</label>
                        <select name="site_ids[]" id="site_ids_select" class="form-select select2 bg-light" multiple="multiple" data-placeholder="Select Sites">
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ in_array($site->id, (array)$selectedSites) ? 'selected' : '' }}>{{ $site->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-muted">Weekly Runsheet</label>
                        <select name="weekly_run_sheet_id" class="form-select bg-light">
                            <option value="">All Runsheets</option>
                            @foreach($weeklyRunSheets as $wrs)
                                <option value="{{ $wrs->id }}" {{ $selectedWeeklyRunSheet == $wrs->id ? 'selected' : '' }}>{{ $wrs->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-muted">Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control bg-light" value="{{ $startDate }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-muted">End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control bg-light" value="{{ $endDate }}" required>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label fw-semibold text-muted">Start Time</label>
                        <input type="time" name="start_time" class="form-control bg-light" value="{{ $startTime }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label fw-semibold text-muted">End Time</label>
                        <input type="time" name="end_time" class="form-control bg-light" value="{{ $endTime }}">
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('chronological-reports.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Reset</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="mdi mdi-magnify me-1"></i> Generate Report
                        </button>
                    </div>
                </form>

                @if($hasSearched)
                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0">Chronological Activity Log: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</h5>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-danger rounded-pill px-4" id="bulkDeleteBtn" style="display: none;" onclick="confirmBulkDelete()">
                                <i class="mdi mdi-trash-can me-1"></i> Delete Selected (<span id="selectedCount">0</span>)
                            </button>
                            @if(count($reports) > 0)
                                <a href="{{ route('chronological-reports.pdf', request()->query()) }}" class="btn btn-danger rounded-pill px-4">
                                    <i class="mdi mdi-file-pdf-box me-1"></i> Export PDF
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="table-responsive">                   
                        <table id="custom-table" class="table table-bordered align-middle">
                            <thead>
                                <tr class="table-light">
                                    <th style="width: 40px;" class="text-center">
                                        <input type="checkbox" id="selectAllTours" class="form-check-input">
                                    </th>
                                    <th style="width: 40px;" class="text-center">#</th>
                                    <th>Date</th>
                                    <th>Interval (Time)</th>
                                    <th>User</th>
                                    <th>Site</th>
                                    <th>Tour / Patrol Name</th>
                                    <th class="text-center">Required</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $index => $report)
                                @php 
                                    $statusClass = match($report['status']) {
                                        'Completed' => 'bg-success',
                                        'Partial' => 'bg-warning text-dark',
                                        default => 'bg-danger'
                                    };
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input tour-checkbox" data-type="{{ $report['type'] }}" data-id="{{ $report['id'] }}" data-date="{{ $report['date'] }}" data-user-id="{{ $report['user_id'] ?? '' }}">
                                    </td>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($report['date'])->format('d M Y') }}</td>
                                    <td class="fw-bold text-primary">
                                        {{ \Carbon\Carbon::parse($report['start_time'])->format('h:i A') }} - 
                                        {{ \Carbon\Carbon::parse($report['end_time'])->format('h:i A') }}
                                    </td>
                                    <td>{{ $report['user'] }}</td>
                                    <td>{{ $report['site'] }}</td>
                                    <td>
                                        <span class="badge bg-soft-info text-info px-2 py-1 me-1">
                                            {{ $report['type'] }}
                                        </span>
                                        <strong>{{ $report['name'] }}</strong>
                                    </td>
                                    <td class="text-center fw-bold">{{ $report['required_count'] }}</td>
                                    <td class="text-center fw-bold text-success">{{ $report['scanned_count'] }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $statusClass }} px-3 py-1 rounded-pill">{{ $report['status'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="bin-button mx-auto" onclick="confirmDelete('{{ addslashes($report['name']) }}', '{{ $report['type'] }}', '{{ $report['id'] }}', '{{ $report['date'] }}', '{{ $report['user_id'] ?? '' }}')">
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
                                        </button>
                                    </td>
                                </tr>
                                <tr class="bg-light-subtle">
                                    <td></td>
                                    <td colspan="10" class="p-3">
                                        <div class="ps-2" style="border-left: 3px solid #7c3aed;">
                                            <div class="mb-2">
                                                <strong class="text-primary"><i class="mdi mdi-check-circle-outline"></i> Scanned Checkpoints:</strong>
                                                @if(count($report['scans']) > 0)
                                                     <div class="d-flex flex-wrap gap-2 mt-2">
                                                         @foreach($report['scans'] as $scan)
                                                             @php
                                                                 $scanImg = null;
                                                                 if (!empty($scan['image'])) {
                                                                     $scanImg = $scan['image'];
                                                                     if (!str_starts_with($scanImg, 'http://') && !str_starts_with($scanImg, 'https://')) {
                                                                         if (str_starts_with($scanImg, 'storage/')) {
                                                                             $scanImg = asset($scanImg);
                                                                         } elseif (str_starts_with($scanImg, '/storage/')) {
                                                                             $scanImg = asset(ltrim($scanImg, '/'));
                                                                         } else {
                                                                             $scanImg = asset('storage/' . ltrim($scanImg, '/'));
                                                                         }
                                                                     }
                                                                 }
                                                             @endphp
                                                             <div class="card p-2 border rounded shadow-sm d-flex flex-row align-items-center justify-content-between bg-white m-0" style="width: 280px; min-width: 280px; flex: 0 0 280px; min-height: 96px;">
                                                                 <div style="flex: 1; min-width: 0; padding-right: 8px;">
                                                                     <div class="fw-bold text-dark text-truncate" style="font-size: 11.5px;" title="{{ $scan['name'] }}">{{ $scan['name'] }}</div>
                                                                     <div class="text-muted text-truncate" style="font-size: 9.5px;"><strong>UID:</strong> {{ $scan['uid'] }}</div>
                                                                     <div class="text-muted" style="font-size: 9.5px;"><strong>Time:</strong> {{ \Carbon\Carbon::parse($scan['time'])->format('h:i:s A') }}</div>
                                                                     <div class="text-muted text-truncate" style="font-size: 9.5px;"><strong>By:</strong> {{ $scan['user'] }}</div>
                                                                 </div>
                                                                 @if($scanImg)
                                                                     <div style="flex-shrink: 0;">
                                                                         <img src="{{ $scanImg }}" alt="scan" class="rounded border" style="width: 75px; height: 75px; object-fit: cover;">
                                                                     </div>
                                                                 @endif
                                                             </div>
                                                         @endforeach
                                                     </div>
                                                @else
                                                    <span class="text-muted italic ms-1">No NFC tags scanned</span>
                                                @endif
                                            </div>
                                            <div>
                                                <strong class="text-danger"><i class="mdi mdi-alert-circle-outline"></i> Missing Checkpoints:</strong>
                                                @if(count($report['missing_tags']) > 0)
                                                    <span class="text-danger-emphasis fw-bold ms-1">
                                                        {{ implode(', ', $report['missing_tags']) }}
                                                    </span>
                                                @else
                                                    <span class="text-success fw-bold ms-1">None</span>
                                                @endif
                                            </div>
                                            @if(!empty($report['images']) && count($report['images']) > 0)
                                            <div class="mt-2 pt-2 border-top">
                                                <strong class="text-secondary"><i class="mdi mdi-camera-outline me-1"></i> Tour Images:</strong>
                                                <div class="d-flex flex-wrap gap-2 mt-2">
                                                    @foreach($report['images'] as $imgUrl)
                                                        @php
                                                            $formattedImg = $imgUrl;
                                                            if (!str_starts_with($formattedImg, 'http://') && !str_starts_with($formattedImg, 'https://')) {
                                                                if (str_starts_with($formattedImg, 'storage/')) {
                                                                    $formattedImg = asset($formattedImg);
                                                                } elseif (str_starts_with($formattedImg, '/storage/')) {
                                                                    $formattedImg = asset(ltrim($formattedImg, '/'));
                                                                } else {
                                                                    $formattedImg = asset('storage/' . ltrim($formattedImg, '/'));
                                                                }
                                                            }
                                                        @endphp
                                                        <a href="{{ $formattedImg }}" target="_blank" class="d-inline-block">
                                                            <img src="{{ $formattedImg }}" alt="Tour Image" class="rounded border shadow-sm" style="width: 85px; height: 85px; object-fit: cover;">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-5">
                                        <i class="mdi mdi-alert-circle-outline font-size-24 mb-2 d-block text-warning"></i>
                                        No activity logs or scans found for the selected parameters.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<form id="delete-form" action="{{ route('chronological-reports.destroy') }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="type" id="delete-type">
    <input type="hidden" name="id" id="delete-id">
    <input type="hidden" name="date" id="delete-date">
    <input type="hidden" name="user_id" id="delete-user-id">
    <input type="hidden" name="items" id="delete-items">
</form>

<script>
function confirmDelete(name, type, id, date, userId) {
    if (confirm("Are you sure you want to delete " + name + "?")) {
        document.getElementById('delete-type').value = type;
        document.getElementById('delete-id').value = id;
        document.getElementById('delete-date').value = date;
        document.getElementById('delete-user-id').value = userId || '';
        document.getElementById('delete-items').value = '';
        document.getElementById('delete-form').submit();
    }
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.tour-checkbox:checked');
    const bulkBtn = document.getElementById('bulkDeleteBtn');
    const countSpan = document.getElementById('selectedCount');
    const selectAll = document.getElementById('selectAllTours');
    const allCheckboxes = document.querySelectorAll('.tour-checkbox');

    if (countSpan) countSpan.textContent = checked.length;
    if (bulkBtn) bulkBtn.style.display = checked.length > 0 ? 'inline-block' : 'none';
    if (selectAll) selectAll.checked = allCheckboxes.length > 0 && checked.length === allCheckboxes.length;
}

function confirmBulkDelete() {
    const checkedBoxes = document.querySelectorAll('.tour-checkbox:checked');
    if (checkedBoxes.length === 0) return;

    if (confirm("Are you sure you want to delete " + checkedBoxes.length + " selected tour log(s)?")) {
        const items = [];
        checkedBoxes.forEach(box => {
            items.push({
                type: box.dataset.type,
                id: box.dataset.id,
                date: box.dataset.date,
                user_id: box.dataset.userId
            });
        });
        document.getElementById('delete-type').value = '';
        document.getElementById('delete-id').value = '';
        document.getElementById('delete-date').value = '';
        document.getElementById('delete-user-id').value = '';
        document.getElementById('delete-items').value = JSON.stringify(items);
        document.getElementById('delete-form').submit();
    }
}

$(document).ready(function() {
    $('#site_ids_select').select2({
        placeholder: "Select Sites",
        allowClear: true
    });

    const selectAll = document.getElementById('selectAllTours');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const boxes = document.querySelectorAll('.tour-checkbox');
            boxes.forEach(box => box.checked = this.checked);
            updateSelectedCount();
        });
    }

    $(document).on('change', '.tour-checkbox', function() {
        updateSelectedCount();
    });
});
</script>
@endsection
