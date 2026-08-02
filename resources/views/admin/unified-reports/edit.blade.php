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
                                    $isArray = is_array($value) || is_object($value);
                                    $isLongText = $isArray || in_array(strtolower($key), [
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
                                        <textarea name="{{ $key }}" id="{{ $key }}" class="form-control rounded-3" rows="4">{{ old($key, $isArray ? json_encode($value, JSON_PRETTY_PRINT) : $value) }}</textarea>
                                    @elseif(trim(strtolower($key)) == 'decision')
                                        <select name="{{ $key }}" id="{{ $key }}" class="form-select rounded-3">
                                            <option value="Pending" {{ old($key, $value) == 'Pending' || old($key, $value) == '' || is_null($value) ? 'selected' : '' }}>Pending</option>
                                            <option value="Approved" {{ old($key, $value) == 'Approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="Denied" {{ old($key, $value) == 'Denied' ? 'selected' : '' }}>Denied</option>
                                        </select>
                                    @elseif($isDate)
                                        <input type="date" name="{{ $key }}" id="{{ $key }}" class="form-control rounded-3" value="{{ old($key, ($value instanceof \Carbon\Carbon || $value instanceof \Illuminate\Support\Carbon) ? $value->format('Y-m-d') : (!empty($value) ? date('Y-m-d', strtotime($value)) : '')) }}">
                                    @elseif($isTime)
                                        <input type="time" name="{{ $key }}" id="{{ $key }}" class="form-control rounded-3" value="{{ old($key, $value) }}">
                                    @elseif($report->hasCast($key, 'boolean'))
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

                        @foreach(['patrolLogs' => 'Patrol Logs', 'patrolEntries' => 'Patrol Entries'] as $relation => $label)
                            @if($report->relationLoaded($relation) && $report->getRelation($relation)->count())
                                <h5 class="border-bottom pb-2 mt-4 text-primary">{{ $label }}</h5>
                                @foreach($report->getRelation($relation) as $item)
                                    <div class="row bg-light rounded p-2 mb-3">
                                        @foreach(collect($item->getAttributes())->except(['id', $item->getForeignKey(), 'created_at', 'updated_at']) as $field => $fieldValue)
                                            <div class="col-md-6 mb-2">
                                                <label class="form-label fw-semibold">{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                                <textarea name="relations[{{ $relation }}][{{ $item->id }}][{{ $field }}]" class="form-control" rows="2">{{ $fieldValue }}</textarea>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @endif
                        @endforeach

                        @php $signatureFields = ['signature' => 'Signature', 'employee_signature' => 'Employee Signature', 'supervisor_signature' => 'Supervisor Signature']; @endphp
                        @if(collect($signatureFields)->keys()->contains(fn ($field) => filled($report->getAttribute($field))))
                            <h5 class="border-bottom pb-2 mt-4 text-primary">Signatures</h5>
                            <div class="row">
                                @foreach($signatureFields as $field => $label)
                                    @if($report->getAttribute($field))
                                        <div class="col-md-4 mb-3"><strong>{{ $label }}</strong><br>
                                            @if(str_starts_with($report->getAttribute($field), 'data:image') || filter_var($report->getAttribute($field), FILTER_VALIDATE_URL))
                                                <img src="{{ $report->getAttribute($field) }}" class="img-fluid border rounded p-2 mt-1" style="max-height: 120px;" alt="{{ $label }}">
                                            @else
                                                <span class="text-muted">{{ $report->getAttribute($field) }}</span>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        @foreach(['images' => 'Attached Images', 'issueImages' => 'Issue Images'] as $relation => $label)
                            @if($report->relationLoaded($relation) && $report->getRelation($relation)->count())
                                <h5 class="border-bottom pb-2 mt-4 text-primary">{{ $label }}</h5>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($report->getRelation($relation) as $image)
                                        @foreach(['image_path', 'observation_image_path', 'cleared_area_image_path'] as $pathField)
                                            @if($image->getAttribute($pathField))<a href="{{ $image->getAttribute($pathField) }}" target="_blank"><img src="{{ $image->getAttribute($pathField) }}" class="border rounded" style="width:120px;height:100px;object-fit:cover" alt="Attachment"></a>@endif
                                        @endforeach
                                    @endforeach
                                </div>
                            @endif
                        @endforeach

                        @if($report->getAttribute('documents'))
                            <h5 class="border-bottom pb-2 mt-4 text-primary">Documents / Inspection Result</h5>
                            @if(filter_var($report->getAttribute('documents'), FILTER_VALIDATE_URL) || str_contains($report->getAttribute('documents'), '/'))
                                <a href="{{ $report->getAttribute('documents') }}" target="_blank" class="btn btn-outline-primary">View Document</a>
                            @else
                                <p class="text-muted">{{ $report->getAttribute('documents') }}</p>
                            @endif
                        @endif

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
