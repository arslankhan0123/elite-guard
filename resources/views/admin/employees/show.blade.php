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
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <strong class="text-muted">Profile complete</strong>
                            <strong>{{ $profileCompletion['percentage'] }}%</strong>
                        </div>
                        <div class="progress" style="height: 9px;">
                            <div class="progress-bar bg-success" style="width: {{ $profileCompletion['percentage'] }}%" role="progressbar" aria-valuenow="{{ $profileCompletion['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
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

            <!-- Office Use & Offer Letter Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-secondary text-white rounded-top-4">
                    <h5 class="card-title text-white mb-0"><i class="mdi mdi-office-building-outline"></i> Office Use & Offer Letter</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 border-bottom pb-2 mb-3">
                            <h6 class="text-primary mb-2">Office Details</h6>
                            @if($employee->user->officeDetail)
                                <div class="row">
                                    <div class="col-md-6 mb-2"><strong>Employment Type:</strong> {{ $employee->user->officeDetail->employment_type ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-2"><strong>Start Date:</strong> {{ $employee->user->officeDetail->start_date ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-2"><strong>Job Position:</strong> {{ $employee->user->officeDetail->job_position ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-2"><strong>Wage:</strong> {{ $employee->user->officeDetail->wage ?? 'N/A' }}</div>
                                    <div class="col-12 mb-2"><strong>Hiring Manager:</strong> {{ $employee->user->officeDetail->hiring_manager_name ?? 'N/A' }}</div>
                                    <div class="col-12 mb-2"><strong>Notes:</strong> <br>{{ $employee->user->officeDetail->other_notes ?? 'N/A' }}</div>
                                </div>
                            @else
                                <p class="text-muted">No office details available.</p>
                            @endif
                        </div>
                        
                        <div class="col-12 mt-2">
                            <h6 class="text-primary mb-2">Offer Letter</h6>
                            @if($employee->user->offerLetter)
                                <div class="row">
                                    <div class="col-md-6 mb-2"><strong>Job Title:</strong> {{ $employee->user->offerLetter->job_title ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-2"><strong>Joining Date:</strong> {{ $employee->user->offerLetter->joining_date ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-2"><strong>Salary:</strong> {{ $employee->user->offerLetter->salary ?? 'N/A' }}</div>
                                    <div class="col-md-6 mb-2"><strong>Status:</strong> 
                                        @if($employee->user->offerLetter->is_accepted)
                                            <span class="badge bg-success">Accepted on {{ $employee->user->offerLetter->signed_at }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </div>
                                    <div class="col-12 mb-2">
                                        <strong>Description:</strong> 
                                        <p class="small text-muted mb-0 mt-1">
                                            @if($employee->user->offerLetter && $employee->user->offerLetter->description)
                                                @php
                                                    $desc = $employee->user->offerLetter->description;
                                                    $shouldTruncate = strlen($desc) > 50;
                                                @endphp
                                                @if($shouldTruncate)
                                                    <span id="desc-short">{{ Str::limit($desc, 50, '') }}</span>
                                                    <span id="desc-full" class="d-none">{{ $desc }}</span>
                                                    <a href="javascript:void(0);" id="desc-toggle" class="text-primary fw-bold ms-1" onclick="toggleDescription()">See more</a>
                                                    <script>
                                                        function toggleDescription() {
                                                            var shortSpan = document.getElementById('desc-short');
                                                            var fullSpan = document.getElementById('desc-full');
                                                            var toggleBtn = document.getElementById('desc-toggle');
                                                            if (fullSpan.classList.contains('d-none')) {
                                                                fullSpan.classList.remove('d-none');
                                                                shortSpan.classList.add('d-none');
                                                                toggleBtn.innerText = 'See less';
                                                            } else {
                                                                fullSpan.classList.add('d-none');
                                                                shortSpan.classList.remove('d-none');
                                                                toggleBtn.innerText = 'See more';
                                                            }
                                                        }
                                                    </script>
                                                @else
                                                    <span>{{ $desc }}</span>
                                                @endif
                                            @else
                                                <span>N/A</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted">No offer letter created.</p>
                            @endif
                        </div>
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

            <!-- Pay Slips Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-dark text-white rounded-top-4">
                    <h5 class="card-title text-white mb-0"><i class="mdi mdi-cash-multiple"></i> Employee Pay Slips</h5>
                </div>
                <div class="card-body">
                    @if($employee->user->paySlips && $employee->user->paySlips->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Month</th>
                                        <th>Year</th>
                                        <th>Uploaded On</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employee->user->paySlips->sortByDesc('year')->sortByDesc('month') as $slip)
                                        <tr>
                                            <td>{{ date('F', mktime(0, 0, 0, $slip->month, 1)) }}</td>
                                            <td>{{ $slip->year }}</td>
                                            <td>{{ $slip->created_at->format('j M Y') }}</td>
                                            <td>
                                                <a href="{{ $slip->file_path }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="mdi mdi-download"></i> View Pay Slip</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No pay slips have been uploaded for this employee yet.</p>
                    @endif
                </div>
            </div>

            <!-- Policies Status Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-warning text-dark rounded-top-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-dark mb-0"><i class="mdi mdi-file-document-outline"></i> Company Policies Status</h5>
                    <span class="badge bg-dark">{{ $employee->user->signedPolicies->count() }} / {{ $allPolicies->count() }} Signed</span>
                </div>
                <div class="card-body">
                    @if($allPolicies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Policy Title / Type</th>
                                        <th>Status</th>
                                        <th>Signed Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allPolicies as $policy)
                                        @php
                                            $signed = $employee->user->signedPolicies->firstWhere('policy_id', $policy->id);
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong class="text-dark">{{ $policy->type }}</strong>
                                                @if($policy->description)
                                                    <div class="small text-muted text-truncate" style="max-width: 300px;">{{ $policy->description }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($signed)
                                                    <span class="badge bg-success rounded-pill px-3">Signed</span>
                                                @else
                                                    <span class="badge bg-danger rounded-pill px-3">Pending Signature</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $signed ? $signed->created_at->format('j M Y, g:i A') : 'N/A' }}
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @if($policy->document)
                                                        <a href="{{ $policy->document }}" target="_blank" class="btn btn-xs btn-outline-secondary" title="View Template"><i class="mdi mdi-eye"></i> View Template</a>
                                                    @endif
                                                    @if($signed && $signed->document)
                                                        <a href="{{ $signed->document }}" target="_blank" class="btn btn-xs btn-outline-success" title="Download Signed"><i class="mdi mdi-download-box"></i> Download Signed</a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No active policies configured in the system.</p>
                    @endif
                </div>
            </div>

            <!-- Tax Documents Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-danger text-white rounded-top-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-white mb-0"><i class="mdi mdi-file-percent"></i> Tax Documents</h5>
                    <span class="badge bg-white text-danger">{{ $employee->user->taxDocumentSubmissions->count() }} / {{ $allTaxDocs->count() }} Submitted</span>
                </div>
                <div class="card-body">
                    @if($allTaxDocs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Document Type</th>
                                        <th>Template</th>
                                        <th>Submission Status</th>
                                        <th>Submitted At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allTaxDocs as $taxDoc)
                                        @php
                                            $submission = $employee->user->taxDocumentSubmissions->firstWhere('tax_document_id', $taxDoc->id);
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong class="text-dark">{{ $taxDoc->type }}</strong>
                                            </td>
                                            <td>
                                                @if($taxDoc->file_path)
                                                    <a href="{{ $taxDoc->file_path }}" target="_blank" class="btn btn-xs btn-outline-secondary"><i class="mdi mdi-download"></i> Template</a>
                                                @else
                                                    <span class="text-muted">No template file</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($submission)
                                                    <span class="badge bg-success rounded-pill px-3">Submitted</span>
                                                @else
                                                    <span class="badge bg-warning text-dark rounded-pill px-3">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $submission ? $submission->created_at->format('j M Y, g:i A') : 'N/A' }}
                                            </td>
                                            <td>
                                                @if($submission && $submission->document_path)
                                                    <a href="{{ $submission->document_path }}" target="_blank" class="btn btn-sm btn-primary"><i class="mdi mdi-file-find"></i> View Submission</a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No tax documents configured in the system.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Personal & Contact Details Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h5 class="card-title text-white mb-0"><i class="mdi mdi-card-account-details-outline"></i> Personal & Contact Details</h5>
                </div>
                <div class="card-body">
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
                            <div class="col-12 mt-2">
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

            <!-- Licenses & Certifications Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-success text-white rounded-top-4">
                    <h5 class="card-title text-white mb-0"><i class="mdi mdi-certificate-outline"></i> Licenses & Certifications</h5>
                </div>
                <div class="card-body">
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
                        
                        <h6 class="text-primary border-bottom pb-1 mt-3">Attached Documents</h6>
                        <div class="row mt-2">
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

            <!-- Banking Information Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-info text-white rounded-top-4">
                    <h5 class="card-title text-white mb-0"><i class="mdi mdi-bank-outline"></i> Banking Information</h5>
                </div>
                <div class="card-body">
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

            <!-- Orientation Quiz Attempts Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-purple text-white rounded-top-4" style="background-color: #6f42c1;">
                    <h5 class="card-title text-white mb-0"><i class="mdi mdi-school-outline"></i> Orientation Quiz Attempts & Details</h5>
                </div>
                <div class="card-body">
                    @if($employee->user->orientationAttempts && $employee->user->orientationAttempts->count() > 0)
                        <div class="accordion" id="attemptsAccordion">
                            @foreach($employee->user->orientationAttempts->sortByDesc('created_at') as $index => $attempt)
                                <div class="accordion-item border rounded-3 mb-2 overflow-hidden shadow-xs">
                                    <h2 class="accordion-header" id="headingAttempt{{ $attempt->id }}">
                                        <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAttempt{{ $attempt->id }}" aria-expanded="false" aria-controls="collapseAttempt{{ $attempt->id }}">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between w-100 pe-3 gap-2">
                                                <div>
                                                    <span class="fw-bold text-dark">Orientation: {{ $attempt->orientation->type ?? 'General' }}</span>
                                                    <span class="text-muted ms-2 small">({{ $attempt->created_at->format('j M Y, g:i A') }})</span>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <span class="badge bg-secondary">Score: {{ round($attempt->score, 2) }}%</span>
                                                    @if($attempt->is_passed)
                                                        <span class="badge bg-success"><i class="mdi mdi-checkbox-marked-circle-outline"></i> Passed</span>
                                                    @else
                                                        <span class="badge bg-danger"><i class="mdi mdi-alert-circle-outline"></i> Failed</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapseAttempt{{ $attempt->id }}" class="accordion-collapse collapse" aria-labelledby="headingAttempt{{ $attempt->id }}" data-bs-parent="#attemptsAccordion">
                                        <div class="accordion-body bg-light">
                                            <h6 class="border-bottom pb-2 mb-3 text-secondary">Attempt Quiz Detail Breakdown:</h6>
                                            
                                            @php
                                                $attemptQuestionIds = collect($attempt->answers)->pluck('question_id')->filter()->all();
                                                $currentQuestionIds = $attempt->orientation ? $attempt->orientation->questions->pluck('id')->all() : [];
                                                $hasMatchingQuestions = !empty($attemptQuestionIds) && count(array_intersect($attemptQuestionIds, $currentQuestionIds)) > 0;
                                            @endphp
                                            
                                            @if(!$hasMatchingQuestions && !empty($attemptQuestionIds))
                                                <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-0">
                                                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                                                    <strong>Historical Attempt Notice:</strong> The quiz questions for this orientation have been updated or recreated since this attempt was submitted. The detailed question-by-question breakdown is no longer available for this attempt.
                                                </div>
                                            @elseif($attempt->orientation && $attempt->orientation->questions->count() > 0)
                                                <div class="list-group list-group-flush rounded-3">
                                                    @foreach($attempt->orientation->questions as $qIdx => $question)
                                                        @php
                                                            // Find user's answer for this question
                                                            $userAnswer = collect($attempt->answers)->first(function($ans) use ($question) {
                                                                return isset($ans['question_id']) && $ans['question_id'] == $question->id;
                                                            });
                                                            $selectedOption = null;
                                                            $isUserCorrect = false;
                                                            
                                                            if ($userAnswer && isset($userAnswer['option_id'])) {
                                                                $selectedOption = $question->options->firstWhere('id', $userAnswer['option_id']);
                                                                if ($selectedOption && $selectedOption->is_correct) {
                                                                    $isUserCorrect = true;
                                                                }
                                                            }
                                                            $correctOption = $question->options->firstWhere('is_correct', true);
                                                        @endphp
                                                        
                                                        <div class="list-group-item bg-white border-bottom p-3 mb-2 rounded shadow-xs">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <h6 class="mb-0 text-dark">Q{{ $qIdx + 1 }}: {{ $question->question_text }}</h6>
                                                                @if($isUserCorrect)
                                                                    <span class="badge bg-success-light text-success border border-success-subtle"><i class="mdi mdi-check"></i> Correct</span>
                                                                @else
                                                                    <span class="badge bg-danger-light text-danger border border-danger-subtle"><i class="mdi mdi-close"></i> Incorrect</span>
                                                                @endif
                                                            </div>
                                                            
                                                            <div class="ps-2 py-1">
                                                                <span class="small text-muted">User's Selected Answer:</span> <br>
                                                                <span class="fw-semibold {{ $isUserCorrect ? 'text-success' : 'text-danger' }}">
                                                                    @if($selectedOption)
                                                                        {{ $selectedOption->option_text }}
                                                                    @else
                                                                        <span class="fst-italic text-muted">No Option Selected</span>
                                                                    @endif
                                                                </span>
                                                            </div>
                                                            
                                                            @if(!$isUserCorrect && $correctOption)
                                                                <div class="ps-2 py-1 border-top mt-2">
                                                                    <span class="small text-muted">Correct Answer:</span> <br>
                                                                    <span class="fw-semibold text-success">{{ $correctOption->option_text }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted mb-0">No questions or answers detail available for this orientation.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No quiz orientation attempts recorded for this employee yet.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
