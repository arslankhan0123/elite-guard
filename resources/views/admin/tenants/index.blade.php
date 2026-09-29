@extends('dashboardLayouts.main')
@section('title', 'Tenant Management')

@section('breadcrumbTitle', 'Tenant Management')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Tenants</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <!-- Stats Row -->
        <div class="row mb-4">
            @php
                $total      = $tenants->total();
                $active     = \App\Models\Tenant::where('status','active')->count();
                $inactive   = \App\Models\Tenant::where('status','inactive')->count();
                $suspended  = \App\Models\Tenant::where('status','suspended')->count();
            @endphp
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg,#1e1b4b 0%,#4338ca 100%); color:#fff; border-radius:14px;">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div style="font-size:2rem;">🏢</div>
                        <div>
                            <div style="font-size:1.6rem; font-weight:800; line-height:1;">{{ $total }}</div>
                            <div style="font-size:.75rem; opacity:.8; text-transform:uppercase; letter-spacing:.05em;">Total Tenants</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg,#065f46 0%,#10b981 100%); color:#fff; border-radius:14px;">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div style="font-size:2rem;">✅</div>
                        <div>
                            <div style="font-size:1.6rem; font-weight:800; line-height:1;">{{ $active }}</div>
                            <div style="font-size:.75rem; opacity:.8; text-transform:uppercase; letter-spacing:.05em;">Active</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg,#78350f 0%,#f59e0b 100%); color:#fff; border-radius:14px;">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div style="font-size:2rem;">⏸️</div>
                        <div>
                            <div style="font-size:1.6rem; font-weight:800; line-height:1;">{{ $inactive }}</div>
                            <div style="font-size:.75rem; opacity:.8; text-transform:uppercase; letter-spacing:.05em;">Inactive</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg,#7f1d1d 0%,#ef4444 100%); color:#fff; border-radius:14px;">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div style="font-size:2rem;">🚫</div>
                        <div>
                            <div style="font-size:1.6rem; font-weight:800; line-height:1;">{{ $suspended }}</div>
                            <div style="font-size:.75rem; opacity:.8; text-transform:uppercase; letter-spacing:.05em;">Suspended</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-header justify-content-between d-flex align-items-center">
                <h4 class="card-title shine mb-0">Tenant Directory</h4>
                <a href="{{ route('tenants.create') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                    <i class="mdi mdi-plus-circle"></i> Add Tenant
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="custom-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Company</th>
                                <th>Contact Person</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Plan</th>
                                <th>Status</th>
                                <th>Subscription Ends</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tenants as $tenant)
                            <tr>
                                <td>{{ $tenant->id }}</td>
                                <td>
                                    <div class="fw-bold">{{ $tenant->company_name }}</div>
                                    @if($tenant->city || $tenant->country)
                                    <div class="text-muted" style="font-size:.8rem;">
                                        {{ collect([$tenant->city, $tenant->country])->filter()->implode(', ') }}
                                    </div>
                                    @endif
                                </td>
                                <td>{{ $tenant->contact_person }}</td>
                                <td>
                                    <a href="mailto:{{ $tenant->email }}" class="text-decoration-none">{{ $tenant->email }}</a>
                                </td>
                                <td>{{ $tenant->phone ?? '—' }}</td>
                                <td>
                                    @php
                                        $planColors = ['basic'=>'bg-secondary','standard'=>'bg-info','premium'=>'bg-warning text-dark'];
                                        $planColor  = $planColors[$tenant->plan] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $planColor }} rounded-pill">{{ ucfirst($tenant->plan) }}</span>
                                </td>
                                <td>
                                    @if($tenant->status === 'active')
                                        <span class="badge bg-success rounded-pill">Active</span>
                                    @elseif($tenant->status === 'inactive')
                                        <span class="badge bg-warning text-dark rounded-pill">Inactive</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill">Suspended</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tenant->subscription_ends_at)
                                        @php
                                            $expired = $tenant->subscription_ends_at->isPast();
                                        @endphp
                                        <span class="{{ $expired ? 'text-danger fw-bold' : 'text-success' }}">
                                            {{ $tenant->subscription_ends_at->format('d M Y') }}
                                        </span>
                                        @if($expired)
                                            <span class="badge bg-danger ms-1" style="font-size:.65rem;">Expired</span>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('tenants.edit', $tenant->id) }}"
                                           class="text-decoration-none" data-bs-toggle="tooltip" title="Edit Tenant">
                                            <button class="editBtn" type="button">
                                                <svg height="1em" viewBox="0 0 512 512">
                                                    <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/>
                                                </svg>
                                            </button>
                                        </a>
                                        <form action="{{ route('tenants.destroy', $tenant->id) }}" method="POST"
                                              onsubmit="return confirm('Delete tenant {{ addslashes($tenant->company_name) }}? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bin-button" data-bs-toggle="tooltip" title="Delete Tenant">
                                                <svg class="bin-top" viewBox="0 0 39 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <line y1="5" x2="39" y2="5" stroke="white" stroke-width="4"/>
                                                    <line x1="12" y1="1.5" x2="26.0357" y2="1.5" stroke="white" stroke-width="3"/>
                                                </svg>
                                                <svg class="bin-bottom" viewBox="0 0 33 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <mask id="path-1-inside-1_t" fill="white">
                                                        <path d="M0 0H33V35C33 37.2091 31.2091 39 29 39H4C1.79086 39 0 37.2091 0 35V0Z"/>
                                                    </mask>
                                                    <path d="M0 0H33H0ZM37 35C37 39.4183 33.4183 43 29 43H4C-0.418278 43 -4 39.4183 -4 35H4H29H37ZM4 43C-0.418278 43 -4 39.4183 -4 35V0H4V35V43ZM37 0V35C37 39.4183 33.4183 43 29 43V35V0H37Z" fill="white" mask="url(#path-1-inside-1_t)"/>
                                                    <path d="M12 6L12 29" stroke="white" stroke-width="4"/>
                                                    <path d="M21 6V29" stroke="white" stroke-width="4"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-office-building-outline" style="font-size:2rem;"></i>
                                    <p class="mt-2 mb-0">No tenants found. <a href="{{ route('tenants.create') }}">Add your first tenant</a>.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tenants->hasPages())
                <div class="d-flex justify-content-between align-items-center server-pagination">
                    <span class="text-muted" style="font-size:.9rem;">
                        Showing {{ $tenants->firstItem() }}–{{ $tenants->lastItem() }} of {{ $tenants->total() }} tenants
                    </span>
                    {{ $tenants->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
