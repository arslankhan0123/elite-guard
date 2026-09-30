@extends('master.layout')
@section('title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3">
            <div class="text-muted small mb-1">Total Tenants</div>
            <div class="fs-2 fw-bold text-white">{{ $totalTenants }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3">
            <div class="text-muted small mb-1">Active Tenants</div>
            <div class="fs-2 fw-bold text-success">{{ $activeTenants }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3">
            <div class="text-muted small mb-1">Inactive Tenants</div>
            <div class="fs-2 fw-bold text-secondary">{{ $totalTenants - $activeTenants }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('master.tenants.create') }}" class="card p-3 text-decoration-none d-flex flex-row align-items-center gap-3">
            <i class="bi bi-plus-circle fs-3 text-warning"></i>
            <div>
                <div class="text-muted small">Quick Action</div>
                <div class="text-white fw-semibold">New Tenant</div>
            </div>
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Recent Tenants</h6>
        <a href="{{ route('master.tenants.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Name</th><th>Database</th><th>Status</th><th>Created</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTenants as $tenant)
                <tr>
                    <td>{{ $tenant->name }}</td>
                    <td><code class="text-warning small">{{ $tenant->db_name }}</code></td>
                    <td>
                        @if($tenant->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $tenant->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('master.tenants.show', $tenant->id) }}" class="btn btn-sm btn-outline-secondary">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No tenants yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
