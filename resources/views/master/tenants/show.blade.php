@extends('master.layout')
@section('title', $tenant->name)

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('master.tenants.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0">{{ $tenant->name }}</h5>
    @if($tenant->is_active)
        <span class="badge bg-success">Active</span>
    @else
        <span class="badge bg-secondary">Inactive</span>
    @endif
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header py-3"><h6 class="mb-0">Tenant Details</h6></div>
            <div class="card-body">
                <dl class="row mb-0" style="row-gap:.75rem;">
                    <dt class="col-sm-4 text-muted">Name</dt>
                    <dd class="col-sm-8 mb-0">{{ $tenant->name }}</dd>
                    <dt class="col-sm-4 text-muted">Slug</dt>
                    <dd class="col-sm-8 mb-0"><code class="text-info">{{ $tenant->slug }}</code></dd>
                    <dt class="col-sm-4 text-muted">Database</dt>
                    <dd class="col-sm-8 mb-0"><code class="text-warning">{{ $tenant->db_name }}</code></dd>
                    <dt class="col-sm-4 text-muted">Admin Email</dt>
                    <dd class="col-sm-8 mb-0">{{ $tenant->admin_email }}</dd>
                    <dt class="col-sm-4 text-muted">Total Users</dt>
                    <dd class="col-sm-8 mb-0">{{ $userCount }}</dd>
                    <dt class="col-sm-4 text-muted">Created</dt>
                    <dd class="col-sm-8 mb-0">{{ $tenant->created_at->format('d M Y, h:i A') }}</dd>
                    @if($tenant->notes)
                    <dt class="col-sm-4 text-muted">Notes</dt>
                    <dd class="col-sm-8 mb-0">{{ $tenant->notes }}</dd>
                    @endif
                </dl>
            </div>
            <div class="card-footer d-flex gap-2">
                <a href="{{ route('master.tenants.edit', $tenant->id) }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
                <form method="POST" action="{{ route('master.tenants.toggle-status', $tenant->id) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm {{ $tenant->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                        <i class="bi bi-{{ $tenant->is_active ? 'pause-circle' : 'play-circle' }} me-1"></i>
                        {{ $tenant->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
