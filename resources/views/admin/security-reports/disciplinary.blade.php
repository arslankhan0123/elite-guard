@extends('dashboardLayouts.main')
@section('title', 'Disciplinary Reports Listing')

@section('breadcrumbTitle', 'Disciplinary Reports Listing')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Disciplinary Reports</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title shine">Disciplinary Reports Table ({{ $reports->total() }})</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="custom-table" class="table table-striped table-bordered">
                        <thead>
                            <tr class="table-dark">
                                <th>ID</th>
                                <th>Guard Name</th>
                                <th>License #</th>
                                <th>Site/Property</th>
                                <th>Warning Date</th>
                                <th>Violation Type</th>
                                <th>Severity</th>
                                <th>Supervisor</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>
                                    <strong>{{ $report->employee_name }}</strong><br>
                                    <small class="text-muted">ID: {{ $report->employee_id_license }}</small>
                                </td>
                                <td>{{ $report->employee_id_license }}</td>
                                <td>{{ $report->site_property }}</td>
                                <td>{{ \Carbon\Carbon::parse($report->warning_date)->format('j M Y') }}</td>
                                <td>{{ $report->violation_type }}</td>
                                <td>
                                    <span class="badge @if($report->classification_severity == 'Major') bg-danger @else bg-warning @endif">
                                        {{ $report->classification_severity }}
                                    </span>
                                </td>
                                <td>{{ $report->supervisor }}</td>
                                <td>{{ $report->created_at->format('j M Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">No reports found.</td>
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
