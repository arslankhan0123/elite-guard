@extends('dashboardLayouts.main')
@section('title', 'Orientation Create')

@section('breadcrumbTitle', 'Orientation Create')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('orientations.index') }}">Orientations</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header justify-content-between d-flex align-items-center">
                    <h4 class="card-title shine">Create New Orientation</h4>
                    <a href="{{ route('orientations.index') }}" class="btn btn-sm btn-secondary-subtle">
                        <i class="mdi mdi-arrow-left align-middle"></i> Back to Listing
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('orientations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Orientation Type <span class="text-danger">*</span></label>
                                <input type="text" name="type" class="form-control" value="{{ old('type') }}"
                                    required placeholder="e.g. Workplace Safety, Remote Work, etc.">
                                @error('type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Orientation Document <span class="text-danger">*</span></label>
                                <input type="file" name="document" class="form-control" required
                                    accept=".pdf,.doc,.docx,.txt,.png,.jpg,.jpeg">
                                <small class="text-muted">Accepted formats: PDF, DOC, DOCX, TXT, PNG, JPG (Max 5MB)</small>
                                @error('document')
                                    <br><span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-4">Create Orientation</button>
                                <a href="{{ route('orientations.index') }}" class="btn btn-light px-4">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
