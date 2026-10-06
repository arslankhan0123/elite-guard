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
    {{-- Subscription badge --}}
    @if($tenant->subscription_type === 'trial')
        <span class="badge bg-info text-dark">Trial</span>
    @elseif($tenant->subscription_type === 'monthly')
        <span class="badge bg-warning text-dark">Monthly</span>
    @elseif($tenant->subscription_type === 'yearly')
        <span class="badge bg-success">Yearly</span>
    @endif
    @if($tenant->subscription_ends_at && $tenant->subscription_ends_at->isPast())
        <span class="badge bg-danger">Expired</span>
    @endif
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card mb-4">
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
                    @if($tenant->phone)
                    <dt class="col-sm-4 text-muted">Phone</dt>
                    <dd class="col-sm-8 mb-0">{{ $tenant->phone }}</dd>
                    @endif
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

        {{-- Subscription Card --}}
        <div class="card">
            <div class="card-header py-3"><h6 class="mb-0"><i class="bi bi-credit-card me-2"></i>Subscription</h6></div>
            <div class="card-body">
                <dl class="row mb-0" style="row-gap:.75rem;">
                    <dt class="col-sm-4 text-muted">Plan</dt>
                    <dd class="col-sm-8 mb-0">
                        @if($tenant->subscription_type === 'trial')
                            <span class="badge bg-info text-dark">Trial</span>
                            @if($tenant->subscription_period)
                                &nbsp;
                                @php
                                    $periodMap = ['1day'=>'1 Day','2days'=>'2 Days','3days'=>'3 Days','1week'=>'1 Week','2weeks'=>'2 Weeks','3weeks'=>'3 Weeks','1month'=>'1 Month'];
                                @endphp
                                <span class="text-muted small">({{ $periodMap[$tenant->subscription_period] ?? $tenant->subscription_period }})</span>
                            @endif
                        @elseif($tenant->subscription_type === 'monthly')
                            <span class="badge bg-warning text-dark">Monthly</span>
                            <span class="text-muted small ms-1">$350 CAD / month</span>
                        @elseif($tenant->subscription_type === 'yearly')
                            <span class="badge bg-success">Yearly</span>
                            <span class="text-muted small ms-1">$3,990 CAD / year</span>
                        @endif
                    </dd>
                    @if($tenant->subscription_amount)
                    <dt class="col-sm-4 text-muted">Amount</dt>
                    <dd class="col-sm-8 mb-0">${{ number_format($tenant->subscription_amount, 2) }} CAD</dd>
                    @endif
                    <dt class="col-sm-4 text-muted">Starts</dt>
                    <dd class="col-sm-8 mb-0">{{ $tenant->subscription_starts_at?->format('d M Y') ?? '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Expires</dt>
                    <dd class="col-sm-8 mb-0">
                        @if($tenant->subscription_ends_at)
                            @if($tenant->subscription_ends_at->isPast())
                                <span class="text-danger fw-semibold">{{ $tenant->subscription_ends_at->format('d M Y') }} (Expired)</span>
                            @elseif($tenant->subscription_ends_at->diffInDays(now()) <= 7)
                                <span class="text-warning fw-semibold">{{ $tenant->subscription_ends_at->format('d M Y') }} ({{ $tenant->subscription_ends_at->diffForHumans() }})</span>
                            @else
                                {{ $tenant->subscription_ends_at->format('d M Y') }}
                                <span class="text-muted small">({{ $tenant->subscription_ends_at->diffForHumans() }})</span>
                            @endif
                        @else
                            —
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
