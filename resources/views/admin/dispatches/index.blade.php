@extends('dashboardLayouts.main')
@section('title', 'Dispatch Tasks')

@section('breadcrumbTitle', 'Dispatch Tasks')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Dispatch Tasks</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 fw-bold text-dark"><i class="mdi mdi-send text-primary me-2"></i>Dispatch Log</h4>
                <a href="{{ route('dispatches.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="mdi mdi-plus-circle me-1"></i> New Dispatch
                </a>
            </div>
            <div class="card-body">
                <!-- Search & Filters -->
                <form action="{{ route('dispatches.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" placeholder="Search Caller, Location, Type..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="priority" class="form-select bg-light" onchange="this.form.submit()">
                            <option value="">All Priorities</option>
                            <option value="Low" {{ request('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ request('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ request('priority') == 'High' ? 'selected' : '' }}>High</option>
                            <option value="Emergency" {{ request('priority') == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select bg-light" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <a href="{{ route('dispatches.index') }}" class="btn btn-outline-secondary rounded-pill">Reset</a>
                    </div>
                </form>

                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-xs rounded-3 mb-4">
                        <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">                   
                    <table id="custom-table" class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date/Time</th>
                                <th>Client / Site</th>
                                <th>Caller Name</th>
                                <th>Incident Type</th>
                                <th>Assigned Guard</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dispatches as $dispatch)
                            <tr>
                                <td>#{{ $dispatch->id }}</td>
                                <td>
                                    <span class="d-block fw-bold text-dark">{{ \Carbon\Carbon::parse($dispatch->incident_date)->format('d M Y') }}</span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($dispatch->incident_time)->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <span class="d-block fw-semibold text-dark">{{ $dispatch->company->name ?? 'N/A' }}</span>
                                    <small class="text-primary">{{ $dispatch->site->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $dispatch->caller_type }}</span>
                                    <span class="d-block fw-semibold">{{ $dispatch->caller_name }}</span>
                                </td>
                                <td><span class="fw-semibold text-dark">{{ $dispatch->incident_type }}</span></td>
                                <td>
                                    @if($dispatch->assignedGuard)
                                        <span class="text-dark fw-bold"><i class="mdi mdi-account text-secondary me-1"></i>{{ $dispatch->assignedGuard->name }}</span>
                                    @else
                                        <span class="text-muted fst-italic">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $pBadge = match($dispatch->priority) {
                                            'Low' => 'bg-secondary-subtle text-secondary',
                                            'Medium' => 'bg-info-subtle text-info',
                                            'High' => 'bg-warning-subtle text-warning-emphasis',
                                            'Emergency' => 'bg-danger-subtle text-danger',
                                            default => 'bg-light text-dark'
                                        };
                                    @endphp
                                    <span class="badge {{ $pBadge }} px-2 py-1">{{ $dispatch->priority }}</span>
                                </td>
                                <td>
                                    @php
                                        $sBadge = match($dispatch->status) {
                                            'Pending' => 'bg-warning-subtle text-warning',
                                            'In Progress' => 'bg-primary-subtle text-primary',
                                            'Completed' => 'bg-success-subtle text-success',
                                            'Cancelled' => 'bg-danger-subtle text-danger',
                                            default => 'bg-light text-dark'
                                        };
                                    @endphp
                                    <span class="badge {{ $sBadge }} px-2 py-1 rounded-pill">{{ $dispatch->status }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Dispatch Actions">
                                        <a class="text-decoration-none text-dark" href="{{ route('dispatches.show', $dispatch->id) }}" data-bs-toggle="tooltip" title="View Details">
                                            <button class="view_btn me-2">
                                            </button>
                                        </a>
                                        <a class="text-decoration-none me-2 text-dark ml-1" href="{{ route('dispatches.edit', $dispatch->id) }}" data-bs-toggle="tooltip" title="Edit Dispatch">
                                            <button class="editBtn">
                                                <svg height="1em" viewBox="0 0 512 512">
                                                    <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                                                </svg>
                                            </button>
                                        </a>
                                        <a href="{{ route('dispatches.delete', $dispatch->id) }}" class="bin-button ml-1" data-bs-toggle="tooltip" title="Delete Dispatch" onclick="return confirm('Are you sure you want to delete this dispatch record?')">
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
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No dispatch logs found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $dispatches->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
