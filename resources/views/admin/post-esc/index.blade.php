@extends('dashboardLayouts.main')
@section('title', 'Post & ESC')
@section('breadcrumbTitle', 'Post & ESC')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Post &amp; ESC</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Post &amp; ESC</h4>
                <a href="{{ route('post-esc.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Create Post &amp; ESC</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="custom-table" class="table table-striped table-bordered">
                        <thead><tr><th>ID</th><th>Date</th><th>Subject</th><th>Description</th><th>PDF</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach($posts as $post)
                            <tr>
                                <td>{{ $post->id }}</td>
                                <td>{{ $post->date->format('j M Y') }}</td>
                                <td>{{ $post->subject }}</td>
                                <td>{{ Str::limit($post->long_description, 100) }}</td>
                                <td>
                                    @if($post->pdf_path)
                                        <a href="{{ route('post-esc.download', $post->id) }}" class="btn btn-outline-primary btn-sm">Download PDF</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Post and ESC actions">
                                        <a class="text-decoration-none me-2 text-dark ml-1" href="{{ route('post-esc.edit', $post->id) }}" title="Edit Post & ESC">
                                            <button type="button" class="editBtn"><svg height="1em" viewBox="0 0 512 512"><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231z"></path></svg></button>
                                        </a>
                                        <a href="{{ route('post-esc.delete', $post->id) }}" class="bin-button ml-1" title="Delete Post & ESC" onclick="return confirm('Are you sure you want to delete this Post & ESC?')">
                                            <svg class="bin-top" viewBox="0 0 39 7" fill="none"><line y1="5" x2="39" y2="5" stroke="white" stroke-width="4"></line><line x1="12" y1="1.5" x2="26.0357" y2="1.5" stroke="white" stroke-width="3"></line></svg>
                                            <svg class="bin-bottom" viewBox="0 0 33 39" fill="none"><path d="M4 0h25v35a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4V0z" fill="white"></path><path d="M12 6v23M21 6v23" stroke="black" stroke-width="4"></path></svg>
                                        </a>
                                    </div>
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
