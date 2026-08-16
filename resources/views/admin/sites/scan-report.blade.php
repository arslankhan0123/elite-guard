@extends('dashboardLayouts.main')
@section('title', 'Site Scan Report')
@section('breadcrumbTitle', 'Site Scan Report')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('sites.index') }}">Sites</a></li>
<li class="breadcrumb-item active">Scan Report</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="card-title text-white mb-1">{{ $site->name }} - Checkpoint Scan Report</h4>
                    <small>{{ $site->company?->name ?? 'N/A' }}</small>
                </div>
                <span class="badge bg-light text-primary">{{ $scans->total() }} scans</span>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('sites.scan-report', $site->id) }}" class="row g-3 align-items-end mb-4">
                    <div class="col-md-2">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Start Time</label>
                        <input type="time" name="start_time" class="form-control" value="{{ $filters['start_time'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">End Time</label>
                        <input type="time" name="end_time" class="form-control" value="{{ $filters['end_time'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(($filters['user_id'] ?? '') == $user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Search</label>
                        <input type="search" name="search" class="form-control" placeholder="User, checkpoint or UID" value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="col-12 d-flex flex-wrap gap-2">
                        <button class="btn btn-info" type="submit"><i data-feather="filter" class="me-1"></i> Filter</button>
                        <a class="btn btn-light" href="{{ route('sites.scan-report', $site->id) }}">Today</a>
                        <a class="btn btn-danger ms-md-auto" target="_blank" href="{{ route('sites.scan-report.export', array_merge(['site_id' => $site->id, 'format' => 'pdf'], request()->only(['start_date', 'end_date', 'start_time', 'end_time', 'user_id', 'search']))) }}"><i data-feather="file-text" class="me-1"></i> View PDF Report</a>
                        <a class="btn btn-success" href="{{ route('sites.scan-report.export', array_merge(['site_id' => $site->id, 'format' => 'csv'], request()->only(['start_date', 'end_date', 'start_time', 'end_time', 'user_id', 'search']))) }}"><i data-feather="download" class="me-1"></i> Export CSV</a>
                    </div>
                </form>

                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr><th>#</th><th>Date</th><th>Time</th><th>User</th><th>Checkpoint / Tag</th><th>Tag UID</th><th>Evidence</th></tr>
                        </thead>
                        <tbody>
                            @forelse($scans as $scan)
                            <tr>
                                <td>{{ $scans->firstItem() + $loop->index }}</td>
                                <td>{{ $scan->date?->format('d/m/Y') }}</td>
                                <td>{{ $scan->time ? \Carbon\Carbon::parse($scan->time)->format('h:i:s A') : 'N/A' }}</td>
                                <td>{{ $scan->user?->name ?? 'N/A' }}</td>
                                <td><strong>{{ $scan->nfcTag?->name ?? 'N/A' }}</strong></td>
                                <td>{{ $scan->nfcTag?->uid ?? 'N/A' }}</td>
                                <td>
                                    @if($scan->image)
                                        <a href="{{ asset('storage/' . ltrim($scan->image, '/')) }}" target="_blank" class="btn btn-sm btn-outline-primary">View Image</a>
                                    @else
                                        <span class="text-muted">No image</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No checkpoint scans found for the selected filters.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">{{ $scans->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
