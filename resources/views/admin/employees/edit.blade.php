@extends('dashboardLayouts.main')
@section('title', 'Edit Employee Profile')

@section('breadcrumbTitle', 'Update Personnel')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
<li class="breadcrumb-item active">Edit Profile</li>
@endsection

@section('content')
<style>
    .form-card {
        border: none;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .nav-tabs-custom {
        display: flex;
        overflow-x: auto;
        white-space: nowrap;
        background: #f8fafc;
        padding: 10px 15px;
        border-radius: 15px 15px 0 0;
        border-bottom: 2px solid #e2e8f0;
    }
    .nav-tabs-custom .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        padding: 12px 20px;
        border-radius: 10px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .nav-tabs-custom .nav-link.active {
        background: #7c3aed;
        color: white;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2);
    }
    .section-title {
        color: #1e293b;
        font-weight: 800;
        font-size: 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-title i {
        color: #7c3aed;
    }
    .form-label {
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }
    .form-control, .form-select {
        border-radius: 12px;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s;
        background-color: #fcfdfe;
    }
    .form-control:focus, .form-select:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
        background-color: #fff;
    }
    .btn-submit {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        color: white;
        border-radius: 12px;
        padding: 14px 45px;
        font-weight: 700;
        border: none;
        transition: all 0.3s;
        box-shadow: 0 10px 15px -3px rgba(109, 40, 217, 0.3);
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgba(109, 40, 217, 0.4);
    }
    .attach-label {
        display: block;
        padding: 15px;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: #f8fafc;
    }
    .attach-label:hover {
        border-color: #7c3aed;
        background: #f5f3ff;
    }
    .current-file {
        display: block;
        padding: 5px 10px;
        background: #f1f5f9;
        border-radius: 8px;
        font-size: 0.8rem;
        color: #475569;
        margin-top: 5px;
    }
</style>

