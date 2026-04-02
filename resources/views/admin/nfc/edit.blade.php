@extends('dashboardLayouts.main')
@section('title', 'NFC Tag Edit')

@section('breadcrumbTitle', 'NFC Tag Edit')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{route('nfc.index')}}">NFC Tags</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card card-h-100">
            <div class="card-header justify-content-between d-flex align-items-center">
                <h4 class="card-title">NFC Tag Edit</h4>
                <a href="{{route('nfc.index')}}" class="btn btn-sm btn-secondary-subtle"><i class="mdi mdi-arrow-right align-middle"></i> Back</a>
            </div>
            <div class="card-body">
                <form action="{{ route('nfc.update', $nfcTag->id) }}" method="POST">
                    @csrf

                    <!-- ================= BASIC INFO ================= -->
                    <h5 class="text-primary">🏷️ NFC Tag Information</h5>

                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Site <span class="text-danger">*</span></label>
                            <select name="site_id" class="form-select" required>
                                <option value="">-- Select Site --</option>
                                @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ old('site_id', $nfcTag->site_id) == $site->id ? 'selected' : '' }}>
                                    {{ $site->name }} ({{ $site->company->name ?? 'No Company' }})
                                </option>
                                @endforeach
                            </select>
                            @error('site_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tag Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $nfcTag->name) }}" required>
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">NFC UID <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="uid" id="nfc_uid" class="form-control" value="{{ old('uid', $nfcTag->uid) }}" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="generateUid()">Generate New</button>
                            </div>
                            <small class="text-muted">Changing this UID means you must write a new UID to your physical card/chip.</small>
                            @error('uid') <br><span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="1" {{ old('status', $nfcTag->status) == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $nfcTag->status) == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary px-4">Update NFC Tag</button>
                            <a href="{{ route('nfc.index') }}" class="btn btn-light px-4">Cancel</a>
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
    function generateUid() {
        const chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        let result = 'NFC-';
        for (let i = 0; i < 10; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('nfc_uid').value = result;
    }
</script>
@endsection
