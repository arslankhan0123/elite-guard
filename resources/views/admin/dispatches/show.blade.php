@extends('dashboardLayouts.main')
@section('title', 'Dispatch Details')

@section('breadcrumbTitle', 'Dispatch Details')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('dispatches.index') }}">Dispatch Tasks</a></li>
<li class="breadcrumb-item active">Dispatch #{{ $dispatch->id }}</li>
@endsection

@section('content')
<div class="row">
    <!-- Top Action Bar -->
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('dispatches.index') }}" class="btn btn-light rounded-pill"><i class="mdi mdi-arrow-left me-1"></i> Back to Directory</a>
            <div class="d-flex gap-2">
                <a href="{{ route('dispatches.edit', $dispatch->id) }}" class="btn btn-primary rounded-pill px-4"><i class="mdi mdi-pencil me-1"></i> Edit Details</a>
                <a href="{{ route('dispatches.delete', $dispatch->id) }}" class="btn btn-outline-danger rounded-pill" onclick="return confirm('Are you sure you want to delete this dispatch record?')"><i class="mdi mdi-trash-can-outline me-1"></i> Delete</a>
            </div>
        </div>
    </div>

    <!-- Left Column: Details -->
    <div class="col-lg-8">
        <!-- Incident Information -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-danger text-white py-3 rounded-top-4">
                <h5 class="fw-bold mb-0 text-white"><i class="mdi mdi-alert-octagon-outline me-2"></i>Incident Information</h5>
            </div>
            <div class="card-body p-4">
                <div class="row mb-3 pb-2 border-bottom">
                    <div class="col-sm-4 text-muted small fw-bold text-uppercase">Incident Type</div>
                    <div class="col-sm-8 text-dark fw-bold" style="font-size: 1.1rem;">{{ $dispatch->incident_type }}</div>
                </div>
                <div class="row mb-3 pb-2 border-bottom">
                    <div class="col-sm-4 text-muted small fw-bold text-uppercase">Location</div>
                    <div class="col-sm-8 text-dark fw-semibold"><i class="mdi mdi-map-marker text-danger me-1"></i>{{ $dispatch->incident_location }}</div>
                </div>
                <div class="row mb-3 pb-2 border-bottom">
                    <div class="col-sm-4 text-muted small fw-bold text-uppercase">Date & Time</div>
                    <div class="col-sm-8 text-dark fw-semibold">
                        <i class="mdi mdi-calendar me-1 text-primary"></i>{{ \Carbon\Carbon::parse($dispatch->incident_date)->format('F j, Y') }}
                        <span class="mx-2 text-muted">|</span>
                        <i class="mdi mdi-clock-outline me-1 text-primary"></i>{{ \Carbon\Carbon::parse($dispatch->incident_time)->format('h:i A') }}
                    </div>
                </div>

                <div class="mt-4">
                    <h6 class="text-secondary small fw-bold text-uppercase mb-2">Description / Details</h6>
                    <div class="bg-light p-3 rounded-3 text-dark style-scroll border" style="max-height: 250px; overflow-y: auto; white-space: pre-wrap;">{{ $dispatch->incident_details }}</div>
                </div>
            </div>
        </div>

        <!-- Action Taken -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-success text-white py-3 rounded-top-4">
                <h5 class="fw-bold mb-0 text-white"><i class="mdi mdi-check-decagram-outline me-2"></i>Action Taken</h5>
            </div>
            <div class="card-body p-4">
                @if($dispatch->action_taken)
                    <div class="p-3 bg-success-subtle bg-opacity-10 border border-success-subtle rounded-3 text-dark" style="white-space: pre-wrap;">{{ $dispatch->action_taken }}</div>
                @else
                    <p class="text-muted fst-italic py-3 mb-0">No actions recorded yet.</p>
                @endif
            </div>
        </div>

        <!-- Supervisor / Internal Notes -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-secondary text-white py-3 rounded-top-4">
                <h5 class="fw-bold mb-0 text-white"><i class="mdi mdi-shield-outline me-2"></i>Internal Supervisor Notes</h5>
            </div>
            <div class="card-body p-4">
                @if($dispatch->internal_notes)
                    <div class="p-3 bg-light border rounded-3 text-dark" style="white-space: pre-wrap;">{{ $dispatch->internal_notes }}</div>
                @else
                    <p class="text-muted fst-italic py-3 mb-0">No internal notes added.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Status & Assignment -->
    <div class="col-lg-4">
        <!-- Status & Priority Card -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-primary text-white py-3 rounded-top-4">
                <h5 class="fw-bold mb-0 text-white"><i class="mdi mdi-information-outline me-2"></i>Status & Priority</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small fw-bold text-uppercase">Status</span>
                    @php
                        $sBadge = match($dispatch->status) {
                            'Pending' => 'bg-warning-subtle text-warning',
                            'In Progress' => 'bg-primary-subtle text-primary',
                            'Completed' => 'bg-success-subtle text-success',
                            'Cancelled' => 'bg-danger-subtle text-danger',
                            default => 'bg-light text-dark'
                        };
                    @endphp
                    <span class="badge {{ $sBadge }} px-3 py-2 rounded-pill fw-bold" style="font-size: 0.85rem;">{{ $dispatch->status }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small fw-bold text-uppercase">Priority</span>
                    @php
                        $pBadge = match($dispatch->priority) {
                            'Low' => 'bg-secondary-subtle text-secondary',
                            'Medium' => 'bg-info-subtle text-info',
                            'High' => 'bg-warning-subtle text-warning-emphasis',
                            'Emergency' => 'bg-danger-subtle text-danger',
                            default => 'bg-light text-dark'
                        };
                    @endphp
                    <span class="badge {{ $pBadge }} px-3 py-2 rounded-pill fw-bold" style="font-size: 0.85rem;">{{ $dispatch->priority }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small fw-bold text-uppercase">Logged At</span>
                    <span class="text-dark small fw-bold">{{ $dispatch->created_at->format('j M Y, g:i A') }}</span>
                </div>
            </div>
        </div>

        <!-- Caller & Guard Assignment Card -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-dark text-white py-3 rounded-top-4">
                <h5 class="fw-bold mb-0 text-white"><i class="mdi mdi-account-group-outline me-2"></i>Assignment & Caller</h5>
            </div>
            <div class="card-body p-4">
                <!-- Client & Site -->
                <div class="mb-4">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Client & Post Site</span>
                    <span class="text-dark fw-bold d-block" style="font-size: 1rem;">{{ $dispatch->company->name ?? 'N/A' }}</span>
                    <span class="text-primary small fw-semibold"><i class="mdi mdi-domain me-1"></i>{{ $dispatch->site->name ?? 'N/A' }}</span>
                </div>

                <!-- Caller -->
                <div class="mb-4 border-top pt-3">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Caller Information</span>
                    <span class="text-dark fw-bold d-block">{{ $dispatch->caller_name }}</span>
                    <span class="badge bg-light text-dark px-2 py-1 mt-1">{{ $dispatch->caller_type }}</span>
                </div>

                <!-- Assigned Guard -->
                <div class="border-top pt-3">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Assigned Security Guard</span>
                    @if($dispatch->assignedGuard)
                        <div class="d-flex align-items-center mt-2">
                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                {{ strtoupper(substr($dispatch->assignedGuard->name, 0, 2)) }}
                            </div>
                            <div>
                                <span class="text-dark fw-bold d-block">{{ $dispatch->assignedGuard->name }}</span>
                                <small class="text-muted">{{ $dispatch->assignedGuard->email }}</small>
                            </div>
                        </div>
                    @else
                        <span class="text-muted fst-italic d-block py-2"><i class="mdi mdi-account-question-outline me-1"></i>No guard assigned yet.</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Attachment Card -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-info text-white py-3 rounded-top-4">
                <h5 class="fw-bold mb-0 text-white"><i class="mdi mdi-attachment me-2"></i>Attachment</h5>
            </div>
            <div class="card-body p-4">
                @if($dispatch->attachment_path)
                    <div class="d-flex flex-column gap-2">
                        <div class="p-2 bg-light rounded border border-light text-truncate mb-2" style="font-size: 0.85rem;">
                            <i class="mdi mdi-file-outline me-1 text-primary"></i> {{ basename($dispatch->attachment_path) }}
                        </div>
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($dispatch->attachment_path) }}" target="_blank" class="btn btn-outline-info rounded-pill w-100 fw-bold">
                            <i class="mdi mdi-download me-1"></i> Download / View File
                        </a>
                    </div>
                @else
                    <p class="text-muted fst-italic text-center mb-0 py-2">No attachments uploaded.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
