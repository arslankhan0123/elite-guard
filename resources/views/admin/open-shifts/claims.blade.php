@extends('dashboardLayouts.main')
@section('title', 'Shift Claim Requests')

@section('breadcrumbTitle', 'Shift Claim Requests')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('open-shifts.index') }}">Open Shifts</a></li>
    <li class="breadcrumb-item active">Claims</li>
@endsection

@section('content')
<div class="row g-4">

    {{-- Header --}}
    <div class="col-12">
        <div class="card border-0 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #92400e 0%, #d97706 100%);">
            <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold text-white mb-1">
                        <i data-feather="bell" class="me-2" style="width:22px;"></i> Shift Claim Requests
                    </h4>
                    <p class="text-white mb-0" style="opacity:.8;">
                        Review and approve or reject employee shift claims
                        @if($pendingClaimsCount > 0)
                            &mdash; <strong>{{ $pendingClaimsCount }} pending</strong>
                        @endif
                    </p>
                </div>
                <a href="{{ route('open-shifts.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">
                    <i data-feather="arrow-left" style="width:16px;" class="me-1"></i> Back to Open Shifts
                </a>
            </div>
        </div>
    </div>

    {{-- Status Filter Tabs --}}
    <div class="col-12">
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex gap-2 flex-wrap">
                    @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $val => $label)
                        <a href="{{ route('open-shifts.claims', ['shift_id' => $shiftId, 'status' => $val]) }}"
                           class="btn rounded-pill px-4 fw-semibold {{ $statusFilter === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
                            {{ $label }}
                            @if($val === 'pending' && $pendingClaimsCount > 0)
                                <span class="badge bg-danger ms-1">{{ $pendingClaimsCount }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Claims Table --}}
    <div class="col-12">
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body p-4">
                @if($claims->isEmpty())
                    <div class="text-center py-5">
                        <div style="font-size:4rem;">🎉</div>
                        <h5 class="fw-bold text-muted mt-3">No claim requests found</h5>
                        <p class="text-muted">
                            @if($statusFilter !== 'all')
                                No <strong>{{ $statusFilter }}</strong> claims at the moment.
                            @else
                                Once employees claim open shifts, requests will appear here.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table id="custom-table" class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Open Shift</th>
                                    <th>Site</th>
                                    <th>Date & Time</th>
                                    <th>Claimed At</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($claims as $claim)
                                    <tr class="{{ $claim->status === 'pending' ? 'table-warning bg-opacity-10' : '' }}">
                                        {{-- Employee --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center"
                                                     style="width:38px;height:38px;flex-shrink:0;">
                                                    <i data-feather="user" style="width:16px;"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $claim->user->name }}</div>
                                                    <small class="text-muted">{{ $claim->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Shift --}}
                                        <td>
                                            <span class="fw-semibold">{{ $claim->openShift->shift_name }}</span>
                                        </td>

                                        {{-- Site --}}
                                        <td>
                                            <span class="badge bg-soft-info text-info rounded-pill px-2">
                                                <i data-feather="map-pin" style="width:11px;" class="me-1"></i>
                                                {{ $claim->openShift->site->name }}
                                            </span>
                                        </td>

                                        {{-- Date & Time --}}
                                        <td>
                                            <div>{{ \Carbon\Carbon::parse($claim->openShift->date)->format('D, d M Y') }}</div>
                                            <small class="text-muted">
                                                {{ substr($claim->openShift->start_time,0,5) }} – {{ substr($claim->openShift->end_time,0,5) }}
                                            </small>
                                        </td>

                                        {{-- Claimed At --}}
                                        <td>
                                            <small class="text-muted">
                                                {{ $claim->created_at->format('d M Y, h:i A') }}
                                            </small>
                                        </td>

                                        {{-- Status --}}
                                        <td>
                                            @if($claim->status === 'pending')
                                                <span class="badge bg-soft-warning text-warning fw-bold px-3 rounded-pill">
                                                    <i data-feather="clock" style="width:11px;" class="me-1"></i>Pending
                                                </span>
                                            @elseif($claim->status === 'approved')
                                                <span class="badge bg-soft-success text-success fw-bold px-3 rounded-pill">
                                                    <i data-feather="check-circle" style="width:11px;" class="me-1"></i>Approved
                                                </span>
                                            @else
                                                <span class="badge bg-soft-danger text-danger fw-bold px-3 rounded-pill">
                                                    <i data-feather="x-circle" style="width:11px;" class="me-1"></i>Rejected
                                                </span>
                                            @endif
                                            @if($claim->admin_note)
                                                <div class="small text-muted mt-1 fst-italic">
                                                    "{{ Str::limit($claim->admin_note, 40) }}"
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Actions --}}
                                        <td class="text-center">
                                            @if($claim->status === 'pending')
                                                <div class="d-flex gap-1 justify-content-center">
                                                    {{-- Approve Button --}}
                                                    <button type="button"
                                                            class="btn btn-sm btn-success rounded-pill px-3 fw-semibold"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#approveModal"
                                                            data-claim-id="{{ $claim->id }}"
                                                            data-employee="{{ $claim->user->name }}"
                                                            data-shift="{{ $claim->openShift->shift_name }}"
                                                            data-date="{{ \Carbon\Carbon::parse($claim->openShift->date)->format('D, d M Y') }}">
                                                        <i data-feather="check" style="width:13px;" class="me-1"></i>Approve
                                                    </button>

                                                    {{-- Reject Button --}}
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal"
                                                            data-claim-id="{{ $claim->id }}"
                                                            data-employee="{{ $claim->user->name }}"
                                                            data-shift="{{ $claim->openShift->shift_name }}">
                                                        <i data-feather="x" style="width:13px;" class="me-1"></i>Reject
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-muted small">Processed</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Approve Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i data-feather="check-circle" class="me-2 text-success" style="width:20px;"></i>Approve Shift Claim
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted" id="approveText">
                        You are approving <strong id="approveEmployee"></strong>'s claim for
                        <strong id="approveShift"></strong> on <strong id="approveDate"></strong>.
                    </p>
                    <div class="p-3 rounded-3 mb-3" style="background:#f0fdf4;border:1px dashed #16a34a;">
                        <small class="text-success fw-semibold">
                            <i data-feather="info" style="width:13px;" class="me-1"></i>
                            This will automatically add the shift to the employee's weekly schedule.
                        </small>
                    </div>
                    <label class="form-label fw-bold small">Admin Note <span class="text-muted fw-normal">(optional)</span></label>
                    <textarea name="admin_note" class="form-control rounded-3" rows="2"
                              placeholder="e.g. Great fit for this shift!"></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                        <i data-feather="check" style="width:16px;" class="me-1"></i> Confirm Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i data-feather="x-circle" class="me-2 text-danger" style="width:20px;"></i>Reject Shift Claim
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">
                        Rejecting claim from <strong id="rejectEmployee"></strong> for
                        <strong id="rejectShift"></strong>.
                    </p>
                    <label class="form-label fw-bold small">Reason for Rejection <span class="text-muted fw-normal">(optional — shown to employee)</span></label>
                    <textarea name="admin_note" class="form-control rounded-3" rows="3"
                              placeholder="e.g. Shift already filled, or you're not assigned to this site."></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                        <i data-feather="x" style="width:16px;" class="me-1"></i> Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Approve Modal
    const approveModal = document.getElementById('approveModal');
    approveModal.addEventListener('show.bs.modal', function (event) {
        const btn      = event.relatedTarget;
        const claimId  = btn.getAttribute('data-claim-id');
        const employee = btn.getAttribute('data-employee');
        const shift    = btn.getAttribute('data-shift');
        const date     = btn.getAttribute('data-date');

        document.getElementById('approveEmployee').textContent = employee;
        document.getElementById('approveShift').textContent    = shift;
        document.getElementById('approveDate').textContent     = date;
        document.getElementById('approveForm').action = `/open-shifts/claims/${claimId}/approve`;
        feather.replace();
    });

    // Reject Modal
    const rejectModal = document.getElementById('rejectModal');
    rejectModal.addEventListener('show.bs.modal', function (event) {
        const btn      = event.relatedTarget;
        const claimId  = btn.getAttribute('data-claim-id');
        const employee = btn.getAttribute('data-employee');
        const shift    = btn.getAttribute('data-shift');

        document.getElementById('rejectEmployee').textContent = employee;
        document.getElementById('rejectShift').textContent    = shift;
        document.getElementById('rejectForm').action = `/open-shifts/claims/${claimId}/reject`;
        feather.replace();
    });

    feather.replace();
});
</script>
@endsection
