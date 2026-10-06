@extends('master.layout')
@section('title', 'Edit Tenant')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
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

                <form method="POST" action="{{ route('master.tenants.update', $tenant->id) }}" id="editTenantForm">
                    @csrf @method('PUT')

                    {{-- Company Info --}}
                    <h6 class="text-muted mb-3 border-bottom pb-2">Company Information</h6>

                    <div class="mb-3">
                        <label class="form-label">Tenant Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $tenant->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Database</label>
                        <input type="text" class="form-control" value="{{ $tenant->db_name }}" disabled>
                        <div class="form-text text-muted">Database name cannot be changed.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Admin Email <span class="text-danger">*</span></label>
                            <input type="email" name="admin_email" class="form-control"
                                   value="{{ old('admin_email', $tenant->admin_email) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone', $tenant->phone) }}"
                                   placeholder="+1 (555) 000-0000">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reset Admin Password <span class="text-muted small">(leave blank to keep current)</span></label>
                        <input type="text" name="admin_password" class="form-control" placeholder="New password (min 8 chars)" minlength="8">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $tenant->notes) }}</textarea>
                    </div>

                    <div class="mb-4 form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $tenant->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Tenant Active</label>
                    </div>

                    {{-- Subscription Plan --}}
                    <h6 class="text-muted mb-3 border-bottom pb-2">Subscription Plan</h6>

                    @php
                        $currentType   = old('subscription_type', $tenant->subscription_type ?? 'trial');
                        $currentPeriod = old('subscription_period', $tenant->subscription_period ?? '1day');
                    @endphp

                    <div class="mb-3">
                        <label class="form-label">Plan Type <span class="text-danger">*</span></label>
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="plan-card d-block border rounded-3 p-3 cursor-pointer {{ $currentType === 'trial' ? 'border-warning bg-warning bg-opacity-10 selected' : 'border-secondary' }}"
                                       for="edit_plan_trial">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <input class="form-check-input" type="radio" name="subscription_type"
                                                   id="edit_plan_trial" value="trial"
                                                   {{ $currentType === 'trial' ? 'checked' : '' }}
                                                   onchange="updatePlan(this)">
                                            <span class="fw-bold">Trial</span>
                                        </div>
                                        <span class="badge bg-secondary">Free</span>
                                    </div>
                                    <div class="small text-muted">Limited evaluation period</div>
                                </label>
                            </div>

                            <div class="col-md-4">
                                <label class="plan-card d-block border rounded-3 p-3 cursor-pointer {{ $currentType === 'monthly' ? 'border-warning bg-warning bg-opacity-10 selected' : 'border-secondary' }}"
                                       for="edit_plan_monthly">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <input class="form-check-input" type="radio" name="subscription_type"
                                                   id="edit_plan_monthly" value="monthly"
                                                   {{ $currentType === 'monthly' ? 'checked' : '' }}
                                                   onchange="updatePlan(this)">
                                            <span class="fw-bold">Monthly</span>
                                        </div>
                                        <span class="badge bg-warning text-dark">$350 CAD</span>
                                    </div>
                                    <div class="small text-muted">Billed every month</div>
                                </label>
                            </div>

                            <div class="col-md-4">
                                <label class="plan-card d-block border rounded-3 p-3 cursor-pointer {{ $currentType === 'yearly' ? 'border-warning bg-warning bg-opacity-10 selected' : 'border-secondary' }}"
                                       for="edit_plan_yearly">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <input class="form-check-input" type="radio" name="subscription_type"
                                                   id="edit_plan_yearly" value="yearly"
                                                   {{ $currentType === 'yearly' ? 'checked' : '' }}
                                                   onchange="updatePlan(this)">
                                            <span class="fw-bold">Yearly</span>
                                        </div>
                                        <span class="badge bg-success">Save 5%</span>
                                    </div>
                                    <div class="small text-muted">
                                        <span class="text-decoration-line-through text-muted">$4,200 CAD</span>
                                        → <strong class="text-success">$3,990 CAD</strong>
                                    </div>
                                </label>
                            </div>

                        </div>
                    </div>

                    {{-- Trial period --}}
                    <div id="trialPeriodSection" class="mb-3" style="{{ $currentType === 'trial' ? '' : 'display:none;' }}">
                        <label class="form-label">Trial Duration <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['1day' => '1 Day', '2days' => '2 Days', '3days' => '3 Days', '1week' => '1 Week', '2weeks' => '2 Weeks', '3weeks' => '3 Weeks', '1month' => '1 Month'] as $val => $label)
                                <label class="trial-period-btn border rounded-2 px-3 py-2 cursor-pointer small fw-semibold {{ $currentPeriod === $val ? 'border-warning bg-warning bg-opacity-10 text-warning-emphasis selected' : 'border-secondary text-muted' }}"
                                       for="edit_period_{{ $val }}">
                                    <input type="radio" name="subscription_period" id="edit_period_{{ $val }}" value="{{ $val }}"
                                           class="d-none"
                                           {{ $currentPeriod === $val ? 'checked' : '' }}
                                           onchange="updatePeriod(this)">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                        <div class="form-text text-muted mt-1">
                            <i class="bi bi-info-circle me-1"></i>
                            Changing the trial period will reset the subscription start date to today.
                        </div>
                    </div>

                    {{-- Current subscription info (read-only) --}}
                    @if($tenant->subscription_ends_at)
                    <div class="alert alert-{{ $tenant->subscription_ends_at->isPast() ? 'danger' : 'info' }} py-2 small mb-3">
                        <i class="bi bi-calendar-event me-1"></i>
                        Current expiry: <strong>{{ $tenant->subscription_ends_at->format('d M Y') }}</strong>
                        ({{ $tenant->subscription_ends_at->diffForHumans() }})
                        — Saving a new plan resets this to today + new period.
                    </div>
                    @endif

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-1"></i>Save Changes
                        </button>
                        <a href="{{ route('master.tenants.show', $tenant->id) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.plan-card { cursor: pointer; transition: border-color .15s, background .15s; }
.plan-card.selected { border-color: #ffc107 !important; background-color: rgba(255,193,7,.1) !important; }
.plan-card:hover { border-color: #ffc107; }
.trial-period-btn { cursor: pointer; transition: border-color .15s, background .15s; }
.trial-period-btn.selected { border-color: #ffc107 !important; background-color: rgba(255,193,7,.1) !important; color: #b45309 !important; }
.trial-period-btn:hover { border-color: #ffc107; }
</style>

<script>
function updatePlan(radio) {
    document.querySelectorAll('.plan-card').forEach(el => el.classList.remove('selected'));
    radio.closest('.plan-card').classList.add('selected');
    const trialSection = document.getElementById('trialPeriodSection');
    trialSection.style.display = radio.value === 'trial' ? '' : 'none';
}
function updatePeriod(radio) {
    document.querySelectorAll('.trial-period-btn').forEach(el => el.classList.remove('selected'));
    radio.closest('.trial-period-btn').classList.add('selected');
}
</script>
@endsection
