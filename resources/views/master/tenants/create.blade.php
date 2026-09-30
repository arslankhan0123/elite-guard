@extends('master.layout')
@section('title', 'Create Tenant')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header py-3">
                <h6 class="mb-0"><i class="bi bi-plus-circle me-2 text-warning"></i>Create New Tenant</h6>
            </div>
            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('master.tenants.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label text-muted">Company / Tenant Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                               placeholder="e.g. Elite Guard Inc." required>
                        <div class="form-text text-muted">A database will be created automatically based on this name.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Admin Email <span class="text-danger">*</span></label>
                        <input type="email" name="admin_email" class="form-control" value="{{ old('admin_email') }}"
                               placeholder="admin@company.com" required>
                        <div class="form-text text-muted">Tenant's SuperAdmin login email.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Admin Password <span class="text-danger">*</span></label>
                        <input type="text" name="admin_password" class="form-control"
                               placeholder="Minimum 8 characters" required minlength="8">
                        <div class="form-text text-muted">Login password for the tenant's admin account.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted">Notes (optional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Internal notes...">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-1"></i>Create Tenant
                        </button>
                        <a href="{{ route('master.tenants.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
