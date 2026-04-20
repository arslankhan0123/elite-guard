@extends('dashboardLayouts.main')
@section('title', 'PaySlips')

@section('breadcrumbTitle', 'PaySlips Management')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">PaySlips</li>
@endsection

@section('content')

<style>
    .payslip-card {
        border: none;
        border-radius: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .payslip-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1) !important;
    }
    .payslip-card .card-header-accent {
        height: 5px;
    }
    .payslip-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .filter-card {
        border: none;
        border-radius: 16px;
        background: #f8faff;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
    }
    .badge-filetype {
        font-size: 0.7rem;
        letter-spacing: 0.05em;
        padding: 0.3em 0.65em;
        border-radius: 50px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .empty-state-icon {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
    }
    .btn-download {
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.82rem;
        padding: 0.45rem 1.1rem;
        transition: all 0.2s;
    }
    .btn-download:hover {
        transform: scale(1.04);
    }
</style>

<div class="row g-4">
    {{-- Page Header --}}
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-bold mb-1">PaySlips Management</h4>
                <p class="text-muted mb-0">
                    {{ $paySlips->count() }} pay slip{{ $paySlips->count() !== 1 ? 's' : '' }} found
                </p>
            </div>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                <i data-feather="users" class="me-1" style="width:16px;"></i> Manage Employees
            </a>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="col-12">
        <div class="filter-card p-4">
            <form method="GET" action="{{ route('pay-slips.index') }}" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted mb-1">
                            <i data-feather="user" style="width:13px;"></i> Filter by Employee
                        </label>
                        <select name="user_id" class="form-select rounded-3" onchange="document.getElementById('filterForm').submit()">
                            <option value="">— All Employees —</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted mb-1">
                            <i data-feather="calendar" style="width:13px;"></i> Filter by Month
                        </label>
                        <select name="month" class="form-select rounded-3" onchange="document.getElementById('filterForm').submit()">
                            <option value="">— All Months —</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted mb-1">
                            <i data-feather="clock" style="width:13px;"></i> Filter by Year
                        </label>
                        <select name="year" class="form-select rounded-3" onchange="document.getElementById('filterForm').submit()">
                            <option value="">— All Years —</option>
                            @for($y = date('Y') + 1; $y >= date('Y') - 3; $y--)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if(request()->hasAny(['user_id', 'month', 'year']))
                            <a href="{{ route('pay-slips.index') }}" class="btn btn-outline-danger rounded-pill w-100 fw-semibold">
                                <i data-feather="x" style="width:14px;" class="me-1"></i> Reset
                            </a>
                        @else
                            <button type="submit" class="btn btn-primary rounded-pill w-100 fw-semibold">
                                <i data-feather="filter" style="width:14px;" class="me-1"></i> Apply
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Results Grid --}}
    @if($paySlips->isEmpty())
        <div class="col-12">
            <div class="text-center py-5 my-3">
                <div class="empty-state-icon">
                    <i data-feather="file-text" style="width:38px; height:38px; color:#38bdf8;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">No Pay Slips Found</h5>
                <p class="text-muted mb-4" style="max-width: 380px; margin: 0 auto;">
                    No pay slips match your current filters. Try adjusting or
                    <a href="{{ route('pay-slips.index') }}" class="text-primary fw-semibold">reset the filters</a>.
                </p>
                <a href="{{ route('employees.index') }}" class="btn btn-primary rounded-pill px-5 fw-bold">
                    <i data-feather="upload" class="me-1" style="width:15px;"></i> Upload a Pay Slip
                </a>
            </div>
        </div>
    @else
        @foreach($paySlips as $slip)
            @php
                $ext = strtolower(pathinfo($slip->file_path, PATHINFO_EXTENSION));
                $monthName = date('F', mktime(0, 0, 0, $slip->month, 1));
                $initials = collect(explode(' ', $slip->user->name ?? 'U N'))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                $colors = ['#7c3aed', '#0891b2', '#059669', '#dc2626', '#d97706', '#2563eb'];
                $color = $colors[$slip->user_id % count($colors)];

                $badgeClass = match($ext) {
                    'pdf'          => 'bg-danger bg-opacity-10 text-danger',
                    'doc', 'docx'  => 'bg-primary bg-opacity-10 text-primary',
                    default        => 'bg-success bg-opacity-10 text-success',
                };
                $iconName = match($ext) {
                    'pdf'          => 'file-text',
                    'doc', 'docx'  => 'file',
                    default        => 'image',
                };
            @endphp
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <div class="payslip-card card h-100 shadow-sm">
                    {{-- Accent bar --}}
                    <div class="card-header-accent" style="background: {{ $color }};"></div>

                    <div class="card-body p-4">
                        {{-- Employee Info --}}
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="payslip-avatar text-white flex-shrink-0" style="background: {{ $color }};">
                                {{ $initials }}
                            </div>
                            <div class="overflow-hidden">
                                <p class="fw-bold text-dark mb-0 text-truncate">{{ $slip->user->name ?? 'Unknown' }}</p>
                                <small class="text-muted">Employee</small>
                            </div>
                        </div>

                        <hr class="my-2" style="border-color: #f0f0f0;">

                        {{-- Pay Period --}}
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">Pay Period</span>
                            <span class="fw-bold text-dark">{{ $monthName }} {{ $slip->year }}</span>
                        </div>

                        {{-- File Type --}}
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">File Type</span>
                            <span class="badge badge-filetype {{ $badgeClass }}">
                                <i data-feather="{{ $iconName }}" style="width:10px; height:10px;" class="me-1"></i>{{ strtoupper($ext) }}
                            </span>
                        </div>

                        {{-- Uploaded --}}
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted small fw-semibold">Uploaded</span>
                            <span class="small text-dark">{{ $slip->created_at->format('d M, Y') }}</span>
                        </div>

                        {{-- Download --}}
                        <a href="{{ $slip->file_path }}" target="_blank" rel="noopener noreferrer"
                            class="btn btn-download btn-primary w-100 mt-auto">
                            <i data-feather="download" style="width:14px; height:14px;" class="me-1"></i> Download
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        feather.replace();
    });
</script>

@endsection