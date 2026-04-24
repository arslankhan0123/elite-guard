@extends('dashboardLayouts.main')
@section('title', 'Open Shifts')

@section('breadcrumbTitle', 'Open Shifts')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Open Shifts</li>
@endsection

@section('content')
<div class="row g-4">

    {{-- Header Card --}}
    <div class="col-12">
        <div class="card border-0 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #1e1b4b 0%, #3b0764 100%);">
            <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold text-white mb-1">
                        <i data-feather="layers" class="me-2" style="width:22px;"></i> Open Shifts Board
                    </h4>
                    <p class="text-white mb-0" style="opacity:.75;">Post available shifts for employees to claim</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('open-shifts.claims') }}" class="btn btn-warning rounded-pill px-4 fw-bold position-relative">
                        <i data-feather="bell" class="me-1" style="width:16px;"></i> Claim Requests
                        @if($pendingClaimsCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $pendingClaimsCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('open-shifts.create') }}" class="btn btn-light rounded-pill px-4 fw-bold">
                        <i data-feather="plus" class="me-1" style="width:16px;"></i> Post New Shift
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    @php
        $totalOpen   = $openShifts->where('status','open')->count();
        $totalClosed = $openShifts->where('status','closed')->count();
        $totalClaims = $openShifts->sum(fn($s) => $s->claims->count());
    @endphp
    <div class="col-md-4">
        <div class="card border-0 rounded-4 shadow-sm text-center p-3">
            <div class="mb-2" style="font-size:2rem;">📋</div>
            <h3 class="fw-bold text-primary mb-0">{{ $totalOpen }}</h3>
            <p class="text-muted small mb-0">Open Shifts</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 rounded-4 shadow-sm text-center p-3">
            <div class="mb-2" style="font-size:2rem;">✅</div>
            <h3 class="fw-bold text-success mb-0">{{ $totalClosed }}</h3>
            <p class="text-muted small mb-0">Closed / Filled</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 rounded-4 shadow-sm text-center p-3">
            <div class="mb-2" style="font-size:2rem;">🙋</div>
            <h3 class="fw-bold text-warning mb-0">{{ $pendingClaimsCount }}</h3>
            <p class="text-muted small mb-0">Pending Claims</p>
        </div>
    </div>

    {{-- Shifts Table --}}
    <div class="col-12">
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body p-4">
                @if($openShifts->isEmpty())
                    <div class="text-center py-5">
                        <div style="font-size:4rem;">📭</div>
                        <h5 class="fw-bold text-muted mt-3">No open shifts posted yet</h5>
                        <p class="text-muted">Click "Post New Shift" to get started.</p>
                        <a href="{{ route('open-shifts.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i data-feather="plus" class="me-1" style="width:16px;"></i> Post First Shift
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table id="custom-table" class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Site</th>
                                    <th>Shift</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Slots</th>
                                    <th>Claims</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($openShifts as $shift)
                                    @php
                                        $approvedCount = $shift->claims->where('status','approved')->count();
                                        $pendingCount  = $shift->claims->where('status','pending')->count();
                                        $filled        = $approvedCount >= $shift->slots;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center"
                                                    style="width:38px;height:38px;flex-shrink:0;">
                                                    <i data-feather="map-pin" style="width:16px;"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $shift->site->name }}</div>
                                                    <small class="text-muted">{{ $shift->site->city ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="fw-semibold">{{ $shift->shift_name }}</span></td>
                                        <td>
                                            <span class="badge bg-soft-info text-info rounded-pill px-3">
                                                {{ \Carbon\Carbon::parse($shift->date)->format('D, d M Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <i data-feather="clock" style="width:13px;" class="text-muted me-1"></i>
                                            {{ substr($shift->start_time,0,5) }} – {{ substr($shift->end_time,0,5) }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <div class="progress flex-grow-1" style="height:6px;min-width:60px;border-radius:99px;">
                                                    <div class="progress-bar {{ $filled ? 'bg-success' : 'bg-primary' }}"
                                                        style="width:{{ $shift->slots > 0 ? ($approvedCount / $shift->slots) * 100 : 0 }}%">
                                                    </div>
                                                </div>
                                                <small class="text-muted fw-semibold">{{ $approvedCount }}/{{ $shift->slots }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 flex-wrap">
                                                @if($pendingCount > 0)
                                                    <span class="badge bg-soft-warning text-warning rounded-pill px-2">
                                                        {{ $pendingCount }} pending
                                                    </span>
                                                @endif
                                                @if($approvedCount > 0)
                                                    <span class="badge bg-soft-success text-success rounded-pill px-2">
                                                        {{ $approvedCount }} approved
                                                    </span>
                                                @endif
                                                @if($shift->claims->where('status','rejected')->count() > 0)
                                                    <span class="badge bg-soft-danger text-danger rounded-pill px-2">
                                                        {{ $shift->claims->where('status','rejected')->count() }} rejected
                                                    </span>
                                                @endif
                                                @if($shift->claims->isEmpty())
                                                    <span class="text-muted small">No claims yet</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($shift->status === 'open' && !$filled)
                                                <span class="badge bg-soft-success text-success fw-bold px-3 rounded-pill">
                                                    <i data-feather="unlock" style="width:11px;" class="me-1"></i>Open
                                                </span>
                                            @elseif($shift->status === 'closed' || $filled)
                                                <span class="badge bg-soft-secondary text-secondary fw-bold px-3 rounded-pill">
                                                    <i data-feather="lock" style="width:11px;" class="me-1"></i>Closed
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 justify-content-center">
                                                <a href="{{ route('open-shifts.edit', $shift->id) }}"
                                                   class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Edit">
                                                    <i data-feather="edit-2" style="width:13px;"></i>
                                                </a>
                                                <a href="{{ route('open-shifts.claims') }}?shift_id={{ $shift->id }}"
                                                   class="btn btn-sm btn-outline-warning rounded-pill px-3 position-relative" title="View Claims">
                                                    <i data-feather="users" style="width:13px;"></i>
                                                    @if($pendingCount > 0)
                                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem;">
                                                            {{ $pendingCount }}
                                                        </span>
                                                    @endif
                                                </a>
                                                <a href="{{ route('open-shifts.delete', $shift->id) }}"
                                                   class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Delete"
                                                   onclick="return confirm('Delete this open shift and all its claims?')">
                                                    <i data-feather="trash-2" style="width:13px;"></i>
                                                </a>
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
@endsection
