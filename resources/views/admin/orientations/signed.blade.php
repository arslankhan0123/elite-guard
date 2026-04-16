@extends('dashboardLayouts.main')
@section('title', 'Signed Orientations')

@section('breadcrumbTitle', 'Signed Orientations')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('orientations.index') }}">Orientations</a></li>
    <li class="breadcrumb-item active">Signed</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header justify-content-between d-flex align-items-center">
                    <h4 class="card-title shine">Signed Orientation Table</h4>
                    <a href="{{ route('orientations.index') }}" class="btn btn-sm btn-secondary-subtle">
                        <i class="mdi mdi-arrow-left align-middle"></i> Back to Listing
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="custom-table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Orientation Type</th>
                                    <th>Agreed</th>
                                    <th>Signed Date</th>
                                    <th>Signed Document</th>
                                    <th>Digital Signature</th>
                                    <th>Quiz Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($signedOrientations as $signed)
                                    <tr>
                                        <td>{{ $signed->id }}</td>
                                        <td>
                                            @if($signed->user)
                                                <strong>{{ $signed->user->name }}</strong><br>
                                                <small class="text-muted">{{ $signed->user->email }}</small>
                                            @else
                                                <span class="text-danger">User Deleted</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($signed->orientation)
                                                {{ $signed->orientation->type }}
                                            @else
                                                <span class="text-danger">Orientation Deleted</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(strtolower($signed->agreed) == 'yes')
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ ucfirst($signed->agreed) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $signed->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @if($signed->document)
                                                <a href="{{ $signed->document }}" target="_blank"
                                                    class="btn btn-sm btn-soft-primary">
                                                    <i class="mdi mdi-file-document-edit-outline"></i> View Document
                                                </a>
                                            @else
                                                <span class="text-muted">No Document</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($signed->signature)
                                                @if(strpos($signed->signature, 'data:image') === 0)
                                                    <img src="{{ $signed->signature }}" alt="Signature" style="max-height: 50px; border: 1px solid #ddd; padding: 2px;">
                                                @else
                                                    <span class="text-info" style="font-family: 'Dancing Script', cursive;">{{ $signed->signature }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">No Signature</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($signed->answers->count() > 0)
                                                @php
                                                    $correctCount = $signed->answers->where('is_correct', true)->count();
                                                    $totalCount = $signed->answers->count();
                                                    $percentage = ($correctCount / $totalCount) * 100;
                                                @php
                                                <button type="button" class="btn btn-sm btn-soft-info" data-bs-toggle="modal" data-bs-target="#quizModal{{ $signed->id }}">
                                                    <i class="mdi mdi-eye-outline me-1"></i> {{ round($percentage) }}% ({{ $correctCount }}/{{ $totalCount }})
                                                </button>

                                                <!-- Quiz Modal -->
                                                <div class="modal fade" id="quizModal{{ $signed->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Quiz Details - {{ $signed->user->name ?? 'User' }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="alert alert-info border-0 shadow-sm d-flex justify-content-between align-items-center mb-4">
                                                                    <h6 class="mb-0">Orientation: {{ $signed->orientation->type ?? 'N/A' }}</h6>
                                                                    <span class="badge bg-primary fs-6">Score: {{ round($percentage, 2) }}%</span>
                                                                </div>
                                                                <div class="list-group">
                                                                    @foreach($signed->answers as $index => $answer)
                                                                        <div class="list-group-item border-0 border-bottom">
                                                                            <h6 class="mb-2">Q{{ $index + 1 }}: {{ $answer->question->question_text ?? 'Question Deleted' }}</h6>
                                                                            <p class="mb-1">
                                                                                <strong>User's Answer:</strong> 
                                                                                <span class="{{ $answer->is_correct ? 'text-success' : 'text-danger' }}">
                                                                                    {{ $answer->option->option_text ?? 'Option Deleted' }}
                                                                                    @if($answer->is_correct)
                                                                                        <i class="mdi mdi-check-circle ms-1"></i>
                                                                                    @else
                                                                                        <i class="mdi mdi-close-circle ms-1"></i>
                                                                                    @endif
                                                                                </span>
                                                                            </p>
                                                                            @if(!$answer->is_correct)
                                                                                @php
                                                                                    $correctOption = $answer->question->options->where('is_correct', true)->first();
                                                                                @php
                                                                                <p class="mb-0 text-muted small">
                                                                                    <strong>Correct Answer:</strong> {{ $correctOption->option_text ?? 'Unknown' }}
                                                                                </p>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted small">No Quiz Taken</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
