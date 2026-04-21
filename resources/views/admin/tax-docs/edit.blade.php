@extends('dashboardLayouts.main')
@section('title', 'Edit Tax Document')

@section('breadcrumbTitle', 'Edit Tax Document')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tax-docs.index') }}">Tax Documents</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body p-5">
                {{-- Header --}}
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                        style="width:48px; height:48px; background:#f0fdf4;">
                        <i data-feather="edit-2" style="width:22px; color:#059669;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Edit Tax Document</h5>
                        <p class="text-muted small mb-0">
                            Currently editing: <strong class="text-dark">{{ $taxDoc->type }}</strong>
                        </p>
                    </div>
                </div>

                {{-- Currently uploaded file --}}
                <div class="p-3 rounded-3 mb-4 d-flex align-items-center gap-3"
                    style="background:#f8faff; border: 1px solid #e2e8f0;">
                    <i data-feather="file-text" style="width:20px; color:#7c3aed; flex-shrink:0;"></i>
                    <div class="overflow-hidden">
                        <p class="fw-semibold text-dark mb-0 small">Current File</p>
                        <a href="{{ $taxDoc->file_path }}" target="_blank" rel="noopener noreferrer"
                            class="text-primary small text-truncate d-block">
                            {{ basename($taxDoc->file_path) }}
                        </a>
                    </div>
                    <a href="{{ $taxDoc->file_path }}" target="_blank" rel="noopener noreferrer"
                        class="btn btn-sm btn-outline-primary rounded-pill px-3 ms-auto fw-semibold flex-shrink-0">
                        <i data-feather="download" style="width:13px;" class="me-1"></i> View
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger rounded-3 border-0 mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('tax-docs.update', $taxDoc->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Document Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select rounded-3" required>
                            @foreach(\App\Models\TaxDocument::TYPES as $type)
                                <option value="{{ $type }}" {{ (old('type', $taxDoc->type)) == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">
                            <i data-feather="alert-circle" style="width:12px;"></i>
                            Changing to a type that already has a document will be rejected.
                        </small>
                        @error('type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Replace File <span class="text-muted fw-normal">(Optional)</span></label>
                        <input type="file" name="document" class="form-control rounded-3" accept=".pdf,.doc,.docx">
                        <small class="text-muted d-block mt-1">
                            <i data-feather="info" style="width:12px;"></i>
                            Accepted formats: PDF, DOC, DOCX. Max size: 5MB. Leave empty to keep the current file.
                        </small>
                        @error('document')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold">
                            <i data-feather="save" style="width:15px;" class="me-1"></i> Save Changes
                        </button>
                        <a href="{{ route('tax-docs.index') }}" class="btn btn-light rounded-pill px-4 fw-semibold">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () { feather.replace(); });
</script>
@endsection
