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

    <div class="row">
        <!-- Update Profile Information -->
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

        <!-- Update Password -->
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
    <!-- System Timezone Settings -->
    <div class="row mt-2 mb-4">
        <div class="col-12">
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

    <!-- Danger Zone -->
    <div class="row mt-2">
        <div class="col-12 mb-5">
            <div class="card profile-card border-danger-subtle" style="background: #fffafa;">
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
    });
</script>
@endsection
