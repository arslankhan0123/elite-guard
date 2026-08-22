@extends('dashboardLayouts.main')
@section('title', 'View Weekly Runsheet')
@section('breadcrumbTitle', 'View Weekly Runsheet')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('weekly-run-sheets.index') }}">Runsheets</a></li>
<li class="breadcrumb-item active">View</li>
@endsection

@section('content')
@php $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday']; @endphp
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <div>
            <h4 class="card-title mb-1">{{ $runSheet->name }}</h4>
            <p class="text-muted mb-0"><span class="badge bg-success-subtle text-success">Recurring Weekly Schedule</span></p>
        </div>
        <div class="runsheet-actions d-flex align-items-center flex-nowrap gap-2">
            <a class="runsheet-action-link" href="{{ route('weekly-run-sheets.index') }}" data-bs-toggle="tooltip" title="Back to Runsheets">
                <button class="backBtn" type="button" aria-label="Back to Runsheets">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.42-1.41L7.83 13H20v-2z"></path></svg>
                </button>
            </a>
            <a class="runsheet-action-link" href="{{ route('weekly-run-sheets.edit', $runSheet) }}" data-bs-toggle="tooltip" title="Edit Runsheet">
                <button class="editBtn" type="button">
                    <svg height="1em" viewBox="0 0 512 512"><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path></svg>
                </button>
            </a>
        </div>
    </div>
    @if($runSheet->notes)
    <div class="card-body border-top"><strong>Notes:</strong> {{ $runSheet->notes }}</div>
    @endif
</div>

@foreach($days as $dayNumber => $dayName)
@php 
    $entries = $runSheet->entries->where('day_of_week', $dayNumber); 
    $dayStartTime = $runSheet->getDayStartTime($dayNumber);
    $dayEndTime = $runSheet->getDayEndTime($dayNumber);
@endphp
<div class="card border-0 shadow-sm mb-3 runsheet-day-card">
    <div class="card-header runsheet-day-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0">{{ $dayName }}</h5>
            @if($dayStartTime && $dayEndTime)
                <span class="badge bg-white text-dark rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                    <i data-feather="clock" class="me-1 text-primary" style="width:12px; height:12px;"></i>
                    {{ \Carbon\Carbon::parse($dayStartTime)->format('h:i A') }} - {{ \Carbon\Carbon::parse($dayEndTime)->format('h:i A') }}
                </span>
            @endif
        </div>
        <span class="runsheet-tour-count">{{ $entries->count() }} {{ \Illuminate\Support\Str::plural('tour', $entries->count()) }}</span>
    </div>
    <div class="card-body p-0">
        @if($entries->isEmpty())
            <div class="text-center text-muted py-4">No tours scheduled.</div>
        @else
            <div class="row g-3 p-3 runsheet-tour-grid" data-day="{{ $dayNumber }}">
                @foreach($entries as $entry)
                        @php
                            $hasTimes = !empty($entry->start_time) && !empty($entry->end_time);
                            if ($hasTimes) {
                                $start = \Carbon\Carbon::parse($entry->start_time);
                                $end = \Carbon\Carbon::parse($entry->end_time);
                                if ($end->lt($start)) $end->addDay();
                            }
                        @endphp
                    <div class="col-xl-4 col-md-6{{ $loop->iteration > 6 ? ' runsheet-extra-tour' : '' }}">
                        <div class="runsheet-tour-card h-100">
                            <div class="runsheet-tour-card-head">
                                <span class="runsheet-tour-number">{{ $loop->iteration }}</span>
                                <strong>{{ $entry->tour_name }}</strong>
                            </div>
                            <div class="runsheet-tour-site"><i data-feather="map-pin"></i> {{ $entry->site?->name ?? 'N/A' }}</div>
                            <div class="runsheet-tour-times">
                                <div><span>Start</span><strong>{{ $hasTimes ? $start->format('h:i A') : 'N/A' }}</strong></div>
                                <div><span>End</span><strong>{{ $hasTimes ? $end->format('h:i A') : 'N/A' }}</strong></div>
                                <div><span>Duration</span><strong>{{ $hasTimes ? $start->diff($end)->format('%hh %im') : 'N/A' }}</strong></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($entries->count() > 6)
                <div class="text-center pb-3">
                    <button type="button" class="btn runsheet-toggle-tours" data-day="{{ $dayNumber }}" data-more="{{ $entries->count() - 6 }}">See More ({{ $entries->count() - 6 }})</button>
                </div>
            @endif
        @endif
    </div>
