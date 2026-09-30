@extends('master.layout')
@section('title', 'Tenants')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">All Tenants</h5>
    <a href="{{ route('master.tenants.create') }}" class="btn btn-warning btn-sm">
        <i class="bi bi-plus-lg me-1"></i>New Tenant
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th><th>Name</th><th>Database</th><th>Admin Email</th><th>Status</th><th>Created</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                <tr>
                    <td class="text-muted">{{ $tenant->id }}</td>
                    <td class="fw-semibold">{{ $tenant->name }}</td>
                    <td><code class="text-warning small">{{ $tenant->db_name }}</code></td>
                    <td class="text-muted small">{{ $tenant->admin_email }}</td>
                    <td>
                        @if($tenant->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $tenant->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('master.tenants.show', $tenant->id) }}" class="btn btn-sm btn-outline-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('master.tenants.edit', $tenant->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('master.tenants.toggle-status', $tenant->id) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm {{ $tenant->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                        title="{{ $tenant->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="bi bi-{{ $tenant->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No tenants found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tenants->hasPages())
        <div class="card-footer">{{ $tenants->links() }}</div>
    @endif
</div>
@endsection
