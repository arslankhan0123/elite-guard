@extends('dashboardLayouts.main')
@section('title', $title)

@section('breadcrumbTitle', $title)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.all') }}">Reports & Forms</a></li>
    <li class="breadcrumb-item active">View Details</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-white mb-0">{{ $title }}</h4>
                    <div class="d-flex align-items-center gap-2">
                        <form action="{{ route('reports.destroy', ['type' => $type, 'id' => $report->id]) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this record? All attached images, signatures and documents will also be deleted.');">
                            @csrf
                            @method('DELETE')
                            @include('admin.unified-reports.partials.delete-icon', ['record' => $report])
                        </form>
                        <a href="{{ route('reports.all', ['type' => $type]) }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- User Info -->
                    @if($report->user)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 text-primary"><i class="mdi mdi-account-circle-outline"></i> Employee Details</h5>
                        </div>
                        <div class="col-md-4 mb-2">
                            <strong>Name:</strong> <br>
                            {{ $report->user->name }}
                        </div>
                        <div class="col-md-4 mb-2">
                            <strong>Email:</strong> <br>
                            {{ $report->user->email }}
                        </div>
                        <div class="col-md-4 mb-2">
                            <strong>Submitted At:</strong> <br>
                            {{ $report->created_at->format('j M Y H:i A') }}
                        </div>
                    </div>
                    @endif

                    <!-- Report Data -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 text-primary"><i class="mdi mdi-file-document-outline"></i> Record Information</h5>
                        </div>
                        @foreach($attributes->except(['signature', 'employee_signature', 'supervisor_signature', 'documents']) as $key => $value)
                            @php
                                $isJson = is_array($value) || is_object($value);
                                $isLongText = $isJson || in_array(strtolower($key), ['incident_summary', 'summary', 'observation_situation', 'reason_for_fire_watch', 'action_taken', 'description', 'comments']) || strlen((string)$value) > 80;
                            @endphp
                            <div class="{{ $isLongText ? 'col-12' : 'col-md-4' }} mb-3">
                                <strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> <br>
                                @if($isJson)
                                    <pre class="bg-light p-2 rounded">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                @elseif($report->hasCast($key, 'boolean'))
                                    <span class="badge {{ $value ? 'bg-success' : 'bg-danger' }}">{{ $value ? 'Yes' : 'No' }}</span>
                                @else
                                    <span class="text-muted">{{ $value ?? 'N/A' }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Images -->
                    @if(method_exists($report, 'images') && $report->images && $report->images->count() > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 text-primary"><i class="mdi mdi-image-multiple"></i> Attached Images</h5>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($report->images as $image)
                                    @if(isset($image->image_path) && $image->image_path)
                                        <a href="{{ $image->image_path }}" target="_blank" class="border rounded p-1">
                                            <img src="{{ $image->image_path }}" alt="Attachment" class="img-fluid rounded" style="max-height: 150px; max-width: 150px; object-fit: cover;">
                                        </a>
                                    @endif
                                    @if(isset($image->observation_image_path) && $image->observation_image_path)
                                        <a href="{{ $image->observation_image_path }}" target="_blank" class="border rounded p-1" title="Observation">
                                            <img src="{{ $image->observation_image_path }}" alt="Observation" class="img-fluid rounded" style="max-height: 150px; max-width: 150px; object-fit: cover;">
                                        </a>
                                    @endif
                                    @if(isset($image->cleared_area_image_path) && $image->cleared_area_image_path)
                                        <a href="{{ $image->cleared_area_image_path }}" target="_blank" class="border rounded p-1" title="Cleared Area">
                                            <img src="{{ $image->cleared_area_image_path }}" alt="Cleared Area" class="img-fluid rounded" style="max-height: 150px; max-width: 150px; object-fit: cover;">
                                        </a>
                                    @endif
                                    @if(isset($image->path) && $image->path)
                                        <a href="{{ asset('storage/' . $image->path) }}" target="_blank" class="border rounded p-1">
                                            <img src="{{ asset('storage/' . $image->path) }}" alt="Attachment" class="img-fluid rounded" style="max-height: 150px; max-width: 150px; object-fit: cover;">
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(method_exists($report, 'issueImages') && $report->issueImages->count() > 0)
                    <div class="row mb-4">
                        <div class="col-12"><h5 class="border-bottom pb-2 text-primary"><i class="mdi mdi-image-multiple"></i> Issue Images</h5></div>
                        <div class="col-12 d-flex flex-wrap gap-2">
                            @foreach($report->issueImages as $image)
                                <a href="{{ $image->image_path }}" target="_blank" class="border rounded p-1">
                                    <img src="{{ $image->image_path }}" alt="Issue" class="img-fluid rounded" style="max-height: 150px; max-width: 150px; object-fit: cover;">
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @php $signatureFields = ['signature' => 'Signature', 'employee_signature' => 'Employee Signature', 'supervisor_signature' => 'Supervisor Signature']; @endphp
                    @if(collect($signatureFields)->keys()->contains(fn ($field) => filled($report->getAttribute($field))))
                    <div class="row mb-4">
                        <div class="col-12"><h5 class="border-bottom pb-2 text-primary"><i class="mdi mdi-draw"></i> Signatures</h5></div>
                        @foreach($signatureFields as $field => $label)
                            @if($report->getAttribute($field))
                                <div class="col-md-4 mb-3">
                                    <strong>{{ $label }}:</strong><br>
                                    @if(str_starts_with($report->getAttribute($field), 'data:image') || filter_var($report->getAttribute($field), FILTER_VALIDATE_URL))
                                        <a href="{{ $report->getAttribute($field) }}" target="_blank"><img src="{{ $report->getAttribute($field) }}" alt="{{ $label }}" class="img-fluid border rounded p-2 mt-1" style="max-height: 120px;"></a>
                                    @else
                                        <span class="text-muted">{{ $report->getAttribute($field) }}</span>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @endif

                    <!-- Patrol Logs / Entries -->
                    @if(method_exists($report, 'patrolLogs') && $report->patrolLogs && $report->patrolLogs->count() > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 text-primary"><i class="mdi mdi-format-list-checks"></i> Patrol Logs</h5>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Round</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Findings</th>
                                            <th>Initials</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($report->patrolLogs as $log)
                                            <tr>
                                                <td>{{ $log->round ?? 'N/A' }}</td>
                                                <td>{{ $log->date }}</td>
                                                <td>{{ $log->start_time }} - {{ $log->end_time }}</td>
                                                <td>{{ $log->area_patrolled_findings ?? 'N/A' }}</td>
                                                <td>{{ $log->initials ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(method_exists($report, 'patrolEntries') && $report->patrolEntries && $report->patrolEntries->count() > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 text-primary"><i class="mdi mdi-format-list-checks"></i> Patrol Entries</h5>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Time Range</th>
                                            <th>Summary</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($report->patrolEntries as $entry)
                                            <tr>
                                                <td>{{ $entry->time_range ?? 'N/A' }}</td>
                                                <td>{{ $entry->summary ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Documents (e.g. for Vehicle Checklist) -->
                    @if(isset($report->documents) && $report->documents)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 text-primary"><i class="mdi mdi-file-document"></i> Attached Document</h5>
                            <div class="mt-2">
                                @if(filter_var($report->documents, FILTER_VALIDATE_URL) || str_contains($report->documents, '/'))
                                    <a href="{{ $report->documents }}" target="_blank" class="btn btn-outline-primary"><i class="mdi mdi-download me-1"></i> View/Download Document</a>
                                @else
                                    <span class="text-muted">{{ $report->documents }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
