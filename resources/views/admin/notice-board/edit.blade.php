@extends('dashboardLayouts.main')
@section('title', 'Edit Notice')

@section('breadcrumbTitle', 'Edit Notice')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('notice-board.index') }}">Notice Board</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Notice</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('notice-board.update', $notice->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="date" class="form-label fw-bold">Date</label>
                        <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $notice->date->format('Y-m-d')) }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label fw-bold">Subject</label>
                        <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject', $notice->subject) }}" required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="long_description" class="form-label fw-bold">Long Description</label>
                        <textarea name="long_description" id="long_description" rows="6" class="form-control @error('long_description') is-invalid @enderror" required>{{ old('long_description', $notice->long_description) }}</textarea>
                        @error('long_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('notice-board.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Notice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