<div class="row justify-content-center">
    <div class="col-xl-12">
        <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <div class="card form-card">
                <div class="nav nav-tabs nav-tabs-custom" id="employeeTabs" role="tablist">
                    <button class="nav-link active" id="part1-tab" data-bs-toggle="tab" data-bs-target="#part1" type="button" role="tab"><i data-feather="user"></i> Candidate Info</button>
                    <button class="nav-link" id="part2-tab" data-bs-toggle="tab" data-bs-target="#part2" type="button" role="tab"><i data-feather="credit-card"></i> Bank Details</button>
                    <button class="nav-link" id="part3-tab" data-bs-toggle="tab" data-bs-target="#part3" type="button" role="tab"><i data-feather="file-text"></i> Licenses</button>
                    <button class="nav-link" id="part4-tab" data-bs-toggle="tab" data-bs-target="#part4" type="button" role="tab"><i data-feather="calendar"></i> Availability</button>
                    <button class="nav-link" id="part5-tab" data-bs-toggle="tab" data-bs-target="#part5" type="button" role="tab"><i data-feather="briefcase"></i> Office Use</button>
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="tab-content" id="employeeTabsContent">
                        <!-- Part 1: Candidate Information -->
                        <div class="tab-pane fade show active" id="part1" role="tabpanel">
                            <h4 class="section-title"><i data-feather="user"></i> Part 1: Candidate Information</h4>
                            <div class="row g-4">
                                @php
                                    $candidate = $employee->user->candidate;
                                @endphp
                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $candidate->first_name ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $candidate->last_name ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email (System Login)</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $employee->user->email) }}" required>
                                    <small class="text-muted">Changes to login email may affect employee access.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Assigned Role</label>
                                    <select name="role" class="form-select" required>
                                        <option value="Employee" {{ $employee->user->role == 'Employee' ? 'selected' : '' }}>Employee / Guard</option>
                                        <option value="Admin" {{ $employee->user->role == 'Admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="SuperAdmin" {{ $employee->user->role == 'SuperAdmin' ? 'selected' : '' }}>SuperAdmin</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Change Password (Optional)</label>
                                    <input type="password" name="password" class="form-control">
                                    <small class="text-muted">Leave blank to keep current password.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control">
                                </div>
                                <hr class="my-4">
                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control" value="{{ old('dob', $candidate->dob ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Social Insurance Number (SIN)</label>
                                    <input type="text" name="sin" class="form-control" value="{{ old('sin', $candidate->sin ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $candidate->phone ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Personal Email Address</label>
                                    <input type="email" name="email_personal" class="form-control" value="{{ old('email_personal', $candidate->email ?? '') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Home Address</label>
                                    <input type="text" name="address" class="form-control" value="{{ old('address', $candidate->address ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city', $candidate->city ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Province</label>
                                    <input type="text" name="province" class="form-control" value="{{ old('province', $candidate->province ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $candidate->postal_code ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Emergency Contact Person Name</label>
                                    <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $candidate->emergency_contact_name ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Emergency Contact Phone Number</label>
                                    <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone', $candidate->emergency_contact_phone ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Part 2: Direct Deposit -->
                        <div class="tab-pane fade" id="part2" role="tabpanel">
                            <h4 class="section-title"><i data-feather="credit-card"></i> Part 2: Direct Deposit / E-transfer Info</h4>
                            @php
                                $bank = $employee->user->bankDetail;
                            @endphp
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Bank Name</label>
                                    <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $bank->bank_name ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Institution Number</label>
                                    <input type="text" name="institution_number" class="form-control" value="{{ old('institution_number', $bank->institution_number ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Transit Number</label>
                                    <input type="text" name="transit_number" class="form-control" value="{{ old('transit_number', $bank->transit_number ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Account Number</label>
                                    <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $bank->account_number ?? '') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Bank Address</label>
                                    <input type="text" name="bank_address" class="form-control" value="{{ old('bank_address', $bank->bank_address ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Interac e-Transfer Email</label>
                                    <input type="email" name="interac_email" class="form-control" value="{{ old('interac_email', $bank->interac_email ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Attach Void Cheque</label>
                                    <input type="file" name="void_cheque_file" id="void_cheque" class="d-none">
                                    <label for="void_cheque" class="attach-label">
                                        <i data-feather="upload-cloud" class="mb-2"></i>
                                        <p class="mb-0 small text-muted">Click to update Void Cheque (PDF/Image)</p>
                                    </label>
                                    @if($bank && $bank->void_cheque_file)
                                        <div class="mt-2 d-flex flex-wrap gap-2">
                                            @php
                                                $voidFiles = is_array($bank->void_cheque_file) ? $bank->void_cheque_file : [$bank->void_cheque_file];
                                                $ordinals = ['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh', 'Eighth', 'Ninth', 'Tenth'];
                                            @endphp
                                            @foreach($voidFiles as $index => $file)
                                                @php
                                                    $url = filter_var($file, FILTER_VALIDATE_URL) ? $file : Storage::url($file);
                                                    $label = count($voidFiles) > 1 
                                                        ? "View " . ($ordinals[$index] ?? ($index + 1)) . " Void Cheque Document" 
                                                        : "View Void Cheque Document";
                                                @endphp
                                                <a href="{{ $url }}" target="_blank" class="current-file">
                                                    <i data-feather="file-text" style="width: 14px;"></i> 
                                                    {{ $label }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Part 3: License Information -->
                        <div class="tab-pane fade" id="part3" role="tabpanel">
                            <h4 class="section-title"><i data-feather="file-text"></i> Part 3: License Information</h4>
                            @php
                                $license = $employee->user->licenseDetail;
                            @endphp
                            <!-- Security License -->
                            <div class="p-3 mb-4 rounded bg-light border">
                                <h6 class="fw-bold mb-3"><i data-feather="shield" class="me-2 text-primary"></i> Security License Info</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">License Number</label>
                                        <input type="text" name="security_license_number" class="form-control" value="{{ $license->security_license_number ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Expiry Date</label>
                                        <input type="date" name="security_license_expiry" class="form-control" value="{{ $license->security_license_expiry ?? '' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Update Security License</label>
                                        <input type="file" name="security_license_file" id="security_file" class="d-none">
                                        <label for="security_file" class="attach-label">Click to update doc</label>
                                        @if($license && $license->security_license_file)
                                            <div class="mt-2 d-flex flex-wrap gap-2">
                                                @php
                                                    $securityFiles = is_array($license->security_license_file) ? $license->security_license_file : [$license->security_license_file];
                                                    $ordinals = ['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh', 'Eighth', 'Ninth', 'Tenth'];
                                                @endphp
                                                @foreach($securityFiles as $index => $file)
                                                    @php
                                                        $url = filter_var($file, FILTER_VALIDATE_URL) ? $file : Storage::url($file);
                                                        $label = count($securityFiles) > 1 
                                                            ? "View " . ($ordinals[$index] ?? ($index + 1)) . " Security License Document" 
                                                            : "View Security License Document";
                                                    @endphp
                                                    <a href="{{ $url }}" target="_blank" class="current-file">
                                                        <i data-feather="shield" style="width: 14px;"></i> 
                                                        {{ $label }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Driver's License -->
                            <div class="p-3 mb-4 rounded bg-light border">
                                <h6 class="fw-bold mb-3"><i data-feather="truck" class="me-2 text-primary"></i> Driver's License Info</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">License Number</label>
                                        <input type="text" name="drivers_license_number" class="form-control" value="{{ $license->drivers_license_number ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Expiry Date</label>
                                        <input type="date" name="drivers_license_expiry" class="form-control" value="{{ $license->drivers_license_expiry ?? '' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Update Driver's License</label>
                                        <input type="file" name="drivers_license_file" id="drivers_file" class="d-none">
                                        <label for="drivers_file" class="attach-label">Click to update doc</label>
                                        @if($license && $license->drivers_license_file)
                                            <div class="mt-2 d-flex flex-wrap gap-2">
                                                @php
                                                    $driversFiles = is_array($license->drivers_license_file) ? $license->drivers_license_file : [$license->drivers_license_file];
                                                    $ordinals = ['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh', 'Eighth', 'Ninth', 'Tenth'];
                                                @endphp
                                                @foreach($driversFiles as $index => $file)
                                                    @php
                                                        $url = filter_var($file, FILTER_VALIDATE_URL) ? $file : Storage::url($file);
                                                        $label = count($driversFiles) > 1 
                                                            ? "View " . ($ordinals[$index] ?? ($index + 1)) . " Driver's License Document" 
                                                            : "View Driver's License Document";
                                                    @endphp
                                                    <a href="{{ $url }}" target="_blank" class="current-file">
                                                        <i data-feather="truck" style="width: 14px;"></i> 
                                                        {{ $label }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Work Eligibility -->
                            <div class="p-3 mb-4 rounded bg-light border">
                                <h6 class="fw-bold mb-3"><i data-feather="globe" class="me-2 text-primary"></i> Work Eligibility</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Document Type and Number</label>
                                        <input type="text" name="work_eligibility_type_number" class="form-control" value="{{ $license->work_eligibility_type_number ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Expiry Date</label>
                                        <input type="date" name="work_eligibility_expiry" class="form-control" value="{{ $license->work_eligibility_expiry ?? '' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Update Work Permit / PR Card</label>
                                        <input type="file" name="work_eligibility_file" id="work_file" class="d-none">
                                        <label for="work_file" class="attach-label">Click to update doc</label>
                                        @if($license && $license->work_eligibility_file)
                                            <div class="mt-2 d-flex flex-wrap gap-2">
                                                @php
                                                    $workFiles = is_array($license->work_eligibility_file) ? $license->work_eligibility_file : [$license->work_eligibility_file];
                                                    $ordinals = ['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh', 'Eighth', 'Ninth', 'Tenth'];
                                                @endphp
                                                @foreach($workFiles as $index => $file)
                                                    @php
                                                        $url = filter_var($file, FILTER_VALIDATE_URL) ? $file : Storage::url($file);
                                                        $label = count($workFiles) > 1 
                                                            ? "View " . ($ordinals[$index] ?? ($index + 1)) . " Work Eligibility Document" 
                                                            : "View Work Eligibility Document";
                                                    @endphp
                                                    <a href="{{ $url }}" target="_blank" class="current-file">
                                                        <i data-feather="globe" style="width: 14px;"></i> 
                                                        {{ $label }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Others -->
                            <div class="p-3 rounded bg-light border">
                                <h6 class="fw-bold mb-3"><i data-feather="more-horizontal" class="me-2 text-primary"></i> Others</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Validate Criminal Record Check</label>
                                        <select name="criminal_record_check" class="form-select">
                                            <option value="Valid" {{ ($license->criminal_record_check ?? '') == 'Valid' ? 'selected' : '' }}>Valid</option>
                                            <option value="In Progress" {{ ($license->criminal_record_check ?? '') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="Not Started" {{ ($license->criminal_record_check ?? '') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">First Aid Training Course</label>
                                        <input type="text" name="first_aid_training" class="form-control" value="{{ $license->first_aid_training ?? '' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Other Certificates if Any</label>
                                        <textarea name="other_certificates" class="form-control" rows="2">{{ $license->other_certificates ?? '' }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Attach Documents</label>
                                        <input type="file" name="other_documents_file" id="other_docs_file" class="d-none">
                                        <label for="other_docs_file" class="attach-label">Click to upload docs</label>
                                        @if($license && $license->other_documents_file)
                                            <div class="mt-2 d-flex flex-wrap gap-2">
                                                @php
                                                    $otherFiles = is_array($license->other_documents_file) ? $license->other_documents_file : [$license->other_documents_file];
                                                    $ordinals = ['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh', 'Eighth', 'Ninth', 'Tenth'];
                                                @endphp
                                                @foreach($otherFiles as $index => $file)
                                                    @php
                                                        $url = filter_var($file, FILTER_VALIDATE_URL) ? $file : Storage::url($file);
                                                        $label = count($otherFiles) > 1 
                                                            ? "View " . ($ordinals[$index] ?? ($index + 1)) . " Other Certificate" 
                                                            : "View Other Certificate";
                                                    @endphp
                                                    <a href="{{ $url }}" target="_blank" class="current-file">
                                                        <i data-feather="file" style="width: 14px;"></i> 
                                                        {{ $label }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Part 4: Availability -->
                        <div class="tab-pane fade" id="part4" role="tabpanel">
                            <h4 class="section-title"><i data-feather="calendar"></i> Part 4: Availability</h4>
                            @php
                                $availability = $employee->user->availability;
                            @endphp
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Availability Date</label>
                                    <input type="date" name="availability_date" class="form-control" value="{{ $availability->availability_date ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Hours employee willing to work</label>
                                    <input type="text" name="willing_hours" class="form-control" value="{{ $availability->willing_hours ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Specific Hours employee unable to work</label>
                                    <textarea name="unable_hours" class="form-control" rows="2">{{ $availability->unable_hours ?? '' }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Days of week employee unable to work</label>
                                    <textarea name="unable_days" class="form-control" rows="2">{{ $availability->unable_days ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Part 5: Office Use Only -->
                        <div class="tab-pane fade" id="part5" role="tabpanel">
                            <h4 class="section-title"><i data-feather="briefcase"></i> Part 5: Office Use Only</h4>
                            @php
                                $office = $employee->user->officeDetail;
                            @endphp
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label d-block">Employment Type</label>
                                    <div class="d-flex gap-4 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="employment_type" id="fullTime" value="Full Time" {{ ($office->employment_type ?? '') == 'Full Time' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="fullTime">Full Time</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="employment_type" id="partTime" value="Part Time" {{ ($office->employment_type ?? '') == 'Part Time' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="partTime">Part Time</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="employment_type" id="casual" value="Casual" {{ ($office->employment_type ?? '') == 'Casual' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="casual">Casual</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ $office->start_date ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Job Position</label>
                                    <input type="text" name="job_position" class="form-control" value="{{ $office->job_position ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Wage</label>
                                    <input type="text" name="wage" class="form-control" value="{{ $office->wage ?? '' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Other Notes</label>
                                    <textarea name="other_notes" class="form-control" rows="3">{{ $office->other_notes ?? '' }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Hiring Manager Name</label>
                                    <input type="text" name="hiring_manager_name" class="form-control" value="{{ $office->hiring_manager_name ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Hiring Manager Signature (Initials)</label>
                                    <input type="text" name="hiring_manager_signature" class="form-control" value="{{ $office->hiring_manager_signature ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 text-center">
                        <button type="submit" class="btn-submit">
                            <i data-feather="save" class="me-2"></i> Update Profile
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // File input label update
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                let fileName = e.target.files[0].name;
                let label = document.querySelector(`label[for="${e.target.id}"]`);
                label.innerHTML = `<i data-feather="check" class="text-success me-2"></i> ${fileName}`;
                feather.replace();
            });
        });
    });
</script>
@endsection
