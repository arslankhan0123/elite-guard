@extends('dashboardLayouts.main')
@section('title', 'Post Open Shift')

@section('breadcrumbTitle', 'Post Open Shift')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('open-shifts.index') }}">Open Shifts</a></li>
    <li class="breadcrumb-item active">Post New Shift</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body p-4">

                {{-- Header --}}
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:50px;height:50px;background:linear-gradient(135deg,#1e1b4b,#7c3aed);">
                        <i data-feather="layers" style="width:22px;color:#fff;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Post an Open Shift</h5>
                        <p class="text-muted mb-0 small">Employees will see this and can send a claim request</p>
                    </div>
                </div>

                <form action="{{ route('open-shifts.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">

                        {{-- Site --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">
                                <i data-feather="map-pin" style="width:13px;" class="me-1 text-primary"></i>Site / Location
                            </label>
                            <select name="site_id" id="site_id" class="form-select rounded-3 @error('site_id') is-invalid @enderror" required>
                                <option value="">— Select Site —</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                                        {{ $site->name }} {{ $site->city ? '('.$site->city.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('site_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Shift Name --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">
                                <i data-feather="tag" style="width:13px;" class="me-1 text-primary"></i>Shift Name
                            </label>
                            <input type="text" name="shift_name" class="form-control rounded-3 @error('shift_name') is-invalid @enderror"
                                   value="{{ old('shift_name', 'Regular Shift') }}" placeholder="e.g. Night Patrol" required>
                            @error('shift_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Date --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">
                                <i data-feather="calendar" style="width:13px;" class="me-1 text-primary"></i>Date
                            </label>
                            <input type="date" name="date" class="form-control rounded-3 @error('date') is-invalid @enderror"
                                   value="{{ old('date') }}" required>
                            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Start Time --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">
                                <i data-feather="clock" style="width:13px;" class="me-1 text-primary"></i>Start Time
                            </label>
                            <input type="time" name="start_time" class="form-control rounded-3 @error('start_time') is-invalid @enderror"
                                   value="{{ old('start_time', '08:00') }}" required>
                            @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- End Time --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">
                                <i data-feather="clock" style="width:13px;" class="me-1 text-primary"></i>End Time
                            </label>
                            <input type="time" name="end_time" class="form-control rounded-3 @error('end_time') is-invalid @enderror"
                                   value="{{ old('end_time', '16:00') }}" required>
                            @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Slots --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">
                                <i data-feather="users" style="width:13px;" class="me-1 text-primary"></i>Available Slots
                            </label>
                            <input type="number" name="slots" class="form-control rounded-3 @error('slots') is-invalid @enderror"
                                   value="{{ old('slots', 1) }}" min="1" max="50" required>
                            <small class="text-muted">How many employees can claim this shift</small>
                            @error('slots')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">
                                <i data-feather="toggle-right" style="width:13px;" class="me-1 text-primary"></i>Status
                            </label>
                            <select name="status" class="form-select rounded-3 @error('status') is-invalid @enderror" required>
                                <option value="open" {{ old('status','open') === 'open' ? 'selected' : '' }}>Open — Visible to Employees</option>
                                <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed — Hidden from Employees</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Notes --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small">
                                <i data-feather="file-text" style="width:13px;" class="me-1 text-primary"></i>Notes for Employees <span class="text-muted fw-normal">(optional)</span>
                            </label>
                            <textarea name="notes" class="form-control rounded-3 @error('notes') is-invalid @enderror"
                                      rows="3" placeholder="Any special requirements, uniform notes, etc.">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                        <a href="{{ route('open-shifts.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i data-feather="send" style="width:16px;" class="me-1"></i> Post Open Shift
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
