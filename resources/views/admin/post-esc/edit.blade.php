@extends('dashboardLayouts.main')
@section('title', 'Edit Post & ESC')
@section('breadcrumbTitle', 'Edit Post & ESC')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('post-esc.index') }}">Post &amp; ESC</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Edit Post &amp; ESC</h4></div>
            <div class="card-body">
                <form action="{{ route('post-esc.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('admin.post-esc.form', ['post' => $post])
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('post-esc.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Post &amp; ESC</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
