@extends('dashboardLayouts.main')
@section('title', 'Edit Policy')

@section('breadcrumbTitle', 'Edit Policy')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('policies.index') }}">Policies</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card card-h-100">
            <div class="card-header justify-content-between d-flex align-items-center">
                <h4 class="card-title">Policy Edit</h4>
                <a href="{{route('policies.index')}}" class="btn btn-sm btn-secondary-subtle"><i class="mdi mdi-arrow-right align-middle"></i> Back</a>
            </div>
            <div class="card-body">
                <form action="{{ route('policies.update', $policy->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Policy Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="Employment Policies" {{ old('type', $policy->type) == 'Employment Policies' ? 'selected' : '' }}>Employment Policies</option>
                                <option value="Violence and Harassment Prevention Plan" {{ old('type', $policy->type) == 'Violence and Harassment Prevention Plan' ? 'selected' : '' }}>Violence and Harassment Prevention Plan</option>
                                <option value="Public Complaints Policy" {{ old('type', $policy->type) == 'Public Complaints Policy' ? 'selected' : '' }}>Public Complaints Policy</option>
                                <option value="Code of Conduct" {{ old('type', $policy->type) == 'Code of Conduct' ? 'selected' : '' }}>Code of Conduct</option>
                            </select>
                            @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="1" {{ old('status', $policy->status) == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $policy->status) == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Document</label>
                            <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx,.txt,image/*">
                            @if($policy->document)
                            <div class="mt-2 text-muted">
                                Current Document: <a href="{{ $policy->document }}" target="_blank">View File</a>
                            </div>
                            @endif
                            <small class="text-muted">Leave blank to keep the current document. Max size: 5MB.</small>
                            @error('document') <br><span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary px-4">Update Policy</button>
                            <a href="{{ route('policies.index') }}" class="btn btn-light px-4">Cancel</a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
