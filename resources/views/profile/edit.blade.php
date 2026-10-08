@extends('dashboardLayouts.main')
@section('title', 'User Profile')

@section('breadcrumbTitle', 'Account Settings')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Profile Settings</li>
@endsection

@section('content')
<style>
    .profile-card {
        border: none;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }
    .profile-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%);
        color: white;
        padding: 30px;
        border-radius: 20px;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
    }
    .profile-header::after {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 150px;
        height: 150px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .form-label {
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
    }
    .form-control {
        border-radius: 12px;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s;
    }
    .form-control:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
    }
    .btn-save {
        background: #7c3aed;
        color: white;
        border-radius: 12px;
        padding: 12px 30px;
        font-weight: 600;
        border: none;
        transition: all 0.3s;
    }
    .btn-save:hover {
        background: #6d28d9;
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(109, 40, 217, 0.2);
        color: white;
    }
    /* Tab styles */
    .profile-tabs .nav-link {
        color: #64748b;
        font-weight: 600;
        border-radius: 12px 12px 0 0;
        padding: 12px 24px;
        border: none;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
    }
    .profile-tabs .nav-link:hover {
        color: #7c3aed;
        background: rgba(124,58,237,0.05);
    }
    .profile-tabs .nav-link.active {
        color: #7c3aed;
        background: #fff;
        border-bottom: 3px solid #7c3aed;
    }
    .logo-preview-wrap {
        width: 130px;
        height: 130px;
        border-radius: 16px;
        overflow: hidden;
        border: 2px dashed #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
    }
    .logo-preview-wrap img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
</style>

