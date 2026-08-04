@extends('dashboardLayouts.main')
@section('title', 'Create Post & ESC')
@section('breadcrumbTitle', 'Create Post & ESC')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('post-esc.index') }}">Post &amp; ESC</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Add New Post &amp; ESC</h4></div>
            <div class="card-body">
                <form action="{{ route('post-esc.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('admin.post-esc.form')
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('post-esc.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Post &amp; ESC</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
