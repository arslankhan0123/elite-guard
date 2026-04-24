@extends('dashboardLayouts.main')
@section('title', 'Site Create')

@section('breadcrumbTitle', 'Site Create')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{route('sites.index')}}">Sites</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card card-h-100">
            <div class="card-header justify-content-between d-flex align-items-center">
                <h4 class="card-title">Site Create</h4>
                <a href="{{route('sites.index')}}" class="btn btn-sm btn-secondary-subtle"><i class="mdi mdi-arrow-right align-middle"></i> Back</a>
            </div>
            <div class="card-body">
                <form action="{{ route('sites.store') }}" method="POST">
                    @csrf

                    <!-- ================= BASIC INFO ================= -->
                    <h5 class="text-primary">🏢 Site Information</h5>

                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Company <span class="text-danger">*</span></label>
                            <select name="company_id" class="form-select" required>
                                <option value="">-- Select Company --</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                @endforeach
                            </select>
                            @error('company_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Site Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. Head Office, Warehouse A">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- ================= LOCATION ================= -->
                    <h5 class="text-primary mt-4">📍 Location</h5>

                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" value="{{ old('country') }}">
                            @error('country') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                            @error('city') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Detailed Address</label>
                            <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                            @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- ================= GPS COORDINATES ================= -->
                    <h5 class="text-primary mt-4">🛰️ GPS Coordinates <small class="text-muted fs-12 fw-normal">(Check-in ke liye zaroori)</small></h5>

                    <div class="row mt-3">
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="number" step="any" name="latitude" id="create_latitude"
                                   class="form-control" value="{{ old('latitude') }}"
                                   placeholder="e.g. 31.5204">
                            @error('latitude') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-5 mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="number" step="any" name="longitude" id="create_longitude"
                                   class="form-control" value="{{ old('longitude') }}"
                                   placeholder="e.g. 74.3587">
                            @error('longitude') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="button" id="btn_get_location_create"
                                    class="btn btn-outline-success w-100"
                                    onclick="getCurrentLocation('create_latitude','create_longitude','create_location_status')">
                                <i class="mdi mdi-crosshairs-gps me-1"></i> My Location
                            </button>
                        </div>
                    </div>
                    <div id="create_location_status" class="text-muted small mb-3" style="display:none;"></div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary px-4">Create Site</button>
                            <a href="{{ route('sites.index') }}" class="btn btn-light px-4">Cancel</a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function getCurrentLocation(latInputId, lngInputId, statusDivId) {
        var statusDiv = document.getElementById(statusDivId);
        statusDiv.style.display = 'block';
        statusDiv.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Location fetch ho rahi hai...';
        statusDiv.className = 'text-muted small mb-3';

        if (!navigator.geolocation) {
            statusDiv.innerHTML = '<i class="mdi mdi-alert-circle me-1"></i> Aapka browser geolocation support nahi karta.';
            statusDiv.className = 'text-danger small mb-3';
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (position) {
                document.getElementById(latInputId).value  = position.coords.latitude.toFixed(7);
                document.getElementById(lngInputId).value = position.coords.longitude.toFixed(7);
                statusDiv.innerHTML = '<i class="mdi mdi-check-circle me-1"></i> Location set ho gayi! (' +
                    position.coords.latitude.toFixed(5) + ', ' + position.coords.longitude.toFixed(5) + ')';
                statusDiv.className = 'text-success small mb-3';
            },
            function (error) {
                var msg = 'Location mil nahi saki.';
                if (error.code === error.PERMISSION_DENIED)    msg = 'Location permission denied. Browser settings check karein.';
                if (error.code === error.POSITION_UNAVAILABLE) msg = 'Location unavailable hai.';
                if (error.code === error.TIMEOUT)              msg = 'Location timeout ho gayi.';
                statusDiv.innerHTML = '<i class="mdi mdi-alert me-1"></i> ' + msg;
                statusDiv.className = 'text-danger small mb-3';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }
</script>
@endsection