</div>
@endforeach

<style>
    .runsheet-actions { min-width: max-content; }
    .runsheet-day-card { border-left: 4px solid #7c3aed !important; border-radius: 10px; overflow: hidden; margin-bottom: 30px !important; }
    .runsheet-day-header { background: #1e1b4b !important; color: #fff; border-bottom: 0; }
    .runsheet-day-header h5 { color: #fff; font-weight: 700; letter-spacing: .01em; }
    .runsheet-tour-count { display: inline-flex; align-items: center; padding: 5px 10px; border-radius: 20px; background: #fff; color: #1e1b4b; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
    .runsheet-tour-card { padding: 14px; border: 1px solid #e5e7eb; border-radius: 9px; background: #fff; box-shadow: 0 3px 8px rgba(30,27,75,.06); transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
    .runsheet-tour-card:hover { transform: translateY(-2px); border-color: #c4b5fd; box-shadow: 0 7px 16px rgba(30,27,75,.12); }
    .runsheet-tour-card-head { display: flex; align-items: center; gap: 9px; color: #1e1b4b; font-size: 14px; }
    .runsheet-tour-number { flex: 0 0 25px; width: 25px; height: 25px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; background: #7c3aed; color: #fff; font-size: 11px; font-weight: 700; }
    .runsheet-tour-site { display: flex; align-items: center; gap: 5px; margin: 12px 0; color: #64748b; font-size: 12px; }
    .runsheet-tour-site svg { width: 14px; height: 14px; color: #7c3aed; }
    .runsheet-tour-times { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; padding-top: 10px; border-top: 1px solid #eef0f5; }
    .runsheet-tour-times div { min-width: 0; }
    .runsheet-tour-times span { display: block; margin-bottom: 3px; color: #94a3b8; font-size: 9px; font-weight: 700; text-transform: uppercase; }
    .runsheet-tour-times strong { display: block; color: #334155; font-size: 11px; white-space: nowrap; }
    .runsheet-extra-tour { display: none; }
    .runsheet-tour-grid.is-expanded .runsheet-extra-tour { display: block; }
    .runsheet-toggle-tours { padding: 6px 15px; border: 1px solid #ddd6fe; border-radius: 20px; background: #f5f3ff; color: #6d28d9; font-size: 11px; font-weight: 700; }
    .runsheet-toggle-tours:hover { background: #7c3aed; color: #fff; }
    .runsheet-action-link { display: inline-flex; align-items: center; justify-content: center; line-height: 1; }
    .backBtn {
        width: 1.875rem;
        height: 1.875rem;
        padding: 0;
        border: 0;
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        cursor: pointer;
        color: #fff;
        background: #475569;
        box-shadow: 0 5px 10px rgba(0, 0, 0, .12);
        transition: transform .25s ease, background-color .25s ease, box-shadow .25s ease;
    }
    .backBtn svg { width: 17px; height: 17px; fill: currentColor; transition: transform .3s ease; }
    .backBtn:hover { background: #334155; box-shadow: 0 6px 12px rgba(0, 0, 0, .24); transform: translateY(-1px); }
    .backBtn:hover svg { animation: runsheetBackArrow .65s ease-in-out infinite alternate; }
    .backBtn:active { transform: scale(.9); }
    @keyframes runsheetBackArrow {
        from { transform: translateX(2px); }
        to { transform: translateX(-3px); }
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.runsheet-toggle-tours').forEach(function (button) {
        button.addEventListener('click', function () {
            const grid = document.querySelector(`.runsheet-tour-grid[data-day="${button.dataset.day}"]`);
            const expanded = grid.classList.toggle('is-expanded');
            button.textContent = expanded ? 'See Less' : `See More (${button.dataset.more})`;
        });
    });
});
</script>
@endsection
