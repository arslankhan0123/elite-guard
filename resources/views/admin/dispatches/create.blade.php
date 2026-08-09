@extends('dashboardLayouts.main')
@section('title', 'New Dispatch')

@section('breadcrumbTitle', 'New Dispatch')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('dispatches.index') }}">Dispatch Tasks</a></li>
<li class="breadcrumb-item active">New Dispatch</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <form action="{{ route('dispatches.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Form Cards wrapper -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-primary text-white py-3 rounded-top-4">
                    <h5 class="fw-bold mb-0 text-white"><i class="mdi mdi-plus-circle-outline me-2"></i>New Dispatch Log</h5>
                </div>
                <div class="card-body p-4">
                    <!-- First row: Client, Site, Guard -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="company_id" class="form-label fw-semibold text-secondary">Client*</label>
                            <select name="company_id" id="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                                <option value="">Select Client</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="site_id" class="form-label fw-semibold text-secondary">Post Site*</label>
                            <select name="site_id" id="site_id" class="form-select @error('site_id') is-invalid @enderror" required>
                                <option value="">Select Post Site</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" data-company="{{ $site->company_id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                                @endforeach
                            </select>
                            @error('site_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="assigned_guard_ids" class="form-label fw-semibold text-secondary">Assign Guards</label>
                            <select name="assigned_guard_ids[]" id="assigned_guard_ids" class="form-select @error('assigned_guard_ids') is-invalid @enderror" multiple="multiple" data-placeholder="Select Guards">
                                @foreach($guards as $guard)
                                    <option value="{{ $guard->id }}" {{ (is_array(old('assigned_guard_ids')) && in_array($guard->id, old('assigned_guard_ids'))) ? 'selected' : '' }}>{{ $guard->name }}</option>
                                @endforeach
                            </select>
                            @error('assigned_guard_ids')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Second row: Priority, Caller Type, Caller Name -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="priority" class="form-label fw-semibold text-secondary">Priority*</label>
                            <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Medium" {{ old('priority', 'Medium') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                                <option value="Emergency" {{ old('priority') == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="caller_type" class="form-label fw-semibold text-secondary">Caller Type*</label>
                            <select name="caller_type" id="caller_type" class="form-select @error('caller_type') is-invalid @enderror" required>
                                <option value="Client" {{ old('caller_type', 'Client') == 'Client' ? 'selected' : '' }}>Client</option>
                                <option value="Guard" {{ old('caller_type') == 'Guard' ? 'selected' : '' }}>Guard</option>
                                <option value="Emergency Services" {{ old('caller_type') == 'Emergency Services' ? 'selected' : '' }}>Emergency Services</option>
                                <option value="Other" {{ old('caller_type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('caller_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="caller_name" class="form-label fw-semibold text-secondary">Caller Name*</label>
                            <input type="text" name="caller_name" id="caller_name" class="form-control @error('caller_name') is-invalid @enderror" value="{{ old('caller_name') }}" placeholder="Enter caller name" required>
                            @error('caller_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="border-top pt-4 mb-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="mdi mdi-alert-circle-outline text-danger me-1"></i>Incident Details</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="incident_location" class="form-label fw-semibold text-secondary">Incident Location*</label>
                                <input type="text" name="incident_location" id="incident_location" class="form-control @error('incident_location') is-invalid @enderror" value="{{ old('incident_location') }}" placeholder="Address, building, room or specific area" required>
                                @error('incident_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="incident_type" class="form-label fw-semibold text-secondary">Incident Type*</label>
                                <input type="text" name="incident_type" id="incident_type" class="form-control @error('incident_type') is-invalid @enderror" value="{{ old('incident_type') }}" placeholder="Fire, Theft, Trespassing, Welfare Check, etc." required>
                                @error('incident_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="incident_date" class="form-label fw-semibold text-secondary">Incident Date*</label>
                                <div class="input-group">
                                    <input type="date" name="incident_date" id="incident_date" class="form-control @error('incident_date') is-invalid @enderror" value="{{ old('incident_date', date('Y-m-d')) }}" required>
                                </div>
                                @error('incident_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="incident_time" class="form-label fw-semibold text-secondary">Incident Time*</label>
                                <div class="input-group">
                                    <input type="time" name="incident_time" id="incident_time" class="form-control @error('incident_time') is-invalid @enderror" value="{{ old('incident_time', date('H:i')) }}" required>
                                </div>
                                @error('incident_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="incident_details" class="form-label fw-semibold text-secondary">Incident Details*</label>
                            <textarea name="incident_details" id="incident_details" rows="4" class="form-control @error('incident_details') is-invalid @enderror" placeholder="Describe the incident fully..." required>{{ old('incident_details') }}</textarea>
                            @error('incident_details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Administration Section -->
                    <div class="border-top pt-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="mdi mdi-shield-account text-info me-1"></i>Action & Internal Administration</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="action_taken" class="form-label fw-semibold text-secondary">Action Taken</label>
                                <textarea name="action_taken" id="action_taken" rows="3" class="form-control @error('action_taken') is-invalid @enderror" placeholder="What actions have been taken so far...">{{ old('action_taken') }}</textarea>
                                @error('action_taken')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="internal_notes" class="form-label fw-semibold text-secondary">Internal Notes</label>
                                <textarea name="internal_notes" id="internal_notes" rows="3" class="form-control @error('internal_notes') is-invalid @enderror" placeholder="Dispatch internal logs / private supervisor notes...">{{ old('internal_notes') }}</textarea>
                                @error('internal_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label fw-semibold text-secondary">Attachment</label>
                            <input type="file" name="attachment" id="attachment" class="form-control @error('attachment') is-invalid @enderror">
                            <div class="form-text text-muted">Upload any voice notes, reports, PDFs, or photos (Max 10MB)</div>
                            @error('attachment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top py-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('dispatches.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#assigned_guard_ids').select2({
            placeholder: "Select Guards",
            allowClear: true
        });
    });

    // Simple filter to show only sites belonging to the selected client/company
    document.getElementById('company_id').addEventListener('change', function() {
        var companyId = this.value;
        var siteSelect = document.getElementById('site_id');
        var options = siteSelect.options;
        
        for (var i = 0; i < options.length; i++) {
            var option = options[i];
            if (option.value === "") {
                option.style.display = "block";
                continue;
            }
            if (option.getAttribute('data-company') === companyId) {
                option.style.display = "block";
            } else {
                option.style.display = "none";
            }
        }
        siteSelect.value = "";
    });
</script>
@endsection
