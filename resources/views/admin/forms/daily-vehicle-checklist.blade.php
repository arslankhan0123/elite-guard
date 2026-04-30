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
            <div class="card-header justify-content-between d-flex align-items-center">
                <h4 class="card-title shine">Vehicle Checklist Table</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="custom-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
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
                                <td>{{ $checklist->user->name ?? 'N/A' }}</td>
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
                                <td>{{ $checklist->created_at->format('Y-m-d H:i') }}</td>
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
