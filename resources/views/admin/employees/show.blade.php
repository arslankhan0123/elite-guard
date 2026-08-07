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
        <!-- Page Header Banner -->
        <div class="col-12 mb-4">
            @php
                $name = $employee->user->name ?? 'Employee';
                $words = explode(' ', $name);
                $initials = '';
                if (count($words) >= 2) {
                    $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                } else {
                    $initials = strtoupper(substr($name, 0, 2));
                }
                $profilePicture = $employee->user->candidate->profile_picture ?? null;
            @endphp
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="card-body p-4 text-white">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="d-flex align-items-center">
                            <div class="position-relative me-3" style="width: 60px; height: 60px;">
                                @if($profilePicture)
                                    <img src="{{ $profilePicture }}" alt="{{ $name }}" class="rounded-circle border border-2 border-white position-absolute top-0 start-0" style="width: 60px; height: 60px; object-fit: cover; box-shadow: 0 4px 6px rgba(0,0,0,0.15); z-index: 2;" onerror="this.style.display='none'; document.getElementById('profile-initials').classList.remove('d-none');">
                                @endif
                                <div id="profile-initials" class="rounded-circle bg-white text-primary fw-bold d-flex align-items-center justify-content-center border border-2 border-white shadow-sm @if($profilePicture) d-none @endif" style="width: 60px; height: 60px; font-size: 1.4rem; box-shadow: 0 4px 6px rgba(0,0,0,0.15);">
                                    {{ $initials }}
                                </div>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-white">{{ $name }}</h4>
                                <div class="text-white rounded-pill px-3 py-1 mt-1 d-inline-block small" style="font-size: 0.8rem; background-color: rgba(255,255,255,0.2);">
                                    <i class="mdi mdi-briefcase-outline me-1"></i> {{ !empty($employee->user->candidate->designation) ? $employee->user->candidate->designation : 'Team Member' }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('employees.index') }}" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm text-primary transition-all">
                                <i class="mdi mdi-arrow-left me-1"></i> Back to Directory
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Left Column -->
        <div class="col-lg-4 col-md-5">
            <!-- Basic User Info -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-primary text-white py-3 rounded-top-4">
                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center">
                        <i class="mdi mdi-account-circle-outline me-2 mdi-24px text-white"></i> Basic Info
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary small fw-semibold">Profile Completion</span>
                            <span class="text-success fw-bold small">{{ $profileCompletion['percentage'] }}%</span>
                        </div>
                        <div class="progress" style="height: 6px; background-color: #f0f0f0;">
                            <div class="progress-bar bg-success rounded-pill" style="width: {{ $profileCompletion['percentage'] }}%" role="progressbar"></div>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted small">System ID</span>
                            <span class="text-dark fw-semibold">#{{ $employee->id }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted small">Email</span>
                            <span class="text-dark fw-semibold text-truncate ms-2" style="max-width: 180px;">{{ $employee->user->email }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted small">System Role</span>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2">{{ $employee->user->role }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Status</span>
                            @if($employee->status)
                                <span class="badge bg-success-subtle text-success rounded-pill px-2">Active</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Office Use & Offer Letter Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-secondary text-white py-3 rounded-top-4">
                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center">
                        <i class="mdi mdi-briefcase-outline me-2 mdi-24px text-white"></i> Office & Offer Details
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Office Details -->
                    <div class="mb-4">
                        <h6 class="text-secondary fw-bold mb-3 small text-uppercase">Office Details</h6>
                        @if($employee->user->officeDetail)
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted small">Employment Type</span>
                                    <span class="text-dark fw-semibold">{{ $employee->user->officeDetail->employment_type ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted small">Start Date</span>
                                    <span class="text-dark fw-semibold">{{ $employee->user->officeDetail->start_date ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted small">Job Position</span>
                                    <span class="text-dark fw-semibold">{{ $employee->user->officeDetail->job_position ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted small">Wage</span>
                                    <span class="text-dark fw-semibold">${{ $employee->user->officeDetail->wage ?? 'N/A' }}/hr</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Hiring Manager</span>
                                    <span class="text-dark fw-semibold">{{ $employee->user->officeDetail->hiring_manager_name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        @else
                            <p class="text-muted small mb-0">No office details available.</p>
                        @endif
                    </div>

                    <!-- Offer Letter -->
                    <div class="border-top pt-3">
                        <h6 class="text-secondary fw-bold mb-3 small text-uppercase">Offer Letter</h6>
                        @if($employee->user->offerLetter)
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted small">Job Title</span>
                                    <span class="text-dark fw-semibold">{{ $employee->user->offerLetter->job_title ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted small">Joining Date</span>
                                    <span class="text-dark fw-semibold">{{ $employee->user->offerLetter->joining_date ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted small">Salary</span>
                                    <span class="text-dark fw-semibold">{{ $employee->user->offerLetter->salary ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted small">Status</span>
                                    @if($employee->user->offerLetter->is_accepted)
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2">Accepted</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning rounded-pill px-2">Pending</span>
                                    @endif
                                </div>
                                <div class="mt-2 bg-light p-3 rounded-3">
                                    <span class="text-muted small d-block mb-1">Description</span>
                                    <p class="small text-dark mb-0">
                                        @if($employee->user->offerLetter->description)
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
                            <p class="text-muted small mb-0">No offer letter created.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Availability Details -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-info text-white py-3 rounded-top-4">
                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center">
                        <i class="mdi mdi-calendar-clock me-2 mdi-24px text-white"></i> Availability
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($employee->user->availability)
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex justify-content-between border-bottom pb-2">
                                <span class="text-muted small">Availability Date</span>
                                <span class="text-dark fw-semibold">{{ $employee->user->availability->availability_date ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-2">
                                <span class="text-muted small">Willing Hours</span>
                                <span class="badge bg-success-subtle text-success fw-bold px-2">{{ $employee->user->availability->willing_hours ?? 'N/A' }} hrs</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-2">
                                <span class="text-muted small">Unable Hours</span>
                                <span class="badge bg-danger-subtle text-danger fw-bold px-2">{{ $employee->user->availability->unable_hours ?? 'N/A' }} hrs</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Unable Days</span>
                                <span class="text-dark fw-semibold">{{ $employee->user->availability->unable_days ?? 'N/A' }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-muted small mb-0">No availability set.</p>
                    @endif
                </div>
            </div>

            <!-- Pay Slips Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-dark text-white py-3 rounded-top-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center">
                        <i class="mdi mdi-cash-multiple me-2 mdi-24px text-white"></i> Pay Slips
                    </h5>
                    <span class="badge bg-white text-dark rounded-pill">{{ $employee->user->paySlips->count() }} Files</span>
                </div>
                <div class="card-body p-0">
                    @if($employee->user->paySlips && $employee->user->paySlips->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($employee->user->paySlips->sortByDesc('year')->sortByDesc('month') as $slip)
                                <div class="list-group-item d-flex align-items-center justify-content-between px-4 py-3">
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ date('F', mktime(0, 0, 0, $slip->month, 1)) }} {{ $slip->year }}</span>
                                        <small class="text-muted">Uploaded: {{ $slip->created_at->format('j M Y') }}</small>
                                    </div>
                                    <a href="{{ $slip->file_path }}" target="_blank" class="btn btn-icon btn-light rounded-circle text-primary border shadow-xs" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                        <i class="mdi mdi-download"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center text-muted small">No pay slips uploaded yet.</div>
                    @endif
                </div>
            </div>

            <!-- Policies Status Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-warning text-dark py-3 rounded-top-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                        <i class="mdi mdi-file-document-outline me-2 mdi-24px text-dark"></i> Policies Status
                    </h5>
                    <span class="badge bg-dark text-white rounded-pill">{{ $employee->user->signedPolicies->count() }} / {{ $allPolicies->count() }}</span>
                </div>
                <div class="card-body p-0">
                    @if($allPolicies->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($allPolicies as $policy)
                                @php
                                    $signed = $employee->user->signedPolicies->firstWhere('policy_id', $policy->id);
                                @endphp
                                <div class="list-group-item px-4 py-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="text-dark small text-truncate" style="max-width: 200px;">{{ $policy->type }}</strong>
                                        @if($signed)
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2">Signed</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2">Pending</span>
                                        @endif
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted" style="font-size: 0.72rem;">
                                            {{ $signed ? 'Date: ' . $signed->created_at->format('j M Y') : 'Not signed' }}
                                        </small>
                                        <div class="d-flex gap-1">
                                            @if($policy->document)
                                                <a href="{{ $policy->document }}" target="_blank" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 0.7rem;"><i class="mdi mdi-eye"></i> Template</a>
                                            @endif
                                            @if($signed && $signed->document)
                                                <a href="{{ $signed->document }}" target="_blank" class="btn btn-xs btn-success py-0 px-2" style="font-size: 0.7rem;"><i class="mdi mdi-download"></i> Signed</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center text-muted small">No active policies configured.</div>
                    @endif
                </div>
            </div>

            <!-- Tax Documents Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-danger text-white py-3 rounded-top-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center">
                        <i class="mdi mdi-file-percent me-2 mdi-24px text-white"></i> Tax Documents
                    </h5>
                    <span class="badge bg-white text-danger rounded-pill">{{ $employee->user->taxDocumentSubmissions->count() }} / {{ $allTaxDocs->count() }}</span>
                </div>
                <div class="card-body p-0">
                    @if($allTaxDocs->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($allTaxDocs as $taxDoc)
                                @php
                                    $submission = $employee->user->taxDocumentSubmissions->firstWhere('tax_document_id', $taxDoc->id);
                                @endphp
                                <div class="list-group-item px-4 py-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="text-dark small">{{ $taxDoc->type }}</strong>
                                        @if($submission)
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2">Submitted</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning rounded-pill px-2">Pending</span>
                                        @endif
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted" style="font-size: 0.72rem;">
                                            {{ $submission ? 'Date: ' . $submission->created_at->format('j M Y') : 'Required' }}
                                        </small>
                                        <div class="d-flex gap-1">
                                            @if($taxDoc->file_path)
                                                <a href="{{ $taxDoc->file_path }}" target="_blank" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 0.7rem;"><i class="mdi mdi-download"></i> Template</a>
                                            @endif
                                            @if($submission && $submission->document_path)
                                                <a href="{{ $submission->document_path }}" target="_blank" class="btn btn-xs btn-primary py-0 px-2" style="font-size: 0.7rem;"><i class="mdi mdi-file-find"></i> Submission</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center text-muted small">No tax documents configured.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-8 col-md-7">
            <!-- Personal & Contact Details Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-primary text-white py-3 rounded-top-4">
                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center">
                        <i class="mdi mdi-card-account-details-outline me-2 mdi-24px text-white"></i> Personal & Contact Details
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($employee->user->candidate)
                        <div class="d-flex flex-column gap-3">
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">First Name</div>
                                <div class="col-md-8 text-dark fw-bold">{{ $employee->user->candidate->first_name }}</div>
                            </div>
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">Last Name</div>
                                <div class="col-md-8 text-dark fw-bold">{{ $employee->user->candidate->last_name }}</div>
                            </div>
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">Designation</div>
                                <div class="col-md-8 text-dark fw-bold text-primary">{{ $employee->user->candidate->designation ?? 'N/A' }}</div>
                            </div>
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">Date of Birth</div>
                                <div class="col-md-8 text-dark fw-semibold">{{ $employee->user->candidate->dob ?? 'N/A' }}</div>
                            </div>
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">SIN</div>
                                <div class="col-md-8 text-dark fw-semibold">{{ $employee->user->candidate->sin ?? 'N/A' }}</div>
                            </div>
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">Phone Number</div>
                                <div class="col-md-8 text-dark fw-semibold">{{ $employee->user->candidate->phone ?? 'N/A' }}</div>
                            </div>
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">Personal Email</div>
                                <div class="col-md-8 text-dark fw-semibold">{{ $employee->user->candidate->email ?? 'N/A' }}</div>
                            </div>
                            <div class="row align-items-start border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">Residential Address</div>
                                <div class="col-md-8 text-dark fw-semibold">
                                    {{ $employee->user->candidate->address }}, {{ $employee->user->candidate->city }}, {{ $employee->user->candidate->province }} {{ $employee->user->candidate->postal_code }}
                                </div>
                            </div>

                            <!-- Emergency Contact -->
                            <div class="mt-3 p-3 bg-warning-subtle bg-opacity-10 border border-warning-subtle rounded-3">
                                <h6 class="text-warning-emphasis fw-bold mb-2"><i class="mdi mdi-alert-octagon-outline me-1"></i> Emergency Contact</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <small class="text-muted d-block">Contact Name</small>
                                        <span class="text-dark fw-bold">{{ $employee->user->candidate->emergency_contact_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Contact Phone</small>
                                        <span class="text-dark fw-bold">{{ $employee->user->candidate->emergency_contact_phone ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted py-4 text-center mb-0">No personal details available.</p>
                    @endif
                </div>
            </div>

            <!-- Licenses & Certifications Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-success text-white py-3 rounded-top-4">
                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center">
                        <i class="mdi mdi-certificate-outline me-2 mdi-24px text-white"></i> Licenses & Certifications
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($employee->user->licenseDetail)
                        <div class="d-flex flex-column gap-3">
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">Security License #</div>
                                <div class="col-md-8 text-dark fw-bold">
                                    {{ $employee->user->licenseDetail->security_license_number ?? 'N/A' }}
                                    @if($employee->user->licenseDetail->security_license_expiry)
                                        <small class="text-muted d-block">Expiry: {{ $employee->user->licenseDetail->security_license_expiry }}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">Driver's License #</div>
                                <div class="col-md-8 text-dark fw-bold">
                                    {{ $employee->user->licenseDetail->drivers_license_number ?? 'N/A' }}
                                    @if($employee->user->licenseDetail->drivers_license_expiry)
                                        <small class="text-muted d-block">Expiry: {{ $employee->user->licenseDetail->drivers_license_expiry }}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">Work Eligibility</div>
                                <div class="col-md-8 text-dark fw-semibold">
                                    {{ $employee->user->licenseDetail->work_eligibility_type_number ?? 'N/A' }}
                                    @if($employee->user->licenseDetail->work_eligibility_expiry)
                                        <small class="text-muted d-block">Expiry: {{ $employee->user->licenseDetail->work_eligibility_expiry }}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">First Aid Training</div>
                                <div class="col-md-8">
                                    <span class="badge rounded-pill px-3 py-1 {{ $employee->user->licenseDetail->first_aid_training ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ $employee->user->licenseDetail->first_aid_training ? 'Trained / Active' : 'No' }}
                                    </span>
                                </div>
                            </div>
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">Criminal Record Check</div>
                                <div class="col-md-8">
                                    <span class="badge rounded-pill px-3 py-1 {{ $employee->user->licenseDetail->criminal_record_check ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ $employee->user->licenseDetail->criminal_record_check ? 'Cleared / Verified' : 'No' }}
                                    </span>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-4 text-secondary small fw-semibold">Other Certificates</div>
                                <div class="col-md-8 text-dark fw-semibold">{{ $employee->user->licenseDetail->other_certificates ?? 'N/A' }}</div>
                            </div>

                            <!-- Document Files List -->
                            <div class="mt-3 border-top pt-3">
                                <h6 class="text-secondary fw-bold mb-3 small text-uppercase">Attached Verification Files</h6>
                                <div class="row g-2">
                                    @php
                                        $filesToDisplay = [
                                            'Security License File' => $employee->user->licenseDetail->security_license_file,
                                            'Driver\'s License File' => $employee->user->licenseDetail->drivers_license_file,
                                            'Work Eligibility File' => $employee->user->licenseDetail->work_eligibility_file,
                                            'Other Documents File' => $employee->user->licenseDetail->other_documents_file,
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
                                            @foreach((array)$files as $file)
                                                @if($file && is_string($file))
                                                    @php
                                                        $url = filter_var($file, FILTER_VALIDATE_URL) ? $file : \Illuminate\Support\Facades\Storage::url($file);
                                                    @endphp
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded border border-light">
                                                            <span class="small fw-semibold text-dark text-truncate me-2" style="max-width: 160px;"><i class="mdi mdi-file-outline me-1"></i> {{ $label }}</span>
                                                            <a href="{{ $url }}" target="_blank" class="btn btn-xs btn-outline-primary py-1 px-2" style="font-size: 0.72rem;"><i class="mdi mdi-download"></i> View</a>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted py-4 text-center mb-0">No licenses or certifications available.</p>
                    @endif
                </div>
            </div>

            <!-- Banking Information Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-info text-white py-3 rounded-top-4">
                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center">
                        <i class="mdi mdi-bank-outline me-2 mdi-24px text-white"></i> Banking Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($employee->user->bankDetail)
                        <div class="d-flex flex-column gap-3">
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">Bank Name</div>
                                <div class="col-md-8 text-dark fw-bold">{{ $employee->user->bankDetail->bank_name ?? 'N/A' }}</div>
                            </div>
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">Account Number</div>
                                <div class="col-md-8 text-dark fw-bold">{{ $employee->user->bankDetail->account_number ?? 'N/A' }}</div>
                            </div>
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">Institution Number</div>
                                <div class="col-md-8 text-dark fw-semibold">{{ $employee->user->bankDetail->institution_number ?? 'N/A' }}</div>
                            </div>
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">Transit Number</div>
                                <div class="col-md-8 text-dark fw-semibold">{{ $employee->user->bankDetail->transit_number ?? 'N/A' }}</div>
                            </div>
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">Interac Email</div>
                                <div class="col-md-8 text-dark fw-semibold">{{ $employee->user->bankDetail->interac_email ?? 'N/A' }}</div>
                            </div>
                            <div class="row align-items-center border-bottom pb-2">
                                <div class="col-md-4 text-secondary small fw-semibold">Bank Address</div>
                                <div class="col-md-8 text-dark fw-semibold">{{ $employee->user->bankDetail->bank_address ?? 'N/A' }}</div>
                            </div>

                            @php
                                $bankFiles = $employee->user->bankDetail->void_cheque_file;
                                if (is_string($bankFiles)) {
                                    $decoded = json_decode($bankFiles, true);
                                    $bankFiles = json_last_error() === JSON_ERROR_NONE ? $decoded : [$bankFiles];
                                }
                            @endphp
                            @if($bankFiles && count((array)$bankFiles) > 0)
                                <div class="mt-2">
                                    <span class="text-secondary small fw-semibold d-block mb-2">Void Cheques</span>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach((array)$bankFiles as $file)
                                            @if($file && is_string($file))
                                                @php
                                                    $url = filter_var($file, FILTER_VALIDATE_URL) ? $file : \Illuminate\Support\Facades\Storage::url($file);
                                                @endphp
                                                <a href="{{ $url }}" target="_blank" class="btn btn-outline-info rounded-pill px-3 py-1 btn-sm fw-bold">
                                                    <i class="mdi mdi-download me-1"></i> Download Void Cheque
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-muted py-4 text-center mb-0">No banking details recorded.</p>
                    @endif
                </div>
            </div>

            <!-- Orientation Quiz Attempts Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header text-white py-3 rounded-top-4" style="background-color: #6f42c1;">
                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center">
                        <i class="mdi mdi-school-outline me-2 mdi-24px text-white"></i> Orientation Quiz Attempts
                    </h5>
                </div>
                <div class="card-body p-4">
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
                                                    <span class="badge bg-secondary-subtle text-dark">Score: {{ round($attempt->score, 2) }}%</span>
                                                    @if($attempt->is_passed)
                                                        <span class="badge bg-success-subtle text-success"><i class="mdi mdi-checkbox-marked-circle-outline"></i> Passed</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger"><i class="mdi mdi-alert-circle-outline"></i> Failed</span>
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
                                                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="mdi mdi-check"></i> Correct</span>
                                                                @else
                                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="mdi mdi-close"></i> Incorrect</span>
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
                        <p class="text-muted py-4 text-center mb-0">No quiz orientation attempts recorded.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
