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

    // status badge helper
    $badge = function ($st) {
        $map = [
            'Completed'   => ['success','Completed'],
            'In Progress' => ['warning','In Progress'],
            'Not Started' => ['danger','Not Started'],
            'No Items'    => ['secondary','No Items'],
            'No Tags'     => ['secondary','No Tags'],
        ];
        [$c,$t] = $map[$st] ?? ['secondary', $st];
        return '<span class="badge bg-'.$c.'-subtle text-'.$c.' border border-'.$c.'-subtle rounded-pill px-2 py-1 fw-semibold" style="font-size:0.7rem;">'.$t.'</span>';
    };
    $ringColor = function ($p) {
        if ($p >= 100) return '#198754';
        if ($p >= 50)  return '#0dcaf0';
        if ($p > 0)    return '#fd7e14';
        return '#0d6efd';
    };
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
        <a href="{{ route('daily-tours.index') }}" class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-bold text-decoration-none" style="font-size:0.75rem;" title="Jump to today">Today</a>
    </div>
</div>

@php
    // one reusable renderer for a column of shifts (each shift expands to its tours)
    $renderColumn = function ($shifts, $typeLabel, $emptyMsg) use ($badge, $ringColor) {
        if ($shifts->isEmpty()) {
            return '<div class="text-center text-muted py-4">'.$emptyMsg.'</div>';
        }
        $html = '';
        foreach ($shifts as $sh) {
            $deg = ($sh['percent'] ?? 0) * 3.6;
            $rc  = $ringColor($sh['percent'] ?? 0);
            $overnight = !empty($sh['is_overnight'])
                ? '<span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0 ms-1" style="font-size:0.65rem;">Overnight</span>' : '';
            $ends = !empty($sh['is_overnight']) ? ' · <span class="text-info">ends '.e($sh['end_date']).'</span>' : '';

            $html .= '<details class="border rounded-3 mb-2" open>';
            $html .= '<summary class="d-flex align-items-center justify-content-between gap-2 px-3 py-2" style="cursor:pointer;list-style:none;">';
            $html .= '<div class="flex-grow-1">';
            $html .= '<div class="fw-semibold text-dark">'.e($sh['shift_name']).' <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0" style="font-size:0.65rem;">'.e($typeLabel).'</span>'.$overnight.'</div>';
            $html .= '<div class="text-muted" style="font-size:0.78rem;"><i data-feather="user" style="width:12px;height:12px;"></i> '.e($sh['guard_name']).' · <i data-feather="clock" style="width:12px;height:12px;"></i> '.e($sh['time_range']).$ends.'</div>';
            $html .= '</div>';
            $html .= '<div class="d-flex align-items-center gap-2">';
            $html .= '<div style="width:46px;height:46px;border-radius:50%;background:conic-gradient('.$rc.' '.$deg.'deg,#e9ecef '.$deg.'deg);display:inline-flex;align-items:center;justify-content:center;">'
                   . '<div style="width:34px;height:34px;border-radius:50%;background:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;">'
                   . '<span style="font-size:0.58rem;font-weight:700;color:#333;line-height:1;">'.$sh['scanned'].'/'.$sh['total'].'</span>'
                   . '<span style="font-size:0.5rem;color:#888;line-height:1;">'.$sh['percent'].'%</span></div></div>';
            $html .= $badge($sh['status']);
            $html .= '</div></summary>';

            // tours table
            $html .= '<div class="px-2 pb-2"><table class="table table-sm align-middle mb-0" style="font-size:0.8rem;"><thead class="table-light"><tr>'
                   . '<th style="width:34px;">#</th><th>Site</th><th>Time</th><th class="text-center">Tags</th><th class="text-center">Status</th></tr></thead><tbody>';
            $tours = $sh['tours'] ?? collect();
            if (count($tours) === 0) {
                $html .= '<tr><td colspan="5" class="text-center text-muted py-2">No tours generated yet.</td></tr>';
            } else {
                $i = 0;
                foreach ($tours as $t) {
                    $i++;
                    $seq = $t['sequence'] ?? $i;
                    $tagTxt = $t['scanned_tags'].'/'.$t['total_tags'];
                    $dateTag = !empty($t['date']) ? ' <span class="text-muted" style="font-size:0.68rem;">('.e($t['date']).')</span>' : '';
                    $html .= '<tr>';
                    $html .= '<td class="text-muted">'.e($seq).'</td>';
                    $html .= '<td><i data-feather="map-pin" style="width:12px;height:12px;" class="text-danger me-1"></i>'.e($t['site_name']).$dateTag.'</td>';
                    $html .= '<td class="text-muted" style="font-size:0.74rem;">'.e($t['time']).'</td>';
                    $html .= '<td class="text-center fw-semibold">'.$tagTxt.'</td>';
                    $html .= '<td class="text-center">'.$badge($t['status']).'</td>';
                    $html .= '</tr>';
                }
            }
            $html .= '</tbody></table></div></details>';
        }
        return $html;
    };
@endphp

<div class="row g-4">
    <div class="col-xl-6 col-12">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold text-dark mb-0"><i data-feather="map-pin" class="text-primary me-2"></i> Daily Shift Site Tours</h5>
            </div>
            <div class="card-body px-3 pb-4">
                {!! $renderColumn($dailyShiftTours, 'Site Tour', 'No site tour shifts found for this date.') !!}
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-12">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold text-dark mb-0"><i data-feather="list" class="text-primary me-2"></i> Daily Shift Runsheets</h5>
            </div>
            <div class="card-body px-3 pb-4">
                {!! $renderColumn($dailyShiftRunsheets, 'Runsheet', 'No runsheet shifts found for this date.') !!}
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
