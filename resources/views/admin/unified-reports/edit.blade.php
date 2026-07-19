@extends('dashboardLayouts.main')
@section('title', $title)

@section('breadcrumbTitle', $title)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.all') }}">Reports & Forms</a></li>
    <li class="breadcrumb-item active">Edit Record</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-white mb-0">{{ $title }}</h4>
                    <a href="{{ route('reports.all', ['type' => $type]) }}" class="btn btn-light btn-sm">
                        <i class="mdi mdi-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('reports.update', ['type' => $type, 'id' => $report->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @if($report->user)
                            <div class="row mb-4 bg-light p-3 rounded">
                                <div class="col-md-4">
                                    <strong class="text-muted">Reported By:</strong>
                                    <p class="mb-0 fw-bold text-dark">{{ $report->user->name }}</p>
                                </div>
                                <div class="col-md-4">
                                    <strong class="text-muted">Email:</strong>
                                    <p class="mb-0 text-dark">{{ $report->user->email }}</p>
                                </div>
                                <div class="col-md-4">
                                    <strong class="text-muted">Original Date Submitted:</strong>
                                    <p class="mb-0 text-dark">{{ $report->created_at->format('j M Y, h:i A') }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            @foreach($attributes as $key => $value)
                                @php
                                    $isLongText = in_array(strtolower($key), [
                                        'incident_summary', 'summary', 'observation_situation', 
                                        'reason_for_fire_watch', 'action_taken', 'description', 
                                        'comments', 'observation', 'findings'
                                    ]) || strlen((string)$value) > 100;
                                    
                                    $isDate = str_contains(strtolower($key), 'date');
                                    $isTime = str_contains(strtolower($key), 'time');
                                @endphp

                                <div class="{{ $isLongText ? 'col-12' : 'col-md-6' }} mb-3">
                                    <label for="{{ $key }}" class="form-label fw-semibold text-dark">
                                        {{ ucwords(str_replace('_', ' ', $key)) }}
                                    </label>

                                    @if($isLongText)
                                        <textarea name="{{ $key }}" id="{{ $key }}" class="form-control rounded-3" rows="4">{{ old($key, $value) }}</textarea>
                                    @elseif($isDate)
                                        <input type="date" name="{{ $key }}" id="{{ $key }}" class="form-control rounded-3" value="{{ old($key, $value) }}">
                                    @elseif($isTime)
                                        <input type="time" name="{{ $key }}" id="{{ $key }}" class="form-control rounded-3" value="{{ old($key, $value) }}">
                                    @elseif(is_bool($value) || in_array(strtolower((string)$value), ['true', 'false', '1', '0']) && !is_numeric($value))
                                        <select name="{{ $key }}" id="{{ $key }}" class="form-select rounded-3">
                                            <option value="1" {{ old($key, $value) == '1' || old($key, $value) == 'true' ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ old($key, $value) == '0' || old($key, $value) == 'false' ? 'selected' : '' }}>No</option>
                                        </select>
                                    @else
                                        <input type="text" name="{{ $key }}" id="{{ $key }}" class="form-control rounded-3" value="{{ old($key, $value) }}">
                                    @endif

                                    @error($key)
                                        <div class="text-danger mt-1 small">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 border-top pt-3">
                            <a href="{{ route('reports.all', ['type' => $type]) }}" class="btn btn-secondary px-4 rounded-3">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 rounded-3">
                                <i class="mdi mdi-content-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
