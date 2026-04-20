@extends('dashboardLayouts.main')
@section('title', 'Upload Tax Document')

@section('breadcrumbTitle', 'Upload Tax Document')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tax-docs.index') }}">Tax Documents</a></li>
    <li class="breadcrumb-item active">Upload</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body p-5">
                {{-- Header --}}
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                        style="width:48px; height:48px; background:#ede9fe;">
                        <i data-feather="upload-cloud" style="width:22px; color:#7c3aed;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Upload Tax Document</h5>
                        <p class="text-muted small mb-0">Select a document type and upload the corresponding file.</p>
                    </div>
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

                <form action="{{ route('tax-docs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Document Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select rounded-3" required>
                            <option value="" disabled selected>— Select a Type —</option>
                            @foreach($availableTypes as $type)
                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Document File <span class="text-danger">*</span></label>
                        <input type="file" name="document" class="form-control rounded-3" required
                            accept=".pdf,.doc,.docx">
                        <small class="text-muted d-block mt-1">
                            <i data-feather="info" style="width:12px;"></i>
                            Accepted formats: PDF, DOC, DOCX. Max size: 5MB.
                        </small>
                        @error('document')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">
                            <i data-feather="upload" style="width:15px;" class="me-1"></i> Upload Document
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
