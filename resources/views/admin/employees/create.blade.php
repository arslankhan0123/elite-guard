@extends('dashboardLayouts.main')
@section('title', 'Add New Employee')

@section('breadcrumbTitle', 'Register Personnel')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
<li class="breadcrumb-item active">New Registration</li>
@endsection

@section('content')
<style>
    .form-card {
        border: none;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    .section-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%);
        color: white;
        padding: 20px 25px;
        border-radius: 20px 20px 0 0;
    }
    .form-label {
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
    }
    .form-control, .form-select {
        border-radius: 12px;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
    }
    .btn-submit {
        background: #7c3aed;
        color: white;
        border-radius: 12px;
        padding: 12px 40px;
        font-weight: 700;
        border: none;
        transition: all 0.3s;
    }
    .btn-submit:hover {
        background: #6d28d9;
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(109, 40, 217, 0.2);
    }
</style>

<form action="{{ route('employees.store') }}" method="POST">
    @csrf
    <div class="row g-4">
        <!-- Account Details -->
        <div class="col-lg-6">
            <div class="card form-card h-100">
                <div class="section-header">
                    <h5 class="mb-0 fw-bold"><i data-feather="lock" class="me-2"></i> Account Access</h5>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="mb-4">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. John Doe" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="john@example.com" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="role" class="form-label">Assigned Role</label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="Employee" selected>Employee / Guard</option>
                            <option value="Admin">Admin</option>
                            <option value="SuperAdmin">SuperAdmin</option>
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Details -->
        <div class="col-lg-6">
            <div class="card form-card h-100">
                <div class="section-header" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                    <h5 class="mb-0 fw-bold"><i data-feather="user" class="me-2"></i> Employee Profile</h5>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="phone" class="form-label">Contact Number</label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+1 234 567 890">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="cnic" class="form-label">CNIC / ID Number</label>
                            <input type="text" name="cnic" id="cnic" class="form-control @error('cnic') is-invalid @enderror" value="{{ old('cnic') }}" placeholder="xxxxx-xxxxxxx-x">
                            @error('cnic') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label">Permanent Address</label>
                        <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror" placeholder="123 Security St, Operations City">{{ old('address') }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="gender" class="form-label">Gender</label>
                            <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="joining_date" class="form-label">Joining Date</label>
                            <input type="date" name="joining_date" id="joining_date" class="form-control @error('joining_date') is-invalid @enderror" value="{{ old('joining_date', date('Y-m-d')) }}">
                            @error('joining_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 text-center mt-4">
            <button type="submit" class="btn-submit shadow-lg">
                <i data-feather="check-circle" class="me-2"></i> Confirm Registration
            </button>
            <div class="mt-3">
                <a href="{{ route('employees.index') }}" class="text-muted text-decoration-none small">
                    <i data-feather="arrow-left" style="width: 14px;"></i> Back to Directory
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
