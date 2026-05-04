@extends('dashboardLayouts.main')
@section('title', 'Create Notice')

@section('breadcrumbTitle', 'Create Notice')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('notice-board.index') }}">Notice Board</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Add New Notice</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('notice-board.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="date" class="form-label fw-bold">Date</label>
                        <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label fw-bold">Subject</label>
                        <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}" placeholder="Enter subject" required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="long_description" class="form-label fw-bold">Long Description</label>
                        <textarea name="long_description" id="long_description" rows="6" class="form-control @error('long_description') is-invalid @enderror" placeholder="Enter full description" required>{{ old('long_description') }}</textarea>
                        @error('long_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('notice-board.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Notice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
