@extends('dashboardLayouts.main')
@section('title', 'Attendance Management')

@section('breadcrumbTitle', 'Attendance Listing')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Attendance</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title shine">Filters</h4>
            </div>
            <div class="card-body">
                <form id="filterForm" action="{{ route('attendance.index') }}" method="GET" class="row">
                    <div class="col-md-3 mb-3 d-flex flex-column">
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
                    <div class="col-md-3 mb-3 d-flex flex-column">
                        <label for="site_id" class="form-label fw-bold">Site</label>
                        <select name="site_id" id="site_id" class="form-select select2" onchange="this.form.submit()">
                            <option value="">All Sites</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                                    {{ $site->name }} ({{ $site->company->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 d-flex flex-column">
                        <label for="date_filter" class="form-label fw-bold">Date Range</label>
                        <select name="date_filter" id="date_filter" class="form-select" onchange="this.form.submit()">
                            <option value="">All Time</option>
                            <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="yesterday" {{ request('date_filter') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                            <option value="current_week" {{ request('date_filter') == 'current_week' ? 'selected' : '' }}>Current Week</option>
                            <option value="last_week" {{ request('date_filter') == 'last_week' ? 'selected' : '' }}>Last Week</option>
                            <option value="current_month" {{ request('date_filter') == 'current_month' ? 'selected' : '' }}>Current Month</option>
                            <option value="last_month" {{ request('date_filter') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                            <option value="current_year" {{ request('date_filter') == 'current_year' ? 'selected' : '' }}>Current Year</option>
                            <option value="last_year" {{ request('date_filter') == 'last_year' ? 'selected' : '' }}>Last Year</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 d-flex flex-column">
                        <label for="date_filter" class="form-label fw-bold">Status</label>
                        <select name="status" id="date_filter" class="form-select" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Clocked In (Active)</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Clocked Out (Completed)</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title shine">Attendance Table</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="custom-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Site (Company)</th>
                                <th>Shift Name</th>
                                <th>Clock In</th>
                                <th>Clock Out</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                            <tr>
                                <td>
                                    <strong>{{ $attendance->user->name }}</strong><br>
                                    <small class="text-muted">{{ $attendance->user->email }}</small>
                                </td>
                                <td>
                                    {{ $attendance->shift->site->name ?? 'N/A' }}<br>
                                    <small class="text-muted">({{ $attendance->shift->site->company->name ?? 'N/A' }})</small>
                                </td>
                                <td>{{ $attendance->shift->shift_name ?? 'N/A' }}</td>
                                <td>
                                    {{ $attendance->clock_in_at ? $attendance->clock_in_at->format('Y-m-d H:i') : 'N/A' }}
                                    @if($attendance->clock_in_latitude)
                                        <br><small class="text-info"><i class="mdi mdi-map-marker"></i> Lat: {{ round($attendance->clock_in_latitude, 4) }}, Lng: {{ round($attendance->clock_in_longitude, 4) }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $attendance->clock_out_at ? $attendance->clock_out_at->format('Y-m-d H:i') : '-' }}
                                    @if($attendance->clock_out_latitude)
                                        <br><small class="text-info"><i class="mdi mdi-map-marker"></i> Lat: {{ round($attendance->clock_out_latitude, 4) }}, Lng: {{ round($attendance->clock_out_longitude, 4) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->clock_in_at && $attendance->clock_out_at)
                                        @php
                                            $diff = $attendance->clock_in_at->diff($attendance->clock_out_at);
                                            echo $diff->format('%hh %im');
                                        @endphp
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->status == 'active')
                                        <span class="badge bg-success">Clocked In</span>
                                    @else
                                        <span class="badge bg-secondary">Completed</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No attendance records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto-submit form when Select2 values change
        $('.select2').on('change', function() {
            $('#filterForm').submit();
        });
    });
</script>
@endsection
