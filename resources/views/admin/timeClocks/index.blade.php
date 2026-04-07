@extends('dashboardLayouts.main')
@section('title', 'Time Clock Listing')

@section('breadcrumbTitle', 'Time Clock Listing')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{route('time-clocks.index')}}">Time Clocks</a></li>
<li class="breadcrumb-item active">Listing</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header justify-content-between d-flex align-items-center">
                <h4 class="card-title shine">Time Clock Table</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <!-- Table -->
                    <table id="custom-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User Name</th>
                                <th>NFC Tag ID</th>
                                <!-- <th>Status</th> -->
                                <th>NFC Tag Name</th>
                                <th>Site Name</th>
                                <th>Company Name</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($checkPoints['nfcTags'] as $tag)
                            <tr>
                                <td>{{ $tag->id }}</td>
                                <td>{{ $tag->user->name }}</td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-primary border px-3 py-2 fw-semibold">
                                        {{ $tag->nfcTag->uid }}
                                    </span>
                                </td>
                                <!-- <td>
                                    @if($tag->time_clock)
                                    <span class="badge rounded-pill bg-success px-3 py-2">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    @else
                                    <span class="badge rounded-pill bg-warning text-dark px-3 py-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </span>
                                    @endif
                                </td> -->
                                <td>{{ $tag->nfcTag->name }}</td>
                                <td>{{ $tag->nfcTag->site->name }}</td>
                                <td>{{ $tag->nfcTag->site->company->name }}</td>
                                <td>
                                    {{ $tag->created_at->format('j M Y h:i A') }}
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