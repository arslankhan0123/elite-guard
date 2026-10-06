@extends('master.layout')
@section('title', 'Create Tenant')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
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

                <form method="POST" action="{{ route('master.tenants.store') }}" id="tenantForm">
                    @csrf

                    {{-- Company Info --}}
                    <h6 class="text-muted mb-3 border-bottom pb-2">Company Information</h6>

                    <div class="mb-3">
                        <label class="form-label">Company / Tenant Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                               placeholder="e.g. Elite Guard Inc." required>
                        <div class="form-text text-muted">A database will be created automatically based on this name.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Admin Email <span class="text-danger">*</span></label>
                            <input type="email" name="admin_email" class="form-control" value="{{ old('admin_email') }}"
                                   placeholder="admin@company.com" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}"
                                   placeholder="+1 (555) 000-0000">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Admin Password <span class="text-danger">*</span></label>
                        <input type="text" name="admin_password" class="form-control"
                               placeholder="Minimum 8 characters" required minlength="8">
                    </div>

                    {{-- Subscription Plan --}}
                    <h6 class="text-muted mb-3 border-bottom pb-2">Subscription Plan</h6>

                    <div class="mb-3">
                        <label class="form-label">Plan Type <span class="text-danger">*</span></label>
                        <div class="row g-3" id="planCards">

                            {{-- Trial --}}
                            <div class="col-md-4">
                                <label class="plan-card d-block border rounded-3 p-3 cursor-pointer {{ old('subscription_type', 'trial') === 'trial' ? 'border-warning bg-warning bg-opacity-10 selected' : 'border-secondary' }}"
                                       for="plan_trial">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <input class="form-check-input" type="radio" name="subscription_type"
                                                   id="plan_trial" value="trial"
                                                   {{ old('subscription_type', 'trial') === 'trial' ? 'checked' : '' }}
                                                   onchange="updatePlan(this)">
                                            <span class="fw-bold">Trial</span>
                                        </div>
                                        <span class="badge bg-secondary">Free</span>
                                    </div>
                                    <div class="small text-muted">Limited access period for evaluation</div>
                                </label>
                            </div>

                            {{-- Monthly --}}
                            <div class="col-md-4">
                                <label class="plan-card d-block border rounded-3 p-3 cursor-pointer {{ old('subscription_type') === 'monthly' ? 'border-warning bg-warning bg-opacity-10 selected' : 'border-secondary' }}"
                                       for="plan_monthly">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <input class="form-check-input" type="radio" name="subscription_type"
                                                   id="plan_monthly" value="monthly"
                                                   {{ old('subscription_type') === 'monthly' ? 'checked' : '' }}
                                                   onchange="updatePlan(this)">
                                            <span class="fw-bold">Monthly</span>
                                        </div>
                                        <span class="badge bg-warning text-dark">$350 CAD</span>
                                    </div>
                                    <div class="small text-muted">Billed every month</div>
                                </label>
                            </div>

                            {{-- Yearly --}}
                            <div class="col-md-4">
                                <label class="plan-card d-block border rounded-3 p-3 cursor-pointer {{ old('subscription_type') === 'yearly' ? 'border-warning bg-warning bg-opacity-10 selected' : 'border-secondary' }}"
                                       for="plan_yearly">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <input class="form-check-input" type="radio" name="subscription_type"
                                                   id="plan_yearly" value="yearly"
                                                   {{ old('subscription_type') === 'yearly' ? 'checked' : '' }}
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

                    {{-- Trial period sub-selector --}}
                    <div id="trialPeriodSection" class="mb-3" style="{{ old('subscription_type', 'trial') === 'trial' ? '' : 'display:none;' }}">
                        <label class="form-label">Trial Duration <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-2" id="trialPeriodBtns">
                            @foreach(['1day' => '1 Day', '2days' => '2 Days', '3days' => '3 Days', '1week' => '1 Week', '2weeks' => '2 Weeks', '3weeks' => '3 Weeks', '1month' => '1 Month'] as $val => $label)
                                <label class="trial-period-btn border rounded-2 px-3 py-2 cursor-pointer small fw-semibold {{ old('subscription_period', '1day') === $val ? 'border-warning bg-warning bg-opacity-10 text-warning-emphasis selected' : 'border-secondary text-muted' }}"
                                       for="period_{{ $val }}">
                                    <input type="radio" name="subscription_period" id="period_{{ $val }}" value="{{ $val }}"
                                           class="d-none"
                                           {{ old('subscription_period', '1day') === $val ? 'checked' : '' }}
                                           onchange="updatePeriod(this)">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Pricing summary --}}
                    <div id="pricingSummary" class="alert alert-warning py-2 mb-4 d-flex align-items-center gap-2 small">
                        <i class="bi bi-calendar-check"></i>
                        <span id="pricingText">Trial period selected — no charge</span>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Notes (optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Internal notes...">{{ old('notes') }}</textarea>
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

<style>
.plan-card { cursor: pointer; transition: border-color .15s, background .15s; }
.plan-card.selected { border-color: #ffc107 !important; background-color: rgba(255,193,7,.1) !important; }
.plan-card:hover { border-color: #ffc107; }
.trial-period-btn { cursor: pointer; transition: border-color .15s, background .15s; }
.trial-period-btn.selected { border-color: #ffc107 !important; background-color: rgba(255,193,7,.1) !important; color: #b45309 !important; }
.trial-period-btn:hover { border-color: #ffc107; }
</style>

<script>
const MONTHLY = 350;
const YEARLY  = Math.round(350 * 12 * 0.95);

const periodLabels = {
    '1day':'1 Day','2days':'2 Days','3days':'3 Days','1week':'1 Week','2weeks':'2 Weeks','3weeks':'3 Weeks','1month':'1 Month'
};

function updatePlan(radio) {
    // Update card highlight
    document.querySelectorAll('.plan-card').forEach(el => el.classList.remove('selected'));
    radio.closest('.plan-card').classList.add('selected');

    const val = radio.value;
    const trialSection = document.getElementById('trialPeriodSection');
    const pricingText  = document.getElementById('pricingText');

    if (val === 'trial') {
        trialSection.style.display = '';
        const period = document.querySelector('input[name="subscription_period"]:checked');
        const pLabel = period ? periodLabels[period.value] : '3 Days';
        pricingText.textContent = 'Trial: ' + pLabel + ' — no charge';
    } else if (val === 'monthly') {
        trialSection.style.display = 'none';
        pricingText.textContent = 'Monthly plan — $' + MONTHLY.toLocaleString() + ' CAD / month';
    } else {
        trialSection.style.display = 'none';
        pricingText.textContent = 'Yearly plan — $' + YEARLY.toLocaleString() + ' CAD / year  (5% discount applied, saving $' + (MONTHLY*12 - YEARLY).toLocaleString() + ' CAD)';
    }
}

function updatePeriod(radio) {
    document.querySelectorAll('.trial-period-btn').forEach(el => el.classList.remove('selected'));
    radio.closest('.trial-period-btn').classList.add('selected');
    document.getElementById('pricingText').textContent = 'Trial: ' + periodLabels[radio.value] + ' — no charge';
}

// Set initial pricing text
(function() {
    const checked = document.querySelector('input[name="subscription_type"]:checked');
    if (checked) updatePlan(checked);
})();
</script>
@endsection
