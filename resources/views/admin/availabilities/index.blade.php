@extends('dashboardLayouts.main')
@section('title', 'Employee Availability')

@section('breadcrumbTitle', 'Employee Availability')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Availability</li>
@endsection

@section('content')
<div class="row g-4">

    {{-- Header --}}
    <div class="col-12">
        <div class="card border-0 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
            <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold text-white mb-1">
                        <i data-feather="calendar" class="me-2" style="width:22px;"></i> Employee Availability
                    </h4>
                    <p class="text-white mb-0" style="opacity:.75;">Review when your team is available to work</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-warning rounded-pill px-3 py-2 fw-bold">
                        {{ $pendingCount }} Pending Submissions
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="col-12">
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body p-3 d-flex gap-2 flex-wrap">
                @foreach(['all' => 'All Submissions', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $val => $label)
                    <a href="{{ route('availabilities.index', ['status' => $val]) }}"
                       class="btn rounded-pill px-4 fw-semibold {{ $statusFilter === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="col-12">
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body p-4">
                @if($availabilities->isEmpty())
                    <div class="text-center py-5">
                        <div style="font-size:4rem;">📅</div>
                        <h5 class="fw-bold text-muted mt-3">No availability records found</h5>
                        <p class="text-muted">Change the filters or wait for employees to submit their availability.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table id="custom-table" class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Date</th>
                                    <th>Shift</th>
                                    <th>Employee Notes</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availabilities as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center" style="width:35px;height:35px;">
                                                    <i data-feather="user" style="width:14px;"></i>
                                                </div>
                                                <div class="fw-bold">{{ $item->user->name }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-info text-info rounded-pill px-3">
                                                {{ \Carbon\Carbon::parse($item->date)->format('D, d M Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $item->shift }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ Str::limit($item->user_notes, 30) ?: '—' }}</small>
                                        </td>
                                        <td>
                                            @if($item->status === 'pending')
                                                <span class="badge bg-soft-warning text-warning fw-bold px-3 rounded-pill">Pending</span>
                                            @elseif($item->status === 'approved')
                                                <span class="badge bg-soft-success text-success fw-bold px-3 rounded-pill">Approved</span>
                                            @else
                                                <span class="badge bg-soft-danger text-danger fw-bold px-3 rounded-pill">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 justify-content-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                                        data-bs-toggle="modal" data-bs-target="#processModal"
                                                        data-id="{{ $item->id }}"
                                                        data-employee="{{ $item->user->name }}"
                                                        data-date="{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}"
                                                        data-shift="{{ $item->shift }}"
                                                        data-notes="{{ $item->user_notes }}"
                                                        data-admin-notes="{{ $item->admin_notes }}"
                                                        data-status="{{ $item->status }}">
                                                    Review
                                                </button>
                                                <form action="{{ route('availabilities.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this record?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                        <i data-feather="trash-2" style="width:13px;"></i>
                                                    </button>
                                                </form>
                                            </div>
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

{{-- Review Modal --}}
<div class="modal fade" id="processModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Review Availability</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="processForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="bg-light p-3 rounded-4 mb-3 small">
                        <div class="row g-2">
                            <div class="col-6"><strong>Employee:</strong> <span id="m-employee"></span></div>
                            <div class="col-6"><strong>Date:</strong> <span id="m-date"></span></div>
                            <div class="col-6"><strong>Shift:</strong> <span id="m-shift"></span></div>
                            <div class="col-12 mt-2"><strong>Employee Notes:</strong> <p class="mb-0 text-muted" id="m-notes"></p></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Change Status</label>
                        <select name="status" id="m-status" class="form-select rounded-3">
                            <option value="pending">Pending</option>
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold small">Admin Notes</label>
                        <textarea name="admin_notes" id="m-admin-notes" class="form-control rounded-3" rows="3" placeholder="Notes for the employee..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const processModal = document.getElementById('processModal');
    processModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        const id = btn.getAttribute('data-id');
        
        document.getElementById('m-employee').textContent = btn.getAttribute('data-employee');
        document.getElementById('m-date').textContent = btn.getAttribute('data-date');
        document.getElementById('m-shift').textContent = btn.getAttribute('data-shift');
        document.getElementById('m-notes').textContent = btn.getAttribute('data-notes') || 'No notes provided';
        document.getElementById('m-admin-notes').value = btn.getAttribute('data-admin-notes') || '';
        document.getElementById('m-status').value = btn.getAttribute('data-status');
        
        document.getElementById('processForm').action = `/availabilities/${id}`;
    });
});
</script>
@endsection
