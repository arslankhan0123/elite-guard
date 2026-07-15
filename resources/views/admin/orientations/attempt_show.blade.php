@extends('dashboardLayouts.main')
@section('title', 'Attempt Result')

@section('breadcrumbTitle', 'Orientation Attempt Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('orientations.index') }}">Orientations</a></li>
    <li class="breadcrumb-item"><a href="{{ route('orientations.attempts', $orientation->id) }}">Attempts</a></li>
    <li class="breadcrumb-item active">View Result</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0">Result: {{ $attempt->user->name ?? 'Unknown User' }} - {{ $orientation->type }}</h4>
                <a href="{{ route('orientations.attempts', $orientation->id) }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="mdi mdi-arrow-left"></i> Back to Attempts
                </a>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h5 class="card-title text-white mb-0"><i class="mdi mdi-chart-box-outline"></i> Summary</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong class="text-muted d-block">User</strong>
                        <span>{{ $attempt->user->name ?? 'N/A' }} ({{ $attempt->user->email ?? 'N/A' }})</span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted d-block">Status</strong>
                        @if($validationResult['is_passed'])
                            <span class="badge bg-success rounded-pill px-3 py-2 fs-6">Passed</span>
                        @else
                            <span class="badge bg-danger rounded-pill px-3 py-2 fs-6">Failed</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted d-block">Score</strong>
                        <h3 class="text-primary fw-bold">{{ round($validationResult['score'], 2) }}%</h3>
                        <small class="text-muted">Required to pass: {{ $orientation->passing_percentage }}%</small>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted d-block">Correct Answers</strong>
                        <span>{{ $validationResult['correct_count'] }} out of {{ $validationResult['total_questions'] }}</span>
                    </div>
                    <div class="mb-0">
                        <strong class="text-muted d-block">Attempt Date</strong>
                        <span>{{ $attempt->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h5 class="mb-4 text-dark fw-bold"><i class="mdi mdi-help-circle-outline text-info"></i> Questions & Answers</h5>
            
            @php
                $userAnswers = is_array($attempt->answers) ? $attempt->answers : json_decode($attempt->answers, true);
                if(!$userAnswers) $userAnswers = [];
            @endphp

            @foreach($orientation->questions as $index => $question)
                @php
                    $userAnswerData = collect($userAnswers)->firstWhere('question_id', $question->id);
                    $userSelectedOptionId = $userAnswerData['option_id'] ?? null;
                    
                    $isQuestionCorrect = false;
                    if ($userSelectedOptionId) {
                        $selectedOption = $question->options->find($userSelectedOptionId);
                        if ($selectedOption && $selectedOption->is_correct) {
                            $isQuestionCorrect = true;
                        }
                    }

                    $cardBg = '#fee2e2'; // darker light red
                    $cardBorder = '#fca5a5'; // red border
                    if ($isQuestionCorrect) {
                        $cardBg = '#dcfce7'; // darker light green
                        $cardBorder = '#86efac'; // green border
                    } elseif (!$userSelectedOptionId) {
                        $cardBg = '#fef3c7'; // darker light yellow
                        $cardBorder = '#fcd34d'; // yellow border
                    }
                @endphp
                
                <div class="card shadow-sm mb-4" style="background-color: {{ $cardBg }}; border: 1px solid {{ $cardBorder }}; border-radius: 1rem;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white border border-secondary border-opacity-25 rounded-3 shadow-sm">
                            <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">Q{{ $index + 1 }}. {{ $question->question_text }}</h5>
                            @if($isQuestionCorrect)
                                <span class="badge bg-success rounded-pill fs-6 px-3 py-2 shadow-sm"><i class="mdi mdi-check"></i> Correct</span>
                            @else
                                <span class="badge bg-danger rounded-pill fs-6 px-3 py-2 shadow-sm"><i class="mdi mdi-close"></i> Incorrect</span>
                            @endif
                        </div>

                        <div class="row g-3">
                            @foreach($question->options as $option)
                                @php
                                    $isUserSelected = ($option->id == $userSelectedOptionId);
                                    $isCorrectOption = $option->is_correct;
                                    
                                    $borderClass = 'border-light';
                                    $bgClass = 'bg-white text-dark';
                                    $icon = 'mdi-circle-outline text-muted';
                                    
                                    if ($isCorrectOption) {
                                        $borderClass = 'border-success';
                                        $bgClass = 'bg-success text-white';
                                        $icon = 'mdi-check-circle text-white';
                                    } elseif ($isUserSelected && !$isCorrectOption) {
                                        $borderClass = 'border-danger';
                                        $bgClass = 'bg-danger text-white';
                                        $icon = 'mdi-close-circle text-white';
                                    } elseif ($isUserSelected && $isCorrectOption) {
                                        // Handled above, but just to be explicit
                                        $borderClass = 'border-success';
                                        $bgClass = 'bg-success text-white';
                                        $icon = 'mdi-check-circle text-white';
                                    }
                                @endphp
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 {{ $borderClass }} {{ $bgClass }} d-flex align-items-center shadow-sm">
                                        <i class="mdi {{ $icon }} me-2 fs-5"></i>
                                        <span style="flex: 1; font-weight: 500;">{{ $option->option_text }}</span>
                                        @if($isUserSelected)
                                            @php
                                                $userName = explode(' ', trim($attempt->user->name ?? 'User'))[0];
                                            @endphp
                                            <span class="badge bg-dark ms-2 shadow-sm" style="font-size: 0.75rem;">{{ $userName }}'s Answer</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if(!$userSelectedOptionId)
                            <div class="mt-4 p-3 bg-white border border-warning rounded-3 text-warning small fw-bold shadow-sm">
                                <i class="mdi mdi-alert-circle-outline"></i> The user skipped or did not answer this question.
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
