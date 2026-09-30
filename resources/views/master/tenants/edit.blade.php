@extends('master.layout')
@section('title', 'Edit Tenant')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header py-3">
                <h6 class="mb-0"><i class="bi bi-pencil me-2 text-warning"></i>Edit Tenant: {{ $tenant->name }}</h6>
            </div>
            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('master.tenants.update', $tenant->id) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label text-muted">Tenant Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $tenant->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Database</label>
                        <input type="text" class="form-control" value="{{ $tenant->db_name }}" disabled>
                        <div class="form-text text-muted">Database name cannot be changed.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Admin Email <span class="text-danger">*</span></label>
                        <input type="email" name="admin_email" class="form-control"
                               value="{{ old('admin_email', $tenant->admin_email) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Phone Number</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone', $tenant->phone) }}"
                               placeholder="+1 (555) 000-0000">
                        <div class="form-text text-muted">Company contact number.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Reset Admin Password <span class="text-muted small">(leave blank to keep current)</span></label>
                        <input type="text" name="admin_password" class="form-control" placeholder="New password (min 8 chars)" minlength="8">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $tenant->notes) }}</textarea>
                    </div>

                    <div class="mb-4 form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $tenant->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="is_active">Tenant Active</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-1"></i>Save Changes
                        </button>
                        <a href="{{ route('master.tenants.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
