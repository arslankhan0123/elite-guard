@extends('dashboardLayouts.main')
@section('title', 'Manage Employees')

@section('breadcrumbTitle', 'Employee Directory')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Employees</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="fw-bold mb-1">Elite Personnel</h4>
                            <p class="text-muted mb-0">Total of {{ count($employees) }} registered employees & guards.</p>
                        </div>
                        <a href="{{ route('employees.create') }}"
                            class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i data-feather="plus" class="me-1"></i> Add Employee
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table id="custom-table" class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Assigned Sites</th>
                                    <th>Joining Date</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                    @php
                                        $weeklyNote = '';
                                        if ($employee->user && $employee->user->schedules->isNotEmpty()) {
                                            $weeklyNote = $employee->user->schedules->where('notes', '!=', null)->where('notes', '!=', '')->first()->notes ?? '';
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-soft-primary text-primary p-2 me-3 d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i data-feather="user" style="width: 18px;"></i>
                                                </div>
                                                <span class="fw-bold text-dark">{{ $employee->user->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $employee->user->email }}</td>
                                        <td>
                                            <span
                                                class="badge rounded-pill {{ $employee->user->role == 'SuperAdmin' ? 'bg-soft-danger text-danger' : 'bg-soft-info text-info' }} px-3">
                                                {{ $employee->user->role }}
                                            </span>
                                        </td>
                                        <td>{{ $employee->phone ?? 'N/A' }}</td>
                                        <td>
                                            @if($employee->user->schedules->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-1 mb-1">
                                                    @foreach($employee->user->schedules as $schedule)
                                                        <span class="badge bg-soft-primary text-primary rounded-pill px-2 py-1"
                                                            style="font-size: 0.75rem;">
                                                            <i data-feather="map-pin" style="width: 10px; height: 10px;"></i>
                                                            {{ $schedule->site->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                                @if($weeklyNote)
                                                    <div class="text-muted small italic" style="font-size: 0.7rem;">
                                                        <i data-feather="file-text" style="width: 10px; height: 10px;"></i>
                                                        {{ Str::limit($weeklyNote ?? '', 30) }}
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-muted small">No sites assigned this week</span>
                                            @endif
                                        </td>
                                        <td>{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d M, Y') : 'N/A' }}
                                        </td>
                                        <td>
                                            @if($employee->status)
                                                <span
                                                    class="badge bg-soft-success text-success fw-bold px-3 rounded-pill">Active</span>
                                            @else
                                                <span
                                                    class="badge bg-soft-secondary text-secondary fw-bold px-3 rounded-pill">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                                    title="Assign Sites" data-bs-toggle="modal"
                                                    data-bs-target="#assignSitesModal"
                                                    data-employee-id="{{ $employee->user->id }}"
                                                    data-employee-name="{{ $employee->user->name }}"
                                                    data-assigned-sites="{{ $employee->user->schedules->pluck('site_id')->toJson() }}"
                                                    data-notes="{{ $weeklyNote }}">
                                                    <i data-feather="map" style="width: 14px; height: 14px;"></i>
                                                </button>
                                                <a href="{{ route('employees.edit', $employee->id) }}" class="editBtn"
                                                    title="Edit">
                                                    <svg viewBox="0 0 512 512">
                                                        <path
                                                            d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0z">
                                                        </path>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('employees.delete', $employee->id) }}" class="bin-button"
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this employee?');">
                                                    <svg class="bin-top" viewBox="0 0 39 7">
                                                        <line y1="5" x2="39" y2="5" stroke="white" stroke-width="4"></line>
                                                        <line x1="12" y1="1.5" x2="26.0357" y2="1.5" stroke="white"
                                                            stroke-width="3"></line>
                                                    </svg>
                                                    <svg class="bin-bottom" viewBox="0 0 33 39">
                                                        <mask>
                                                            <path
                                                                d="M0 0H33V35C33 37.2091 31.2091 39 29 39H4C1.79086 39 0 37.2091 0 35V0Z"
                                                                fill="white"></path>
                                                        </mask>
                                                        <path
                                                            d="M0 0H33V35C33 37.2091 31.2091 39 29 39H4C1.79086 39 0 37.2091 0 35V0Z"
                                                            fill="white"></path>
                                                        <path d="M12 6L12 29" stroke="white" stroke-width="4"></path>
                                                        <path d="M21 6V29" stroke="white" stroke-width="4"></path>
                                                    </svg>
                                                </a>
                                            </div>
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

    <!-- Assign Sites Modal -->
    <div class="modal fade" id="assignSitesModal" tabindex="-1" aria-labelledby="assignSitesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="assignSitesModalLabel">
                        <i data-feather="map-pin" class="me-2 text-primary"></i> Assign Sites
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignSitesForm" action="{{ route('schedules.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" id="modal_user_id">
                    <input type="hidden" name="week_start_date" value="{{ $currentMonday }}">

                    <div class="modal-body">
                        <p class="text-muted mb-3">Assign sites to <strong id="modalEmployeeName"></strong> for week <span
                                class="badge bg-light text-primary">{{ \Carbon\Carbon::parse($currentMonday)->format('d M') }}
                                - {{ \Carbon\Carbon::parse($currentMonday)->addDays(6)->format('d M, Y') }}</span></p>
                        <div class="site-checklist" style="max-height: 300px; overflow-y: auto;">
                            @foreach($sites as $site)
                                <div class="form-check site-check-item mb-2 p-2 rounded border bg-light">
                                    <input class="form-check-input site-checkbox" type="checkbox" name="site_ids[]"
                                        value="{{ $site->id }}" id="site_{{ $site->id }}">
                                    <label class="form-check-label fw-semibold" for="site_{{ $site->id }}">
                                        {{ $site->name }}
                                        <small class="text-muted d-block">{{ $site->address ?? $site->city ?? '' }}</small>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-bold small">Notes (Optional)</label>
                            <textarea name="notes" id="modal_notes_employee" class="form-control rounded-3" rows="2"
                                placeholder="Instructions for this week..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i data-feather="check" style="width: 16px; height: 16px;" class="me-1"></i> Save Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .site-check-item {
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .site-check-item.active {
            background: #ede9fe !important;
            border-color: #7c3aed !important;
        }

        .site-check-item.active .form-check-label {
            color: #5b21b6;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('assignSitesModal');

            function updateItemStyles() {
                document.querySelectorAll('.site-checkbox').forEach(cb => {
                    const wrapper = cb.closest('.site-check-item');
                    if (cb.checked) {
                        wrapper.classList.add('active');
                    } else {
                        wrapper.classList.remove('active');
                    }
                });
            }

            // Toggle style on checkbox click
            document.querySelectorAll('.site-checkbox').forEach(cb => {
                cb.addEventListener('change', updateItemStyles);
            });

            // Also toggle on clicking the whole row (but not on input or label)
            document.querySelectorAll('.site-check-item').forEach(item => {
                item.addEventListener('click', function (e) {
                    if (e.target.closest('input') || e.target.closest('label')) return;
                    const cb = item.querySelector('.site-checkbox');
                    cb.checked = !cb.checked;
                    updateItemStyles();
                });
            });

            modal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const employeeId = button.getAttribute('data-employee-id');
                const employeeName = button.getAttribute('data-employee-name');
                const assignedSites = JSON.parse(button.getAttribute('data-assigned-sites') || '[]');
                const notes = button.getAttribute('data-notes')?.trim() || '';

                document.getElementById('modalEmployeeName').textContent = employeeName;
                document.getElementById('modal_user_id').value = employeeId;
                document.getElementById('modal_notes_employee').value = notes;

                // Reset all checkboxes
                document.querySelectorAll('.site-checkbox').forEach(cb => cb.checked = false);

                // Check assigned sites
                assignedSites.forEach(siteId => {
                    const cb = document.getElementById('site_' + siteId);
                    if (cb) cb.checked = true;
                });

                document.getElementById('modal_notes_employee').value = '';

                updateItemStyles();
                feather.replace();
            });
        });
    </script>
@endsection