@extends('dashboardLayouts.main')
@section('title', 'Edit Tenant')

@section('breadcrumbTitle', 'Edit Tenant')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tenants.index') }}">Tenants</a></li>
    <li class="breadcrumb-item active">Edit: {{ $tenant->company_name }}</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-office-building-edit-outline font-size-20 text-warning"></i>
                        <h4 class="card-title shine mb-0">Edit Tenant: {{ $tenant->company_name }}</h4>
                    </div>
                    <span
                        class="badge {{ $tenant->status === 'active' ? 'bg-success' : ($tenant->status === 'suspended' ? 'bg-danger' : 'bg-warning text-dark') }} rounded-pill px-3">
                        {{ ucfirst($tenant->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenants.update', $tenant->id) }}" method="POST" id="tenant-edit-form">
                        @csrf
                        @method('PUT')

                        <!-- Company Information -->
                        <div class="mb-4">
                            <h6 class="text-uppercase fw-bold text-muted mb-3"
                                style="font-size:.75rem; letter-spacing:.08em;">
                                🏢 Company Information
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="company_name">Company Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="company_name" name="company_name"
                                        class="form-control @error('company_name') is-invalid @enderror"
                                        value="{{ old('company_name', $tenant->company_name) }}" required>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="contact_person">Contact Person <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="contact_person" name="contact_person"
                                        class="form-control @error('contact_person') is-invalid @enderror"
                                        value="{{ old('contact_person', $tenant->contact_person) }}" required>
                                    @error('contact_person')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="email">Email Address <span
                                            class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $tenant->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-muted">📧 An update notification will be sent to this
                                        address.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="phone">Phone Number</label>
                                    <input type="text" id="phone" name="phone"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone', $tenant->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold" for="address">Address</label>
                                    <input type="text" id="address" name="address"
                                        class="form-control @error('address') is-invalid @enderror"
                                        value="{{ old('address', $tenant->address) }}">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="city">City</label>
                                    <input type="text" id="city" name="city"
                                        class="form-control @error('city') is-invalid @enderror"
                                        value="{{ old('city', $tenant->city) }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="country">Country</label>
                                    <input type="text" id="country" name="country"
                                        class="form-control @error('country') is-invalid @enderror"
                                        value="{{ old('country', $tenant->country) }}">
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Subscription Details -->
                        <div class="mb-4">
                            <h6 class="text-uppercase fw-bold text-muted mb-3"
                                style="font-size:.75rem; letter-spacing:.08em;">
                                💳 Subscription & Plan
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" for="plan">Plan <span
                                            class="text-danger">*</span></label>
                                    <select id="plan" name="plan" class="form-select @error('plan') is-invalid @enderror"
                                        required>
                                        <option value="basic" {{ old('plan', $tenant->plan) === 'basic' ? 'selected' : '' }}>
                                            Basic</option>
                                        <option value="standard" {{ old('plan', $tenant->plan) === 'standard' ? 'selected' : '' }}>Standard</option>
                                        <option value="premium" {{ old('plan', $tenant->plan) === 'premium' ? 'selected' : '' }}>Premium</option>
                                    </select>
                                    @error('plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" for="status">Status <span
                                            class="text-danger">*</span></label>
                                    <select id="status" name="status"
                                        class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status', $tenant->status) === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $tenant->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="suspended" {{ old('status', $tenant->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <!-- spacer -->
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="subscription_starts_at">Subscription
                                        Starts</label>
                                    <input type="date" id="subscription_starts_at" name="subscription_starts_at"
                                        class="form-control @error('subscription_starts_at') is-invalid @enderror"
                                        value="{{ old('subscription_starts_at', $tenant->subscription_starts_at?->format('Y-m-d')) }}">
                                    @error('subscription_starts_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="subscription_ends_at">Subscription
                                        Ends</label>
                                    <input type="date" id="subscription_ends_at" name="subscription_ends_at"
                                        class="form-control @error('subscription_ends_at') is-invalid @enderror"
                                        value="{{ old('subscription_ends_at', $tenant->subscription_ends_at?->format('Y-m-d')) }}">
                                    @error('subscription_ends_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Notes -->
                        <div class="mb-4">
                            <h6 class="text-uppercase fw-bold text-muted mb-3"
                                style="font-size:.75rem; letter-spacing:.08em;">
                                📝 Internal Notes
                            </h6>
                            <textarea id="notes" name="notes" rows="4"
                                class="form-control @error('notes') is-invalid @enderror"
                                placeholder="Internal notes...">{{ old('notes', $tenant->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notice -->
                        <div class="alert alert-warning border-0 d-flex align-items-start gap-2 mb-4"
                            style="border-radius:12px; background:#fffbeb;">
                            <i class="mdi mdi-email-edit-outline font-size-18 text-warning mt-1"></i>
                            <div>
                                <strong>Email Notification</strong><br>
                                <span class="text-muted" style="font-size:.875rem;">
                                    Saving changes will dispatch an update notification email to the tenant with the latest
                                    account details.
                                </span>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning px-4 text-white">
                                <i class="mdi mdi-content-save me-1"></i> Update Tenant & Send Email
                            </button>
                            <a href="{{ route('tenants.index') }}" class="btn btn-secondary px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection