@extends('dashboardLayouts.main')
@section('title', 'Reports & Forms Listing')

@section('breadcrumbTitle', 'Reports & Forms Listing')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Reports & Forms</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <!-- Selection Card -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Select Report or Form</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Security Reports</label>
                        <select class="form-select select2" onchange="window.location.href='{{ route('reports.all') }}?type=' + this.value">
                            <option value="">Choose a Security Report...</option>
                            <option value="general" {{ $type == 'general' ? 'selected' : '' }}>General Report</option>
                            <option value="incident" {{ $type == 'incident' ? 'selected' : '' }}>Incident Report</option>
                            <option value="disciplinary" {{ $type == 'disciplinary' ? 'selected' : '' }}>Disciplinary Form</option>
                            <option value="daily-shift" {{ $type == 'daily-shift' ? 'selected' : '' }}>Daily Shift Report</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Forms</label>
                        <select class="form-select select2" onchange="window.location.href='{{ route('reports.all') }}?type=' + this.value">
                            <option value="">Choose a Form...</option>
                            <option value="assessments" {{ $type == 'assessments' ? 'selected' : '' }}>Assessment</option>
                            <option value="vehicle-checklist" {{ $type == 'vehicle-checklist' ? 'selected' : '' }}>Daily Vehicle Checklist</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        @if($type)
            <!-- Filters Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Filters ({{ ucwords(str_replace('-', ' ', $type)) }})</h4>
                </div>
                <div class="card-body">
                    <form id="filterForm" action="{{ route('reports.all') }}" method="GET" class="row">
                        <input type="hidden" name="type" value="{{ $type }}">
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
                        @if($type == 'vehicle-checklist')
                            <div class="col-md-4 mb-3 d-flex flex-column">
                                <label for="document_status" class="form-label fw-bold">Document Status</label>
                                <select name="document_status" id="document_status" class="form-select" onchange="this.form.submit()">
                                    <option value="">All</option>
                                    <option value="uploaded" {{ request('document_status') == 'uploaded' ? 'selected' : '' }}>Uploaded</option>
                                    <option value="not_uploaded" {{ request('document_status') == 'not_uploaded' ? 'selected' : '' }}>Not Uploaded</option>
                                </select>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Data Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ ucwords(str_replace('-', ' ', $type)) }} Data</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if($type == 'general')
                            @include('admin.unified-reports.tables.general')
                        @elseif($type == 'incident')
                            @include('admin.unified-reports.tables.incident')
                        @elseif($type == 'disciplinary')
                            @include('admin.unified-reports.tables.disciplinary')
                        @elseif($type == 'daily-shift')
                            @include('admin.unified-reports.tables.daily-shift')
                        @elseif($type == 'assessments')
                            @include('admin.unified-reports.tables.assessments')
                        @elseif($type == 'vehicle-checklist')
                            @include('admin.unified-reports.tables.vehicle-checklist')
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
