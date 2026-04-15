@extends('dashboardLayouts.main')
@section('title', 'Number Create')

@section('breadcrumbTitle', 'Number Create')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{route('numbers.index')}}">Numbers</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card card-h-100">
            <div class="card-header justify-content-between d-flex align-items-center">
                <h4 class="card-title">Number Create</h4>
                <a href="{{route('numbers.index')}}" class="btn btn-sm btn-secondary-subtle"><i class="mdi mdi-arrow-right align-middle"></i> Back</a>
            </div>
            <div class="card-body">
                <form action="{{ route('numbers.store') }}" method="POST">
                    @csrf

                    <h5 class="text-primary">📞 Number Information</h5>

                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. John Doe">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Designation <span class="text-danger">*</span></label>
                            <input type="text" name="designation" class="form-control" value="{{ old('designation') }}" required placeholder="e.g. Manager, Support Agent">
                            @error('designation') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Number <span class="text-danger">*</span></label>
                            <input type="text" name="number" class="form-control" value="{{ old('number') }}" required placeholder="e.g. 123456789">
                            @error('number') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Number With Code</label>
                            <input type="text" name="number_with_code" class="form-control" value="{{ old('number_with_code') }}" placeholder="e.g. +92123456789">
                            @error('number_with_code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                <option value="">-- Select Type --</option>
                                <option value="Website" {{ old('type') == 'Website' ? 'selected' : '' }}>Website</option>
                                <option value="Application" {{ old('type') == 'Application' ? 'selected' : '' }}>Application</option>
                                <option value="Footer/Header" {{ old('type') == 'Footer/Header' ? 'selected' : '' }}>Footer/Header</option>
                                <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary px-4">Create Number</button>
                            <a href="{{ route('numbers.index') }}" class="btn btn-light px-4">Cancel</a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
