@extends('dashboardLayouts.main')
@section('title', 'Daily Vehicle Checklist Listing')

@section('breadcrumbTitle', 'Daily Vehicle Checklist Listing')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Daily Vehicle Checklist</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title shine">Filters</h4>
            </div>
            <div class="card-body">
                <form id="filterForm" action="{{ route('forms.daily-vehicle-checklist') }}" method="GET" class="row">
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
                    <div class="col-md-4 mb-3 d-flex flex-column">
                        <label for="document_status" class="form-label fw-bold">Document Status</label>
                        <select name="document_status" id="document_status" class="form-select" onchange="this.form.submit()">
                            <option value="">Both</option>
                            <option value="uploaded" {{ request('document_status') == 'uploaded' ? 'selected' : '' }}>Document Uploaded</option>
                            <option value="not_uploaded" {{ request('document_status') == 'not_uploaded' ? 'selected' : '' }}>Document Not Uploaded</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title shine">Vehicle Checklist Table ({{ $checklists->count() }})</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="custom-table" class="table table-striped table-bordered">
                        <thead>
                            <tr class="table-dark">
                                <th>ID</th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Vehicle</th>
                                <th>Odometer</th>
                                <th>Fuel</th>
                                <th>Site</th>
                                <th>Driver</th>
                                <th>Document</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($checklists as $checklist)
                            <tr>
                                <td>{{ $checklist->id }}</td>
                                <td>
                                    <strong>{{ $checklist->user->name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $checklist->user->email ?? '' }}</small>
                                </td>
                                <td>{{ $checklist->date }}</td>
                                <td>{{ $checklist->time }}</td>
                                <td>{{ $checklist->vehicle }}</td>
                                <td>{{ $checklist->odometer_reading }}</td>
                                <td>{{ $checklist->fuel }}</td>
                                <td>{{ $checklist->assigned_site }}</td>
                                <td>{{ $checklist->driver }}</td>
                                <td>
                                    @if($checklist->documents)
                                    <a href="{{ $checklist->documents }}" target="_blank" class="btn btn-sm btn-primary">View File</a>
                                    @else
                                    <span class="text-muted">No File</span>
                                    @endif
                                </td>
                                <td>{{ $checklist->created_at->format('j M Y H:i') }}</td>
                            </tr>
                            @endforeach
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
        $('.select2').on('change', function() {
            $('#filterForm').submit();
        });
    });
</script>
@endsection
