@extends('dashboardLayouts.main')
@section('title', 'Orientation Attempts')

@section('breadcrumbTitle', 'Orientation Attempts')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('orientations.index') }}">Orientations</a></li>
    <li class="breadcrumb-item active">Attempts</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0">Attempts: {{ $orientation->type }}</h4>
                <a href="{{ route('orientations.index') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="mdi mdi-arrow-left"></i> Back to Orientations
                </a>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="custom-table" class="table table-striped table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Attempt ID</th>
                                    <th>User Name</th>
                                    <th>Email</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attempts as $attempt)
                                    <tr>
                                        <td>{{ $attempt->id }}</td>
                                        <td>{{ $attempt->user->name ?? 'Unknown User' }}</td>
                                        <td>{{ $attempt->user->email ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $attempt->score >= $orientation->passing_percentage ? 'bg-success' : 'bg-warning text-dark' }}">
                                                {{ round($attempt->score, 2) }}%
                                            </span>
                                        </td>
                                        <td>
                                            @if($attempt->is_passed)
                                                <span class="badge bg-success rounded-pill">Passed</span>
                                            @else
                                                <span class="badge bg-danger rounded-pill">Failed</span>
                                            @endif
                                        </td>
                                        <td>{{ $attempt->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('orientations.showAttempt', ['id' => $orientation->id, 'attempt_id' => $attempt->id]) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View Full Result">
                                                <i class="mdi mdi-eye"></i> View Result
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($attempts->isEmpty())
                            <p class="text-muted mt-3 mb-0">No users have attempted this orientation yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
