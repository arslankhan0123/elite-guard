@extends('dashboardLayouts.main')
@section('title', 'Signed Orientations')

@section('breadcrumbTitle', 'Signed Orientations')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('orientations.index') }}">Orientations</a></li>
    <li class="breadcrumb-item active">Signed</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header justify-content-between d-flex align-items-center">
                    <h4 class="card-title shine">Signed Orientation Table</h4>
                    <a href="{{ route('orientations.index') }}" class="btn btn-sm btn-secondary-subtle">
                        <i class="mdi mdi-arrow-left align-middle"></i> Back to Listing
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="custom-table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Orientation Type</th>
                                    <th>Agreed</th>
                                    <th>Signed Date</th>
                                    <th>Signed Document</th>
                                    <th>Digital Signature</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($signedOrientations as $signed)
                                    <tr>
                                        <td>{{ $signed->id }}</td>
                                        <td>
                                            @if($signed->user)
                                                <strong>{{ $signed->user->name }}</strong><br>
                                                <small class="text-muted">{{ $signed->user->email }}</small>
                                            @else
                                                <span class="text-danger">User Deleted</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($signed->orientation)
                                                {{ $signed->orientation->type }}
                                            @else
                                                <span class="text-danger">Orientation Deleted</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(strtolower($signed->agreed) == 'yes')
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ ucfirst($signed->agreed) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $signed->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @if($signed->document)
                                                <a href="{{ $signed->document }}" target="_blank"
                                                    class="btn btn-sm btn-soft-primary">
                                                    <i class="mdi mdi-file-document-edit-outline"></i> View Document
                                                </a>
                                            @else
                                                <span class="text-muted">No Document</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($signed->signature)
                                                @if(strpos($signed->signature, 'data:image') === 0)
                                                    <img src="{{ $signed->signature }}" alt="Signature" style="max-height: 50px; border: 1px solid #ddd; padding: 2px;">
                                                @else
                                                    <span class="text-info">{{ $signed->signature }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">No Signature</span>
                                            @endif
                                        </td>
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
