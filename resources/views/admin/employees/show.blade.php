@extends('dashboardLayouts.main')
@section('title', 'Employee Details')

@section('breadcrumbTitle', 'Employee Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
    <li class="breadcrumb-item active">View Details</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0">Employee Profile: {{ $employee->user->name }}</h4>
                <a href="{{ route('employees.index') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="mdi mdi-arrow-left"></i> Back to Directory
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Basic User Info -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h5 class="card-title text-white mb-0"><i class="mdi mdi-account-circle-outline"></i> Basic Info</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong class="text-muted d-block">Name</strong>
                        <span>{{ $employee->user->name }}</span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted d-block">Email</strong>
                        <span>{{ $employee->user->email }}</span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted d-block">Role</strong>
                        <span class="badge rounded-pill {{ $employee->user->role == 'SuperAdmin' ? 'bg-danger' : 'bg-info' }}">{{ $employee->user->role }}</span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted d-block">Status</strong>
                        @if($employee->status)
                            <span class="badge bg-success rounded-pill">Active</span>
                        @else
                            <span class="badge bg-secondary rounded-pill">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Availability -->
            @if($employee->user->availability)
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-info text-white rounded-top-4">
                    <h5 class="card-title text-white mb-0"><i class="mdi mdi-calendar-clock"></i> Availability</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Date:</strong> {{ $employee->user->availability->availability_date ?? 'N/A' }}</div>
                    <div class="mb-2"><strong>Willing Hours:</strong> {{ $employee->user->availability->willing_hours ?? 'N/A' }}</div>
                    <div class="mb-2"><strong>Unable Hours:</strong> {{ $employee->user->availability->unable_hours ?? 'N/A' }}</div>
                    <div class="mb-2"><strong>Unable Days:</strong> {{ $employee->user->availability->unable_days ?? 'N/A' }}</div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-8">
            <ul class="nav nav-pills mb-3 gap-2" id="employee-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill fw-bold" id="candidate-tab" data-bs-toggle="pill" data-bs-target="#candidate" type="button" role="tab"><i class="mdi mdi-card-account-details-outline"></i> Personal Details</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-bold" id="license-tab" data-bs-toggle="pill" data-bs-target="#license" type="button" role="tab"><i class="mdi mdi-certificate-outline"></i> Licenses & Documents</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-bold" id="bank-tab" data-bs-toggle="pill" data-bs-target="#bank" type="button" role="tab"><i class="mdi mdi-bank-outline"></i> Bank Details</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-bold" id="office-tab" data-bs-toggle="pill" data-bs-target="#office" type="button" role="tab"><i class="mdi mdi-office-building-outline"></i> Office Use & Offer</button>
                </li>
            </ul>

            <div class="tab-content" id="employee-tabContent">
                <!-- Personal Details Tab -->
                <div class="tab-pane fade show active" id="candidate" role="tabpanel">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <h5 class="border-bottom pb-2 text-primary mb-3">Personal & Contact Details</h5>
                            @if($employee->user->candidate)
                                <div class="row">
                                    <div class="col-md-6 mb-3"><strong>First Name:</strong> <br> {{ $employee->user->candidate->first_name }}</div>
                                    <div class="col-md-6 mb-3"><strong>Last Name:</strong> <br> {{ $employee->user->candidate->last_name }}</div>
                                    <div class="col-md-6 mb-3"><strong>Designation:</strong> <br> {{ $employee->user->candidate->designation ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Date of Birth:</strong> <br> {{ $employee->user->candidate->dob ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>SIN:</strong> <br> {{ $employee->user->candidate->sin ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Phone:</strong> <br> {{ $employee->user->candidate->phone ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Personal Email:</strong> <br> {{ $employee->user->candidate->email ?? 'N/A' }}</div>
                                    <div class="col-md-12 mb-3"><strong>Address:</strong> <br> {{ $employee->user->candidate->address }}, {{ $employee->user->candidate->city }}, {{ $employee->user->candidate->province }} {{ $employee->user->candidate->postal_code }}</div>
                                    <div class="col-12 mt-3">
                                        <h6 class="text-primary border-bottom pb-1">Emergency Contact</h6>
                                    </div>
                                    <div class="col-md-6 mb-3"><strong>Name:</strong> <br> {{ $employee->user->candidate->emergency_contact_name ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Phone:</strong> <br> {{ $employee->user->candidate->emergency_contact_phone ?? 'N/A' }}</div>
                                </div>
                            @else
                                <p class="text-muted">No personal details available.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Licenses Tab -->
                <div class="tab-pane fade" id="license" role="tabpanel">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <h5 class="border-bottom pb-2 text-primary mb-3">Licenses & Certifications</h5>
                            @if($employee->user->licenseDetail)
                                <div class="row">
                                    <div class="col-md-6 mb-3"><strong>Security License #:</strong> <br> {{ $employee->user->licenseDetail->security_license_number ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Expiry:</strong> <br> {{ $employee->user->licenseDetail->security_license_expiry ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Driver's License #:</strong> <br> {{ $employee->user->licenseDetail->drivers_license_number ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Expiry:</strong> <br> {{ $employee->user->licenseDetail->drivers_license_expiry ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Work Eligibility:</strong> <br> {{ $employee->user->licenseDetail->work_eligibility_type_number ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Expiry:</strong> <br> {{ $employee->user->licenseDetail->work_eligibility_expiry ?? 'N/A' }}</div>
                                    
                                    <div class="col-md-4 mb-3"><strong>First Aid Training:</strong> <br> 
                                        <span class="badge {{ $employee->user->licenseDetail->first_aid_training ? 'bg-success' : 'bg-danger' }}">{{ $employee->user->licenseDetail->first_aid_training ? 'Yes' : 'No' }}</span>
                                    </div>
                                    <div class="col-md-4 mb-3"><strong>Criminal Record Check:</strong> <br> 
                                        <span class="badge {{ $employee->user->licenseDetail->criminal_record_check ? 'bg-success' : 'bg-danger' }}">{{ $employee->user->licenseDetail->criminal_record_check ? 'Yes' : 'No' }}</span>
                                    </div>
                                    <div class="col-md-4 mb-3"><strong>Other Certificates:</strong> <br> {{ $employee->user->licenseDetail->other_certificates ?? 'N/A' }}</div>
                                </div>
                                
                                <h6 class="text-primary border-bottom pb-1 mt-4">Attached Documents</h6>
                                <div class="row mt-3">
                                    @php
                                        $filesToDisplay = [
                                            'Security License' => $employee->user->licenseDetail->security_license_file,
                                            'Driver\'s License' => $employee->user->licenseDetail->drivers_license_file,
                                            'Work Eligibility' => $employee->user->licenseDetail->work_eligibility_file,
                                            'Other Documents' => $employee->user->licenseDetail->other_documents_file,
                                        ];
                                    @endphp
                                    @foreach($filesToDisplay as $label => $files)
                                        @php
                                            if (is_string($files)) {
                                                $decoded = json_decode($files, true);
                                                $files = json_last_error() === JSON_ERROR_NONE ? $decoded : [$files];
                                            }
                                        @endphp
                                        @if($files && count((array)$files) > 0)
                                            <div class="col-md-6 mb-3">
                                                <strong>{{ $label }}:</strong>
                                                <ul class="list-unstyled mt-1">
                                                @foreach((array)$files as $file)
                                                    @if($file && is_string($file))
                                                        @php
                                                            $url = filter_var($file, FILTER_VALIDATE_URL) ? $file : \Illuminate\Support\Facades\Storage::url($file);
                                                        @endphp
                                                        <li><a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary mb-1"><i class="mdi mdi-download"></i> View File</a></li>
                                                    @endif
                                                @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No licenses or certifications available.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Bank Tab -->
                <div class="tab-pane fade" id="bank" role="tabpanel">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <h5 class="border-bottom pb-2 text-primary mb-3">Banking Information</h5>
                            @if($employee->user->bankDetail)
                                <div class="row">
                                    <div class="col-md-6 mb-3"><strong>Bank Name:</strong> <br> {{ $employee->user->bankDetail->bank_name ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Institution Number:</strong> <br> {{ $employee->user->bankDetail->institution_number ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Transit Number:</strong> <br> {{ $employee->user->bankDetail->transit_number ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Account Number:</strong> <br> {{ $employee->user->bankDetail->account_number ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Interac Email:</strong> <br> {{ $employee->user->bankDetail->interac_email ?? 'N/A' }}</div>
                                    <div class="col-md-12 mb-3"><strong>Bank Address:</strong> <br> {{ $employee->user->bankDetail->bank_address ?? 'N/A' }}</div>
                                </div>
                                @php
                                    $bankFiles = $employee->user->bankDetail->void_cheque_file;
                                    if (is_string($bankFiles)) {
                                        $decoded = json_decode($bankFiles, true);
                                        $bankFiles = json_last_error() === JSON_ERROR_NONE ? $decoded : [$bankFiles];
                                    }
                                @endphp
                                @if($bankFiles && count((array)$bankFiles) > 0)
                                    <h6 class="text-primary border-bottom pb-1 mt-3">Void Cheques</h6>
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @foreach((array)$bankFiles as $file)
                                            @if($file && is_string($file))
                                                @php
                                                    $url = filter_var($file, FILTER_VALIDATE_URL) ? $file : \Illuminate\Support\Facades\Storage::url($file);
                                                @endphp
                                                <a href="{{ $url }}" target="_blank" class="btn btn-outline-primary"><i class="mdi mdi-download"></i> View Void Cheque</a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <p class="text-muted">No banking information available.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Office Tab -->
                <div class="tab-pane fade" id="office" role="tabpanel">
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-body">
                            <h5 class="border-bottom pb-2 text-primary mb-3">Office Use Details</h5>
                            @if($employee->user->officeDetail)
                                <div class="row">
                                    <div class="col-md-6 mb-3"><strong>Employment Type:</strong> <br> {{ $employee->user->officeDetail->employment_type ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Start Date:</strong> <br> {{ $employee->user->officeDetail->start_date ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Job Position:</strong> <br> {{ $employee->user->officeDetail->job_position ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Wage:</strong> <br> {{ $employee->user->officeDetail->wage ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Hiring Manager:</strong> <br> {{ $employee->user->officeDetail->hiring_manager_name ?? 'N/A' }}</div>
                                    <div class="col-12 mb-3"><strong>Notes:</strong> <br> {{ $employee->user->officeDetail->other_notes ?? 'N/A' }}</div>
                                </div>
                            @else
                                <p class="text-muted">No office details available.</p>
                            @endif
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <h5 class="border-bottom pb-2 text-primary mb-3">Offer Letter Information</h5>
                            @if($employee->user->offerLetter)
                                <div class="row">
                                    <div class="col-md-6 mb-3"><strong>Job Title:</strong> <br> {{ $employee->user->offerLetter->job_title ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Joining Date:</strong> <br> {{ $employee->user->offerLetter->joining_date ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Salary:</strong> <br> {{ $employee->user->offerLetter->salary ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-3"><strong>Status:</strong> <br> 
                                        @if($employee->user->offerLetter->is_accepted)
                                            <span class="badge bg-success">Accepted on {{ $employee->user->offerLetter->signed_at }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </div>
                                    <div class="col-12 mb-3"><strong>Description:</strong> <br> <p class="mt-1">{{ $employee->user->offerLetter->description ?? 'N/A' }}</p></div>
                                </div>
                            @else
                                <p class="text-muted">No offer letter created.</p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
