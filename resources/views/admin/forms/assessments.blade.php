@extends('dashboardLayouts.main')
@section('title', 'Assessments Listing')

@section('breadcrumbTitle', 'Assessments Listing')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Assessments</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header justify-content-between d-flex align-items-center">
                <h4 class="card-title shine">Assessments Table</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="custom-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Shift Date</th>
                                <th>Location</th>
                                <th>Fit for Duty?</th>
                                <th>Believe Fit?</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assessments as $assessment)
                            <tr>
                                <td>{{ $assessment->id }}</td>
                                <td>{{ $assessment->user->name ?? 'N/A' }}</td>
                                <td>{{ $assessment->first_name }} {{ $assessment->last_name }}</td>
                                <td>{{ $assessment->worker_email }}</td>
                                <td>{{ $assessment->shift_date }}</td>
                                <td>{{ $assessment->location }}</td>
                                <td>
                                    @if($assessment->compliance_fit_for_duty)
                                    <span class="badge bg-success">Yes</span>
                                    @else
                                    <span class="badge bg-danger">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($assessment->believe_fit_for_duty)
                                    <span class="badge bg-success">Yes</span>
                                    @else
                                    <span class="badge bg-danger">No</span>
                                    @endif
                                </td>
                                <td>{{ $assessment->created_at->format('Y-m-d H:i') }}</td>
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
