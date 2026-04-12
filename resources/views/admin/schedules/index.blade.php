@extends('dashboardLayouts.main')

@section('title', 'Weekly Schedule')
@section('breadcrumbTitle', 'Weekly Schedule')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Schedule</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Week Navigation Header -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-soft-primary text-primary p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i data-feather="calendar" style="width: 24px;"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Assignments for Week</h4>
                            <p class="text-muted mb-0">
                                <span class="badge bg-primary px-3 rounded-pill">
                                    {{ $weekStart->format('d M, Y') }} - {{ $weekEnd->format('d M, Y') }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('schedules.index', ['date' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}" class="btn btn-outline-secondary rounded-pill px-3">
                            <i data-feather="chevron-left" class="me-1" style="width: 16px;"></i> Previous
                        </a>
                        <a href="{{ route('schedules.index', ['date' => Carbon\Carbon::now()->startOfWeek()->format('Y-m-d')]) }}" class="btn btn-light rounded-pill px-3">
                            Current Week
                        </a>
                        <a href="{{ route('schedules.index', ['date' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}" class="btn btn-outline-secondary rounded-pill px-3">
                            Next <i data-feather="chevron-right" class="ms-1" style="width: 16px;"></i>
                        </a>
                        <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm ms-2" data-bs-toggle="modal" data-bs-target="#assignModal">
                            <i data-feather="plus" class="me-1"></i> New Assignment
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignments Table -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="custom-table" class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Assigned Site</th>
                                <th>Week Period</th>
                                <th>Notes</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schedules as $schedule)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-soft-info text-info p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i data-feather="user" style="width: 18px;"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $schedule->user->name }}</span>
                                            <small class="text-muted">{{ $schedule->user->role }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-2 fw-semibold">
                                        <i data-feather="map-pin" style="width: 12px; height: 12px;" class="me-1"></i>
                                        {{ $schedule->site->name }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted fw-bold">
                                        {{ Carbon\Carbon::parse($schedule->week_start_date)->format('d M') }} - 
                                        {{ Carbon\Carbon::parse($schedule->week_start_date)->addDays(6)->format('d M, Y') }}
                                    </small>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $schedule->notes ?: '---' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('schedules.delete', $schedule->id) }}" class="bin-button" title="Delete" onclick="return confirm('Are you sure you want to remove this assignment?');">
                                            <svg class="bin-top" viewBox="0 0 39 7">
                                                <line y1="5" x2="39" y2="5" stroke="white" stroke-width="4"></line>
                                                <line x1="12" y1="1.5" x2="26.0357" y2="1.5" stroke="white" stroke-width="3"></line>
                                            </svg>
                                            <svg class="bin-bottom" viewBox="0 0 33 39">
                                                <mask>
                                                    <path d="M0 0H33V35C33 37.2091 31.2091 39 29 39H4C1.79086 39 0 37.2091 0 35V0Z" fill="white"></path>
                                                </mask>
                                                <path d="M0 0H33V35C33 37.2091 31.2091 39 29 39H4C1.79086 39 0 37.2091 0 35V0Z" fill="white"></path>
                                                <path d="M12 6L12 29" stroke="white" stroke-width="4"></path>
                                                <path d="M21 6V29" stroke="white" stroke-width="4"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i data-feather="inbox" style="width: 48px; height: 48px; opacity: 0.3;" class="mb-3"></i>
                                        <p class="mb-0">No assignments found for this week.</p>
                                        <p class="small">Click "New Assignment" to start scheduling.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="assignModalLabel">
                    <i data-feather="calendar" class="me-2 text-primary"></i> New Weekly Assignment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('schedules.store') }}" method="POST">
                @csrf
                <input type="hidden" name="week_start_date" value="{{ $weekStart->format('Y-m-d') }}">
                
                <div class="modal-body">
                    <div class="alert bg-soft-primary text-primary border-0 rounded-3 mb-4">
                        <small>Assigning for week: <strong>{{ $weekStart->format('d M, Y') }} - {{ $weekEnd->format('d M, Y') }}</strong></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Employee</label>
                        <select name="user_id" class="form-select rounded-3 p-2" required>
                            <option value="">Choose an employee...</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Site</label>
                        <select name="site_id" class="form-select rounded-3 p-2" required>
                            <option value="">Choose a site...</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}">{{ $site->name }} ({{ $site->company->name ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Notes (Optional)</label>
                        <textarea name="notes" class="form-control rounded-3" rows="2" placeholder="Any specific instructions for this week..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i data-feather="check" style="width: 16px; height: 16px;" class="me-1"></i> Create Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: rgba(124, 58, 237, 0.1) !important; color: #7c3aed !important; }
    .bg-soft-info { background-color: rgba(14, 165, 233, 0.1) !important; color: #0ea5e9 !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endsection