<div class="container-fluid" style="max-width: 100%;">
    <!-- Header Banner -->
    <div class="profile-header shadow-sm">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: rgba(255,255,255,0.2)">
                    <i data-feather="user" style="width: 40px; height: 40px; color: #fff;"></i>
                </div>
            </div>
            <div class="flex-grow-1 ms-4">
                <h2 class="fw-bold mb-1">{{ Auth::user()->name }}</h2>
                <p class="mb-0 text-white-50 fs-5">Manage your account profile and security settings.</p>
                <div class="mt-3" style="max-width: 520px;">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-semibold">Profile complete</span>
                        <span class="fw-bold">{{ $profileCompletion['percentage'] }}%</span>
                    </div>
                    <div class="progress bg-white bg-opacity-25" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $profileCompletion['percentage'] }}%" aria-valuenow="{{ $profileCompletion['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            'personal_info' => 'Personal info',
            'orientation_exams' => 'Orientation exams passed',
            'documents' => 'Documents uploaded',
            'policies' => 'Policies signed',
            'tax_documents' => 'Tax documents filled',
        ] as $key => $label)
            @php($section = $profileCompletion['sections'][$key])
            <div class="col-md-6 col-xl">
                <div class="card profile-card h-100">
                    <div class="card-body p-3">
                        <div class="small text-muted mb-1">{{ $label }}</div>
                        <div class="fw-bold fs-5">{{ $section['completed'] }} / {{ $section['total'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav profile-tabs border-bottom mb-4" id="profileTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ !request('tab') || request('tab') === 'profile' ? 'active' : '' }}" id="tab-profile" data-bs-toggle="tab" data-bs-target="#pane-profile" type="button" role="tab">
                <i data-feather="user" style="width:16px;height:16px;" class="me-1"></i> My Profile
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ request('tab') === 'security' ? 'active' : '' }}" id="tab-security" data-bs-toggle="tab" data-bs-target="#pane-security" type="button" role="tab">
                <i data-feather="lock" style="width:16px;height:16px;" class="me-1"></i> Security
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ request('tab') === 'settings' ? 'active' : '' }}" id="tab-settings" data-bs-toggle="tab" data-bs-target="#pane-settings" type="button" role="tab">
                <i data-feather="settings" style="width:16px;height:16px;" class="me-1"></i> Company Settings
            </button>
        </li>
        @if(Auth::user()->role === 'SuperAdmin')
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ request('tab') === 'smtp' ? 'active' : '' }}" id="tab-smtp" data-bs-toggle="tab" data-bs-target="#pane-smtp" type="button" role="tab">
                    <i data-feather="mail" style="width:16px;height:16px;" class="me-1"></i> SMTP Settings
                </button>
            </li>
        @endif
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ request('tab') === 'timezone' ? 'active' : '' }}" id="tab-timezone" data-bs-toggle="tab" data-bs-target="#pane-timezone" type="button" role="tab">
                <i data-feather="clock" style="width:16px;height:16px;" class="me-1"></i> Timezone
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="profileTabContent">

        {{-- ===== PROFILE TAB ===== --}}
        <div class="tab-pane fade {{ !request('tab') || request('tab') === 'profile' ? 'show active' : '' }}" id="pane-profile" role="tabpanel">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card profile-card h-100">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="p-2 bg-primary-subtle rounded-3 me-3">
                                    <i data-feather="edit-3" class="text-primary"></i>
                                </div>
                                <h4 class="fw-bold mb-0">Profile Information</h4>
                            </div>
                            <p class="text-muted mb-5">Update your account's profile information and email address.</p>

                            <form method="post" action="{{ route('profile.update') }}">
                                @csrf
                                @method('patch')

                                <div class="mb-4">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-5">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <button type="submit" class="btn-save">Update Profile</button>
                                    @if (session('status') === 'profile-updated')
                                        <span class="text-success small fw-semibold animate__animated animate__fadeIn">
                                            <i class="mdi mdi-check-circle me-1"></i> Saved Successfully
                                        </span>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Danger Zone -->
                <div class="col-lg-6 mb-4">
                    <div class="card profile-card border-danger-subtle h-100" style="background: #fffafa;">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="p-2 bg-danger-subtle rounded-3 me-3 text-danger">
                                    <i data-feather="alert-triangle"></i>
                                </div>
                                <h4 class="fw-bold mb-0">Danger Zone</h4>
                            </div>
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                                <div>
                                    <h5 class="fw-bold text-danger mb-1">Delete Account</h5>
                                    <p class="text-muted mb-3 mb-md-0">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                                </div>
                                <button class="btn btn-outline-danger px-4 rounded-pill fw-bold" disabled>Delete Temporarily Disabled</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== SECURITY TAB ===== --}}
        <div class="tab-pane fade {{ request('tab') === 'security' ? 'show active' : '' }}" id="pane-security" role="tabpanel">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card profile-card h-100">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="p-2 bg-warning-subtle rounded-3 me-3">
                                    <i data-feather="lock" class="text-warning"></i>
                                </div>
                                <h4 class="fw-bold mb-0">Update Password</h4>
                            </div>
                            <p class="text-muted mb-5">Ensure your account is using a long, random password to stay secure.</p>

                            <form method="post" action="{{ route('password.update') }}">
                                @csrf
                                @method('put')

                                <div class="mb-4">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input id="current_password" name="current_password" type="password" class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif" autocomplete="current-password">
                                    @if($errors->updatePassword->has('current_password'))
                                        <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
                                    @endif
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label">New Password</label>
                                    <input id="password" name="password" type="password" class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif" autocomplete="new-password">
                                    @if($errors->updatePassword->has('password'))
                                        <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
                                    @endif
                                </div>

                                <div class="mb-5">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control">
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <button type="submit" class="btn-save">Change Password</button>
                                    @if (session('status') === 'password-updated')
                                        <span class="text-success small fw-semibold animate__animated animate__fadeIn">
                                            <i class="mdi mdi-check-circle me-1"></i> Changed Successfully
                                        </span>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== COMPANY SETTINGS TAB ===== --}}
        <div class="tab-pane fade {{ request('tab') === 'settings' ? 'show active' : '' }}" id="pane-settings" role="tabpanel">

            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card profile-card">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="p-2 rounded-3 me-3" style="background: rgba(124,58,237,0.12); width:44px;height:44px;display:flex;align-items:center;justify-content:center;">
                                    <i data-feather="settings" style="width:24px;height:24px;color:#7c3aed;"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0">Company Settings</h4>
                                    <p class="text-muted mb-0 small">Configure your company's branding and contact information.</p>
                                </div>
                            </div>

                            @if(session('status') === 'company-settings-updated')
                                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                                    <i class="mdi mdi-check-circle me-2"></i> Company settings updated successfully!
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form method="post" action="{{ route('profile.company-settings.update') }}" enctype="multipart/form-data">
                                @csrf

                                <!-- Logo upload -->
                                <div class="mb-5">
                                    <label class="form-label d-block">Company Logo</label>
                                    <div class="d-flex align-items-center gap-4 flex-wrap">
                                        <div class="logo-preview-wrap" id="logoPreviewWrap">
                                            @if($companyLogoUrl)
                                                <img src="{{ $companyLogoUrl }}" id="logoPreview" alt="Company Logo">
                                            @else
                                                <img src="{{ asset('logo.png') }}" id="logoPreview" alt="Current Logo" style="opacity:0.4;">
                                            @endif
                                        </div>
                                        <div>
                                            <label for="company_logo" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold" style="cursor:pointer;">
                                                <i class="mdi mdi-upload me-1"></i> Choose Logo
                                            </label>
                                            <input type="file" id="company_logo" name="company_logo" class="d-none @error('company_logo') is-invalid @enderror" accept="image/*">
                                            <div class="text-muted small mt-2">PNG, JPG, GIF, WebP or SVG · Max 2 MB<br>Recommended: transparent background, at least 200×60px</div>
                                            @if($companyLogoUrl)
                                                <div class="mt-2">
                                                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">
                                                        <i class="mdi mdi-check me-1"></i> Custom logo active
                                                    </span>
                                                </div>
                                            @endif
                                            @error('company_logo')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    {{-- Row 1: Company Name + Company Email --}}
                                    <div class="col-md-6">
                                        <label for="company_name" class="form-label fw-semibold">Company Name</label>
                                        <input type="text" id="company_name" name="company_name"
                                            class="form-control @error('company_name') is-invalid @enderror"
                                            value="{{ old('company_name', $companyName) }}"
                                            placeholder="e.g. Elite Guard Inc.">
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="company_email" class="form-label fw-semibold">
                                            Company Email
                                            <span class="badge rounded-pill ms-1 px-2" style="font-size:0.65rem;font-weight:500;background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;">&#128274; Master Admin</span>
                                        </label>
                                        <input type="email" id="company_email"
                                            class="form-control"
                                            style="background:#f8fafc;color:#64748b;cursor:not-allowed;"
                                            value="{{ $companyEmail }}" disabled>
                                    </div>

                                    {{-- Row 2: Company Phone + Website --}}
                                    <div class="col-md-6">
                                        <label for="company_phone" class="form-label fw-semibold">
                                            Company Phone
                                            <span class="badge rounded-pill ms-1 px-2" style="font-size:0.65rem;font-weight:500;background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;">&#128274; Master Admin</span>
                                        </label>
                                        <input type="text" id="company_phone"
                                            class="form-control"
                                            style="background:#f8fafc;color:#64748b;cursor:not-allowed;"
                                            value="{{ $companyPhone }}" disabled>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="company_website" class="form-label fw-semibold">Website</label>
                                        <input type="url" id="company_website" name="company_website"
                                            class="form-control @error('company_website') is-invalid @enderror"
                                            value="{{ old('company_website', $companyWebsite) }}"
                                            placeholder="https://example.com">
                                        @error('company_website')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Row 3: Full-width address --}}
                                    <div class="col-12">
                                        <label for="company_address" class="form-label fw-semibold">Company Address</label>
                                        <textarea id="company_address" name="company_address"
                                            class="form-control @error('company_address') is-invalid @enderror"
                                            rows="3" placeholder="123 Main Street, City, State, ZIP, Country">{{ old('company_address', $companyAddress) }}</textarea>
                                        @error('company_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-3 mt-5">
                                    <button type="submit" class="btn-save">
                                        <i class="mdi mdi-content-save me-1"></i> Save Company Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(Auth::user()->role === 'SuperAdmin')
            {{-- ===== SMTP SETTINGS TAB ===== --}}
            <div class="tab-pane fade {{ request('tab') === 'smtp' ? 'show active' : '' }}" id="pane-smtp" role="tabpanel">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card profile-card">
                            <div class="card-body p-4 p-md-5">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="p-2 rounded-3 me-3" style="background:rgba(124,58,237,0.12);width:44px;height:44px;display:flex;align-items:center;justify-content:center;">
                                        <i data-feather="mail" style="width:24px;height:24px;color:#7c3aed;"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0">SMTP Settings</h4>
                                        <p class="text-muted mb-0 small">Save outgoing email server credentials for this company. The password is encrypted before storage; these settings are not used to send email yet.</p>
                                    </div>
                                </div>

                                @if(session('status') === 'smtp-settings-updated')
                                    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                                        <i class="mdi mdi-check-circle me-2"></i> SMTP settings saved successfully.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form method="post" action="{{ route('profile.smtp-settings.update') }}">
                                    @csrf
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label for="smtp_host" class="form-label fw-semibold">SMTP Host</label>
                                            <input type="text" id="smtp_host" name="smtp_host" class="form-control @error('smtp_host') is-invalid @enderror" value="{{ old('smtp_host', $smtpSettings['host']) }}" placeholder="smtp.example.com" autocomplete="off" required>
                                            @error('smtp_host') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="smtp_port" class="form-label fw-semibold">Port</label>
                                            <input type="number" id="smtp_port" name="smtp_port" class="form-control @error('smtp_port') is-invalid @enderror" value="{{ old('smtp_port', $smtpSettings['port']) }}" min="1" max="65535" required>
                                            @error('smtp_port') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="smtp_encryption" class="form-label fw-semibold">Encryption</label>
                                            <select id="smtp_encryption" name="smtp_encryption" class="form-select @error('smtp_encryption') is-invalid @enderror" required>
                                                <option value="tls" {{ old('smtp_encryption', $smtpSettings['encryption']) === 'tls' ? 'selected' : '' }}>TLS / STARTTLS</option>
                                                <option value="ssl" {{ old('smtp_encryption', $smtpSettings['encryption']) === 'ssl' ? 'selected' : '' }}>SSL</option>
                                                <option value="none" {{ old('smtp_encryption', $smtpSettings['encryption']) === 'none' ? 'selected' : '' }}>None</option>
                                            </select>
                                            @error('smtp_encryption') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="smtp_username" class="form-label fw-semibold">SMTP Username</label>
                                            <input type="text" id="smtp_username" name="smtp_username" class="form-control @error('smtp_username') is-invalid @enderror" value="{{ old('smtp_username', $smtpSettings['username']) }}" autocomplete="off">
                                            @error('smtp_username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="smtp_password" class="form-label fw-semibold">SMTP Password</label>
                                            <input type="password" id="smtp_password" name="smtp_password" class="form-control @error('smtp_password') is-invalid @enderror" value="" placeholder="{{ $smtpSettings['password_configured'] ? 'Saved — leave blank to keep current password' : 'Enter SMTP password' }}" autocomplete="new-password">
                                            @error('smtp_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            @if($smtpSettings['password_configured'])
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" value="1" id="smtp_clear_password" name="smtp_clear_password">
                                                    <label class="form-check-label small text-muted" for="smtp_clear_password">Clear the saved SMTP password</label>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <label for="smtp_from_address" class="form-label fw-semibold">From Email Address</label>
                                            <input type="email" id="smtp_from_address" name="smtp_from_address" class="form-control @error('smtp_from_address') is-invalid @enderror" value="{{ old('smtp_from_address', $smtpSettings['from_address']) }}" placeholder="notifications@example.com" required>
                                            @error('smtp_from_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="smtp_from_name" class="form-label fw-semibold">From Name</label>
                                            <input type="text" id="smtp_from_name" name="smtp_from_name" class="form-control @error('smtp_from_name') is-invalid @enderror" value="{{ old('smtp_from_name', $smtpSettings['from_name']) }}" placeholder="Company name" required>
                                            @error('smtp_from_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button type="submit" class="btn-save">
                                            <i class="mdi mdi-content-save me-1"></i> Save SMTP Settings
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ===== TIMEZONE TAB ===== --}}
        <div class="tab-pane fade {{ request('tab') === 'timezone' ? 'show active' : '' }}" id="pane-timezone" role="tabpanel">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card profile-card">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="p-2 bg-info-subtle rounded-3 me-3 text-info" style="background: rgba(14, 165, 233, 0.15); width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                                    <i data-feather="clock" style="width: 24px; height: 24px;"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0">System Timezone Settings</h4>
                                    <p class="text-muted mb-0 small">Set the global system timezone for all web routes, shift schedules, and API endpoints.</p>
                                </div>
                            </div>

                            <form method="post" action="{{ route('profile.timezone.update') }}">
                                @csrf

                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="timezone" class="form-label">Select Active Timezone</label>
                                        <select id="timezone" name="timezone" class="form-select form-control select2 @error('timezone') is-invalid @enderror" required style="width: 100%;">
                                            @foreach($timezones as $tz)
                                                <option value="{{ $tz }}" {{ $currentTimezone === $tz ? 'selected' : '' }}>
                                                    {{ $tz }} ({{ \Carbon\Carbon::now($tz)->format('P \G\M\T') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('timezone')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text mt-2 text-muted">
                                            Current active system time: <strong class="text-primary">{{ \Carbon\Carbon::now()->format('d M Y, h:i:s A (T)') }}</strong>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-2">
                                        <button type="submit" class="btn-save px-4">Save Timezone</button>
                                    </div>
                                </div>

                                @if (session('status') === 'timezone-updated')
                                    <div class="mt-3 text-success small fw-semibold animate__animated animate__fadeIn">
                                        <i class="mdi mdi-check-circle me-1"></i> System Timezone updated successfully!
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /tab-content --}}
</div>
@endsection

@section('scripts')
<style>
    .select2-container--default .select2-selection--single {
        height: 48px !important;
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        display: flex !important;
        align-items: center !important;
        padding-left: 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
        right: 10px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #1e1b4b !important;
        font-weight: 500 !important;
    }
</style>
<script>
    $(document).ready(function() {
        $('#timezone').select2({
            placeholder: "Search and select timezone...",
            allowClear: false,
            width: '100%'
        });

        // Restore active tab from session status or hash
        @if(session('status') === 'company-settings-updated')
            var triggerEl = document.querySelector('#tab-settings');
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        @elseif(session('status') === 'smtp-settings-updated')
            var triggerEl = document.querySelector('#tab-smtp');
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        @elseif(session('status') === 'timezone-updated')
            var triggerEl = document.querySelector('#tab-timezone');
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        @elseif(session('status') === 'password-updated')
            var triggerEl = document.querySelector('#tab-security');
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        @endif

        // Logo file preview
        $('#company_logo').on('change', function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#logoPreview').attr('src', e.target.result).css('opacity', 1);
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endsection
