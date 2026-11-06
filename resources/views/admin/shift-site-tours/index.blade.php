@extends('dashboardLayouts.main')
@section('title', 'Daily Tours')

@section('breadcrumbTitle', 'Daily Tours')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Daily Tours</li>
@endsection

@section('content')
@php
    $prevDate = \Carbon\Carbon::parse($selectedDate)->copy()->subDay()->toDateString();
    $nextDate = \Carbon\Carbon::parse($selectedDate)->copy()->addDay()->toDateString();
    $curDate  = \Carbon\Carbon::parse($selectedDate)->toDateString();
    $dispDate = \Carbon\Carbon::parse($selectedDate)->format('d M Y');
@endphp

{{-- Top bar: title + shared date navigator --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div class="text-muted small">Showing shifts for <span class="fw-semibold text-dark">{{ $dispDate }}</span></div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="d-flex align-items-center gap-1 rounded-pill border px-2 py-1" style="background:#f8f9fa;">
            <a href="{{ route('daily-tours.index', ['date' => $prevDate]) }}" class="btn btn-sm btn-link p-0 text-dark d-flex" title="Previous Day">
                <i data-feather="chevron-left" style="width:16px;height:16px;"></i>
            </a>
            <input type="date" id="daily-tours-date-picker" value="{{ $curDate }}"
                class="form-control form-control-sm border-0 bg-transparent p-0 fw-semibold text-dark"
                style="width:140px;text-align:center;box-shadow:none;">
            <a href="{{ route('daily-tours.index', ['date' => $nextDate]) }}" class="btn btn-sm btn-link p-0 text-dark d-flex" title="Next Day">
                <i data-feather="chevron-right" style="width:16px;height:16px;"></i>
            </a>
        </div>
        <a href="{{ route('daily-tours.index') }}" class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-bold text-decoration-none" style="font-size: 0.75rem;" title="Jump to today">
            Today
        </a>
    </div>
</div>

@php
    // Reusable renderer for one row (shared by both columns)
    $renderRow = function ($shift, $typeLabel) {
        $scanned = $shift['scanned'];
        $total   = $shift['total'];
        $percent = $shift['percent'];

        $ringColor = '#0d6efd';
        if ($percent >= 100)      $ringColor = '#198754';
        elseif ($percent >= 50)   $ringColor = '#0dcaf0';
        elseif ($percent > 0)     $ringColor = '#fd7e14';

        $st = $shift['status'];
        if ($st === 'Completed') {
            $statusBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 fw-semibold">Completed</span>';
        } elseif ($st === 'In Progress') {
            $statusBadge = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1 fw-semibold">In Progress</span>';
        } elseif ($st === 'Not Started') {
            $statusBadge = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 fw-semibold">Not Started</span>';
        } else {
            $statusBadge = '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1 fw-semibold">No Items</span>';
        }

        $overnightBadge = !empty($shift['is_overnight'])
            ? '<span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0 mt-1 ms-1" style="font-size:0.68rem;">Overnight</span>'
            : '';
        $endsHint = !empty($shift['is_overnight'])
            ? '<div class="text-info" style="font-size:0.7rem;">ends ' . e($shift['end_date']) . '</div>'
            : '';

        $deg = $percent * 3.6;

        return '
        <tr>
            <td>
                <div class="fw-semibold text-dark">' . e($shift['shift_name']) . '</div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0 mt-1" style="font-size:0.68rem;">' . e($typeLabel) . '</span>
                ' . $overnightBadge . '
            </td>
            <td class="text-secondary"><i data-feather="map-pin" style="width:13px;height:13px;" class="text-danger me-1"></i>' . e($shift['site_name']) . '</td>
            <td class="text-secondary"><i data-feather="user" style="width:13px;height:13px;" class="text-muted me-1"></i>' . e($shift['guard_name']) . '</td>
            <td class="text-muted small"><i data-feather="clock" style="width:13px;height:13px;" class="text-muted me-1"></i>' . e($shift['time_range']) . $endsHint . '</td>
            <td class="text-center">
                <div style="width:56px;height:56px;border-radius:50%;background:conic-gradient(' . $ringColor . ' ' . $deg . 'deg, #e9ecef ' . $deg . 'deg);display:inline-flex;align-items:center;justify-content:center;margin:auto;">
                    <div style="width:40px;height:40px;border-radius:50%;background:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                        <span style="font-size:0.6rem;font-weight:700;color:#333;line-height:1;">' . $scanned . '/' . $total . '</span>
                        <span style="font-size:0.5rem;color:#888;line-height:1;">' . $percent . '%</span>
                    </div>
                </div>
            </td>
            <td class="text-center">' . $statusBadge . '</td>
        </tr>';
    };
@endphp

<div class="row g-4">
    {{-- ───────── Site Tours ───────── --}}
    <div class="col-xl-6 col-12">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold text-dark mb-0">
                    <i data-feather="map-pin" class="text-primary me-2"></i> Daily Shift Site Tours
                </h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Shift</th><th>Site</th><th>Guard</th><th>Hours</th>
                                <th class="text-center">Tour Progress</th><th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyShiftTours as $shift)
                                {!! $renderRow($shift, 'Site Tour') !!}
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No site tour shifts found for this date.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ───────── Runsheets ───────── --}}
    <div class="col-xl-6 col-12">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold text-dark mb-0">
                    <i data-feather="list" class="text-primary me-2"></i> Daily Shift Runsheets
                </h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Shift</th><th>Site</th><th>Guard</th><th>Hours</th>
                                <th class="text-center">Progress</th><th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyShiftRunsheets as $shift)
                                {!! $renderRow($shift, 'Runsheet') !!}
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No runsheet shifts found for this date.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var picker = document.getElementById('daily-tours-date-picker');
        if (picker) {
            picker.addEventListener('change', function () {
                if (this.value) {
                    var url = new URL(window.location.href);
                    url.searchParams.set('date', this.value);
                    window.location.href = url.toString();
                }
            });
        }
        if (window.feather) { try { feather.replace(); } catch (e) {} }
    });
</script>
@endsection
