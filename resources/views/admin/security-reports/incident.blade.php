@extends('dashboardLayouts.main')
@section('title', 'Incident Reports Listing')

@section('breadcrumbTitle', 'Incident Reports Listing')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Incident Reports</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title shine">Filters</h4>
            </div>
            <div class="card-body">
                <form id="filterForm" action="{{ route('security-reports.incident') }}" method="GET" class="row">
                    <div class="col-md-4 mb-3 d-flex flex-column">
                        <label for="user_id" class="form-label fw-bold">Employee</label>
                        <select name="user_id" id="user_id" class="form-select select2" onchange="this.form.submit()">
                            <option value="">All Employees</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3 d-flex flex-column">
                        <label for="date_range" class="form-label fw-bold">Date Range</label>
                        <select name="date_range" id="date_range" class="form-select" onchange="this.form.submit()">
                            <option value="">All Time</option>
                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                            <option value="current_week" {{ request('date_range') == 'current_week' ? 'selected' : '' }}>Current Week</option>
                            <option value="last_week" {{ request('date_range') == 'last_week' ? 'selected' : '' }}>Last Week</option>
                            <option value="current_month" {{ request('date_range') == 'current_month' ? 'selected' : '' }}>Current Month</option>
                            <option value="last_month" {{ request('date_range') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                            <option value="current_year" {{ request('date_range') == 'current_year' ? 'selected' : '' }}>Current Year</option>
                            <option value="last_year" {{ request('date_range') == 'last_year' ? 'selected' : '' }}>Last Year</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title shine">Incident Reports Table ({{ $reports->total() }})</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="custom-table" class="table table-striped table-bordered">
                        <thead>
                            <tr class="table-dark">
                                <th>ID</th>
                                <th>Incident Type</th>
                                <th>Location</th>
                                <th>Property</th>
                                <th>Report Date/Time</th>
                                <th>Reported By</th>
                                <th>Images</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>
                                    <span class="badge bg-danger">{{ $report->incident_type }}</span>
                                </td>
                                <td>{{ $report->location }}</td>
                                <td>{{ $report->property }}</td>
                                <td>{{ \Carbon\Carbon::parse($report->date_of_report)->format('j M Y') }} {{ $report->time_of_report }}</td>
                                <td>{{ $report->reported_by }}</td>
                                <td>
                                    @if($report->images->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($report->images as $image)
                                                <a href="{{ $image->image_path }}" target="_blank">
                                                    <img src="{{ $image->image_path }}" alt="Incident" style="width: 40px; height: 40px; object-fit: cover;" class="rounded border">
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">No images</span>
                                    @endif
                                </td>
                                <td>{{ $report->created_at->format('j M Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No reports found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $reports->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
