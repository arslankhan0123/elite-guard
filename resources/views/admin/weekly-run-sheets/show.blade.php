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
        <div>
            <a href="{{ route('weekly-run-sheets.index') }}" class="btn btn-light">Back</a>
            <a href="{{ route('weekly-run-sheets.edit', $runSheet) }}" class="btn btn-primary">Edit Runsheet</a>
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
@endsection
