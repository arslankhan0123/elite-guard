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
            <p class="text-muted mb-0">{{ $runSheet->week_start_date->format('d M Y') }} – {{ $runSheet->week_start_date->copy()->endOfWeek()->format('d M Y') }}</p>
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
@php $entries = $runSheet->entries->where('day_of_week', $dayNumber); @endphp
<div class="card border-0 shadow-sm mb-3" style="border-left:4px solid #6f42c1 !important">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">{{ $dayName }}</h5>
        <span class="badge bg-primary">{{ $entries->count() }} {{ \Illuminate\Support\Str::plural('tour', $entries->count()) }}</span>
    </div>
    <div class="card-body p-0">
        @if($entries->isEmpty())
            <div class="text-center text-muted py-4">No tours scheduled.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th style="width:70px">#</th><th>Tour Name</th><th>Site</th><th>Start Time</th><th>End Time</th><th>Duration</th></tr></thead>
                    <tbody>
                    @foreach($entries as $entry)
                        @php
                            $start = \Carbon\Carbon::parse($entry->start_time);
                            $end = \Carbon\Carbon::parse($entry->end_time);
                            if ($end->lt($start)) $end->addDay();
                        @endphp
                        <tr>
                            <td><span class="badge bg-primary">{{ $loop->iteration }}</span></td>
                            <td class="fw-semibold">{{ $entry->tour_name }}</td>
                            <td>{{ $entry->site?->name ?? 'N/A' }}</td>
                            <td>{{ $start->format('h:i A') }}</td>
                            <td>{{ $end->format('h:i A') }}</td>
                            <td>{{ $start->diff($end)->format('%hh %im') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endforeach

<style>
    .runsheet-actions { min-width: max-content; }
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
