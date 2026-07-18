@extends('dashboardLayouts.main')
@section('title', 'NFC Tag Create')

@section('breadcrumbTitle', 'NFC Tag Create')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{route('nfc.index')}}">NFC Tags</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card card-h-100">
            <div class="card-header justify-content-between d-flex align-items-center">
                <h4 class="card-title">NFC Tag Create</h4>
                <a href="{{route('nfc.index')}}" class="btn btn-sm btn-secondary-subtle"><i class="mdi mdi-arrow-right align-middle"></i> Back</a>
            </div>
            <div class="card-body">
                <form action="{{ route('nfc.store') }}" method="POST">
                    @csrf

                    <!-- ================= BASIC INFO ================= -->
                    <h5 class="text-primary">🏷️ NFC Tag Information</h5>

                    <div class="row mt-3">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Select Site <span class="text-danger">*</span></label>
                            <select name="site_id" class="form-select" required>
                                <option value="">-- Select Site --</option>
                                @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                                    {{ $site->name }} ({{ $site->company->name ?? 'No Company' }})
                                </option>
                                @endforeach
                            </select>
                            @error('site_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tag Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. Entrance Gate A, Patrol Point 5">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tag Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="tag_type" name="type" required>
                                <option value="" disabled {{ old('type') == '' ? 'selected' : '' }}>Select...</option>
                                <option value="nfc" {{ old('type', 'nfc') == 'nfc' ? 'selected' : '' }}>NFC</option>
                                <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>Image</option>
                                <option value="both" {{ old('type') == 'both' ? 'selected' : '' }}>NFC + Image</option>
                            </select>
                            @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3" id="uid_container">
                            <label class="form-label">NFC UID <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="uid" id="nfc_uid" class="form-control" value="{{ old('uid', $suggestedUid) }}" required placeholder="e.g. NFC-8273645">
                                <button class="btn btn-outline-secondary" type="button" onclick="generateUid()">Generate New</button>
                            </div>
                            <small class="text-muted">This is the ID you will write to your NFC card or chip.</small>
                            @error('uid') <br><span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4 mb-3" id="status_container">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary px-4">Create NFC Tag</button>
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

    document.addEventListener('DOMContentLoaded', function() {
        const tagTypeSelect = document.getElementById('tag_type');
        const uidContainer = document.getElementById('uid_container');
        const nfcUidInput = document.getElementById('nfc_uid');
        const statusContainer = document.getElementById('status_container');

        function toggleUidField() {
            if (tagTypeSelect.value === 'image') {
                uidContainer.style.display = 'none';
                nfcUidInput.removeAttribute('required');
                nfcUidInput.value = '';
                statusContainer.className = 'col-md-12 mb-3';
            } else {
                uidContainer.style.display = 'block';
                nfcUidInput.setAttribute('required', 'required');
                statusContainer.className = 'col-md-4 mb-3';
            }
        }

        tagTypeSelect.addEventListener('change', toggleUidField);
        toggleUidField();
    });
</script>
@endsection
