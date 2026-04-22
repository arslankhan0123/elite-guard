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
                            <div class="rounded-circle bg-soft-primary text-primary p-3 d-flex align-items-center justify-content-center"
                                style="width: 54px; height: 54px;">
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
                            <a href="{{ route('schedules.index', ['date' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}"
                                class="btn btn-outline-secondary rounded-pill px-3">
                                <i data-feather="chevron-left" class="me-1" style="width: 16px;"></i> Previous
                            </a>
                            <a href="{{ route('schedules.index', ['date' => Carbon\Carbon::now()->startOfWeek()->format('Y-m-d')]) }}"
                                class="btn btn-light rounded-pill px-3">
                                Current Week
                            </a>
                            <a href="{{ route('schedules.index', ['date' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}"
                                class="btn btn-outline-secondary rounded-pill px-3">
                                Next <i data-feather="chevron-right" class="ms-1" style="width: 16px;"></i>
                            </a>
                            <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm ms-2"
                                data-bs-toggle="modal" data-bs-target="#assignModal">
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
                                    @php
                                        $assignedSites = $schedule->shifts->pluck('site.name')->unique();
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-soft-info text-info p-2 me-3 d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i data-feather="user" style="width: 18px;"></i>
                                                </div>
                                                <div>
                                                    <span class="fw-bold text-dark d-block">{{ $schedule->user->name }}</span>
                                                    <small class="text-muted">{{ $schedule->user->role }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($assignedSites as $siteName)
                                                    <span class="badge bg-soft-primary text-primary rounded-pill px-2 py-1 fw-semibold" style="font-size: 0.75rem;">
                                                        <i data-feather="map-pin" style="width: 10px; height: 10px;" class="me-1"></i>
                                                        {{ $siteName }}
                                                    </span>
                                                @endforeach
                                            </div>
                                            <small class="text-muted mt-1 d-block">{{ $schedule->shifts->count() }} total shifts this week</small>
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
                                                <button type="button" class="editBtn edit-schedule-btn" title="Edit Assignments"
                                                    data-user-id="{{ $schedule->user_id }}" 
                                                    data-user-name="{{ $schedule->user->name }}"
                                                    data-notes="{{ $schedule->notes }}"
                                                    data-schedules="{{ $schedule->toJson() }}">
                                                    <svg viewBox="0 0 512 512">
                                                        <path
                                                            d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 490.7c-3.9 13 2.3 27.2 14.9 31.2s27.2-2.3 31.2-14.9L72 401.7c4.1-14.1 11.7-27 22.2-37.4L293.4 165.1l22.6-22.6 11.3-11.3 22.6-22.6 40.4 40.4 11.3 11.3 11.3-11.3zM461.1 117.8L394.2 50.9c-31.2-31.2-82.1-31.2-113.3 0l-11.3 11.3 96.2 96.2 11.3-11.3c31.2-31.2 31.2-82.1 0-113.3z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <a href="{{ route('schedules.delete', $schedule->id) }}" class="bin-button"
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to remove all assignments for this employee this week?');">
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
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i data-feather="inbox" style="width: 48px; height: 48px; opacity: 0.3;"
                                                    class="mb-3"></i>
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

    <!-- Assign Sites Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="assignModalLabel">
                        <i data-feather="map-pin" class="me-2 text-primary"></i> Assign Shifts
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignForm" action="{{ route('schedules.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" id="modal_user_id">
                    <input type="hidden" name="week_start_date" value="{{ $weekStart->format('Y-m-d') }}">

                    <div class="modal-body">
                        <div class="mb-3" id="employee_select_container">
                            <label class="form-label fw-bold">Select Employee</label>
                            <select name="user_id_select" id="modal_user_id_select" class="form-select rounded-3 p-2">
                                <option value="">Choose an employee...</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <p class="text-muted mb-3">Manage shifts for <strong id="modalEmployeeName">the selected employee</strong> for week <span
                                class="badge bg-light text-primary">{{ $weekStart->format('d M') }}
                                - {{ $weekEnd->format('d M, Y') }}</span></p>
                        
                        <div id="shifts-container" class="mb-3">
                            <!-- Shift rows will be added here dynamically -->
                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill w-100 mb-3 fw-bold" onclick="addShiftRow()">
                            <i data-feather="plus-circle" style="width: 14px; height: 14px;"></i> Add Another Shift
                        </button>

                        <div class="mt-3">
                            <label class="form-label fw-bold small">Weekly Notes (Optional)</label>
                            <textarea name="notes" id="modal_notes" class="form-control rounded-3" rows="2"
                                placeholder="General instructions for the week..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i data-feather="check" style="width: 16px; height: 16px;" class="me-1"></i> <span id="submit_btn_text">Save All Shifts</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        .shift-row {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 1rem;
            position: relative;
            transition: all 0.2s ease;
        }

        .shift-row:hover {
            border-color: #7c3aed;
            background: #fdfcff;
        }

        .btn-remove-shift {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            padding: 0.2rem 0.5rem;
            color: #ef4444;
            cursor: pointer;
            opacity: 0.6;
        }

        .btn-remove-shift:hover {
            opacity: 1;
        }

        .day-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 1px solid #e2e8f0;
            font-size: 0.7rem;
            font-weight: bold;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease;
        }

        .day-btn.active {
            background: #7c3aed;
            border-color: #7c3aed;
            color: white;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    <script>
        let shiftCount = 0;
        const currentMonday = "{{ $weekStart->format('Y-m-d') }}";
        const sites = @json($sites);

        function addShiftRow(data = null) {
            const container = document.getElementById('shifts-container');
            const index = shiftCount++;
            
            const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            const dayValues = [];
            for(let i=0; i<7; i++) {
                dayValues.push(moment(currentMonday).add(i, 'days').format('YYYY-MM-DD'));
            }

            const row = document.createElement('div');
            row.className = 'shift-row';
            row.id = `shift-row-${index}`;
            
            let sitesHtml = `<option value="">Select Site</option>`;
            sites.forEach(site => {
                sitesHtml += `<option value="${site.id}" ${data && data.site_id == site.id ? 'selected' : ''}>${site.name}</option>`;
            });

            row.innerHTML = `
                <div class="btn-remove-shift" onclick="this.closest('.shift-row').remove()">
                    <i data-feather="x-circle" style="width: 16px;"></i>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="small fw-bold">Site</label>
                        <select name="shifts[${index}][site_id]" class="form-select form-select-sm rounded-3" required>
                            ${sitesHtml}
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Shift Name</label>
                        <input type="text" name="shifts[${index}][shift_name]" class="form-control form-control-sm rounded-3" value="${data ? data.shift_name : 'Morning Shift'}" placeholder="e.g. Morning Shift">
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="small fw-bold">Start Time</label>
                        <input type="time" name="shifts[${index}][start_time]" class="form-control form-control-sm rounded-3" value="${data ? (data.start_time ? data.start_time.substring(0,5) : '08:00') : '08:00'}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">End Time</label>
                        <input type="time" name="shifts[${index}][end_time]" class="form-control form-control-sm rounded-3" value="${data ? (data.end_time ? data.end_time.substring(0,5) : '16:00') : '16:00'}" required>
                    </div>
                </div>
                <div class="d-flex justify-content-between gap-1">
                    ${days.map((day, i) => `
                        <div class="text-center">
                            <div class="day-btn ${data && data.dates.includes(dayValues[i]) ? 'active' : ''}" 
                                 onclick="toggleDay(this)" 
                                 title="${moment(dayValues[i]).format('DD MMM')}">
                                ${day[0]}
                            </div>
                            <input type="checkbox" name="shifts[${index}][dates][]" value="${dayValues[i]}" 
                                   class="day-checkbox d-none" ${data && data.dates.includes(dayValues[i]) ? 'checked' : ''}>
                        </div>
                    `).join('')}
                </div>
            `;
            
            container.appendChild(row);
            feather.replace();
        }

        function toggleDay(btn) {
            const checkbox = btn.parentElement.querySelector('.day-checkbox');
            checkbox.checked = !checkbox.checked;
            if (checkbox.checked) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            feather.replace();

            const assignModal = new bootstrap.Modal(document.getElementById('assignModal'));
            const assignForm = document.getElementById('assignForm');
            const modalTitle = document.getElementById('assignModalLabel');
            const submitBtnText = document.getElementById('submit_btn_text');
            const userSelect = document.getElementById('modal_user_id_select');
            const userIdHidden = document.getElementById('modal_user_id');

            userSelect.addEventListener('change', function() {
                userIdHidden.value = this.value;
                const name = this.options[this.selectedIndex].text;
                document.getElementById('modalEmployeeName').textContent = this.value ? name : 'the selected employee';
            });

            // Reset modal for New Assignment
            document.querySelector('[data-bs-target="#assignModal"]').addEventListener('click', function () {
                assignForm.reset();
                modalTitle.innerHTML = '<i data-feather="map-pin" class="me-2 text-primary"></i> Assign Shifts';
                submitBtnText.innerText = 'Save All Shifts';
                document.getElementById('employee_select_container').classList.remove('d-none');
                document.getElementById('modalEmployeeName').textContent = 'the selected employee';
                document.getElementById('shifts-container').innerHTML = '';
                shiftCount = 0;
                addShiftRow();
                feather.replace();
            });

            // Handle Edit button click
            document.querySelectorAll('.edit-schedule-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');
                    const notes = this.getAttribute('data-notes');
                    const scheduleData = JSON.parse(this.getAttribute('data-schedules'));

                    modalTitle.innerHTML = '<i data-feather="edit" class="me-2 text-warning"></i> Edit Shifts';
                    submitBtnText.innerText = 'Update Shifts';
                    
                    document.getElementById('employee_select_container').classList.add('d-none');
                    document.getElementById('modalEmployeeName').textContent = userName;
                    userIdHidden.value = userId;
                    document.getElementById('modal_notes').value = notes;

                    // Clear and populate shifts
                    document.getElementById('shifts-container').innerHTML = '';
                    shiftCount = 0;

                    if (scheduleData.shifts && scheduleData.shifts.length > 0) {
                        // Group shifts by pattern
                        const groups = {};
                        scheduleData.shifts.forEach(s => {
                            const key = `${s.site_id}-${s.start_time}-${s.end_time}-${s.shift_name}`;
                            if (!groups[key]) {
                                groups[key] = {
                                    site_id: s.site_id,
                                    start_time: s.start_time,
                                    end_time: s.end_time,
                                    shift_name: s.shift_name,
                                    dates: []
                                };
                            }
                            groups[key].dates.push(s.date);
                        });
                        Object.values(groups).forEach(group => addShiftRow(group));
                    } else {
                        addShiftRow();
                    }

                    assignModal.show();
                    feather.replace();
                });
            });
        });
    </script>
@endsection