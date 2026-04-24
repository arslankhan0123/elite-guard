@extends('dashboardLayouts.main')
@section('title', 'Edit Open Shift')

@section('breadcrumbTitle', 'Edit Open Shift')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('open-shifts.index') }}">Open Shifts</a></li>
    <li class="breadcrumb-item active">Edit Shift</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body p-4">

                {{-- Header --}}
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:50px;height:50px;background:linear-gradient(135deg,#065f46,#10b981);">
                        <i data-feather="edit-2" style="width:22px;color:#fff;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Edit Open Shift</h5>
                        <p class="text-muted mb-0 small">
                            Updating: <strong>{{ $openShift->shift_name }}</strong> on
                            {{ \Carbon\Carbon::parse($openShift->date)->format('D, d M Y') }}
                        </p>
                    </div>
                </div>

                {{-- Claim summary notice --}}
                @if($openShift->claims->count() > 0)
                    <div class="alert alert-warning rounded-3 border-0 d-flex align-items-center gap-2 mb-4">
                        <i data-feather="alert-triangle" style="width:18px;flex-shrink:0;"></i>
                        <span>This shift has <strong>{{ $openShift->claims->count() }} claim(s)</strong>
                        ({{ $openShift->claims->where('status','approved')->count() }} approved).
                        Changing the date or time will not affect already-approved schedule entries.</span>
                    </div>
                @endif

                <form action="{{ route('open-shifts.update', $openShift->id) }}" method="POST">
                    @csrf

                    <div class="row g-3">

                        {{-- Site --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">
                                <i data-feather="map-pin" style="width:13px;" class="me-1 text-success"></i>Site / Location
                            </label>
                            <select name="site_id" class="form-select rounded-3 @error('site_id') is-invalid @enderror" required>
                                <option value="">— Select Site —</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}"
                                        {{ old('site_id', $openShift->site_id) == $site->id ? 'selected' : '' }}>
                                        {{ $site->name }} {{ $site->city ? '('.$site->city.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('site_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Shift Name --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">
                                <i data-feather="tag" style="width:13px;" class="me-1 text-success"></i>Shift Name
                            </label>
                            <input type="text" name="shift_name" class="form-control rounded-3 @error('shift_name') is-invalid @enderror"
                                   value="{{ old('shift_name', $openShift->shift_name) }}" required>
                            @error('shift_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Date --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">
                                <i data-feather="calendar" style="width:13px;" class="me-1 text-success"></i>Date
                            </label>
                            <input type="date" name="date" class="form-control rounded-3 @error('date') is-invalid @enderror"
                                   value="{{ old('date', $openShift->date) }}" required>
                            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Start Time --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">
                                <i data-feather="clock" style="width:13px;" class="me-1 text-success"></i>Start Time
                            </label>
                            <input type="time" name="start_time" class="form-control rounded-3 @error('start_time') is-invalid @enderror"
                                   value="{{ old('start_time', substr($openShift->start_time,0,5)) }}" required>
                            @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- End Time --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">
                                <i data-feather="clock" style="width:13px;" class="me-1 text-success"></i>End Time
                            </label>
                            <input type="time" name="end_time" class="form-control rounded-3 @error('end_time') is-invalid @enderror"
                                   value="{{ old('end_time', substr($openShift->end_time,0,5)) }}" required>
                            @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Slots --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">
                                <i data-feather="users" style="width:13px;" class="me-1 text-success"></i>Available Slots
                            </label>
                            <input type="number" name="slots" class="form-control rounded-3 @error('slots') is-invalid @enderror"
                                   value="{{ old('slots', $openShift->slots) }}" min="1" max="50" required>
                            @error('slots')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">
                                <i data-feather="toggle-right" style="width:13px;" class="me-1 text-success"></i>Status
                            </label>
                            <select name="status" class="form-select rounded-3 @error('status') is-invalid @enderror" required>
                                <option value="open"   {{ old('status', $openShift->status) === 'open'   ? 'selected' : '' }}>Open — Visible to Employees</option>
                                <option value="closed" {{ old('status', $openShift->status) === 'closed' ? 'selected' : '' }}>Closed — Hidden from Employees</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Notes --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small">
                                <i data-feather="file-text" style="width:13px;" class="me-1 text-success"></i>Notes for Employees <span class="text-muted fw-normal">(optional)</span>
                            </label>
                            <textarea name="notes" class="form-control rounded-3 @error('notes') is-invalid @enderror"
                                      rows="3">{{ old('notes', $openShift->notes) }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                        <a href="{{ route('open-shifts.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">Cancel</a>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                            <i data-feather="save" style="width:16px;" class="me-1"></i> Save Changes
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
