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

                    <div class="modal-body p-0">
                        <div class="px-4 pt-3 pb-2 bg-light border-bottom">
                            <div class="mb-3" id="employee_select_container">
                                <label class="form-label fw-bold small text-muted">Select Employee</label>
                                <select name="user_id_select" id="modal_user_id_select" class="form-select rounded-3 p-2 border-0 shadow-sm">
                                    <option value="">Choose an employee...</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <p class="text-muted mb-2">Manage shifts for <strong id="modalEmployeeName" class="text-dark">the selected employee</strong></p>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">
                                    <i data-feather="calendar" class="me-1" style="width: 14px;"></i>
                                    {{ $weekStart->format('d M') }} - {{ $weekEnd->format('d M, Y') }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="modal-body-scroll" style="max-height: 60vh; overflow-y: auto; padding: 1.5rem;">
                            <div id="days-container">
                                <!-- Day sections will be generated here -->
                            </div>

                            <div class="mt-4 p-3 bg-soft-secondary rounded-4">
                                <label class="form-label fw-bold small text-secondary uppercase tracking-wider">
                                    <i data-feather="file-text" class="me-1" style="width: 14px;"></i> Weekly Notes
                                </label>
                                <textarea name="notes" id="modal_notes" class="form-control border-0 rounded-3" rows="2"
                                    placeholder="Add any general instructions for the employee this week..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light p-3">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i data-feather="check" style="width: 18px; height: 18px;" class="me-1"></i> <span id="submit_btn_text">Save Schedule</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .day-section {
            background: #ffffff;
            border: 1px solid #edf2f7;
            border-radius: 1.25rem;
            margin-bottom: 1.25rem;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .day-section:hover {
            border-color: #cbd5e0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .day-header {
            padding: 0.75rem 1.25rem;
            background: #f8fafc;
            border-bottom: 1px solid #edf2f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .day-title {
            font-weight: 700;
            font-size: 0.9rem;
            color: #2d3748;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .day-date {
            font-weight: 500;
            font-size: 0.75rem;
            color: #718096;
        }

        .shifts-list {
            padding: 1rem;
        }

        .shift-item {
            background: #fdfcff;
            border: 1px solid #e9ecef;
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
            position: relative;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn-remove-shift {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ef4444;
            background: #fee2e2;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-remove-shift:hover {
            background: #ef4444;
            color: white;
            transform: scale(1.1);
        }

        .add-shift-btn {
            color: #7c3aed;
            background: #f5f3ff;
            border: 1px dashed #c4b5fd;
            border-radius: 0.75rem;
            padding: 0.5rem;
            width: 100%;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.2s;
        }

        .add-shift-btn:hover {
            background: #ede9fe;
            border-style: solid;
        }

        .empty-day-placeholder {
            text-align: center;
            padding: 1rem;
            color: #a0aec0;
            font-size: 0.8rem;
            font-style: italic;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    <script>
        let shiftIndex = 0;
        const currentMonday = "{{ $weekStart->format('Y-m-d') }}";
        const sites = @json($sites);

        function generateDaySections() {
            const container = document.getElementById('days-container');
            container.innerHTML = '';
            
            const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            
            days.forEach((day, i) => {
                const date = moment(currentMonday).add(i, 'days').format('YYYY-MM-DD');
                const isToday = moment().format('YYYY-MM-DD') === date;
                
                const section = document.createElement('div');
                section.className = 'day-section';
                section.id = `day-section-${date}`;
                
                section.innerHTML = `
                    <div class="day-header ${isToday ? 'bg-soft-primary' : ''}">
                        <h6 class="day-title">
                            <i data-feather="calendar" style="width: 14px; height: 14px;" class="${isToday ? 'text-primary' : 'text-muted'}"></i>
                            ${day} ${isToday ? '<span class="badge bg-primary ms-2" style="font-size: 0.6rem;">TODAY</span>' : ''}
                        </h6>
                        <span class="day-date">${moment(date).format('DD MMM, YYYY')}</span>
                    </div>
                    <div class="shifts-list" id="shifts-for-${date}">
                        <div class="empty-day-placeholder">No shifts assigned for this day</div>
                    </div>
                    <div class="px-3 pb-3">
                        <button type="button" class="btn add-shift-btn" onclick="addShiftToDay('${date}')">
                            <i data-feather="plus" style="width: 14px; height: 14px;" class="me-1"></i> Add Shift
                        </button>
                    </div>
                `;
                container.appendChild(section);
            });
            feather.replace();
        }

        function addShiftToDay(date, data = null) {
            const container = document.getElementById(`shifts-for-${date}`);
            const placeholder = container.querySelector('.empty-day-placeholder');
            if (placeholder) placeholder.remove();
            
            const index = shiftIndex++;
            const shiftItem = document.createElement('div');
            shiftItem.className = 'shift-item';
            
            let sitesHtml = `<option value="">Select Site</option>`;
            sites.forEach(site => {
                sitesHtml += `<option value="${site.id}" ${data && data.site_id == site.id ? 'selected' : ''}>${site.name}</option>`;
            });

            shiftItem.innerHTML = `
                <button type="button" class="btn-remove-shift" onclick="removeShift(this, '${date}')">
                    <i data-feather="x" style="width: 12px; height: 12px;"></i>
                </button>
                <input type="hidden" name="shifts[${index}][date]" value="${date}">
                <div class="row g-2 mb-2">
                    <div class="col-md-7">
                        <label class="small fw-bold text-muted mb-1">Assigned Site</label>
                        <select name="shifts[${index}][site_id]" class="form-select form-select-sm rounded-3 border-0 bg-light" required>
                            ${sitesHtml}
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="small fw-bold text-muted mb-1">Shift Name</label>
                        <input type="text" name="shifts[${index}][shift_name]" class="form-control form-control-sm rounded-3 border-0 bg-light" 
                               value="${data ? data.shift_name : 'Regular Shift'}" placeholder="e.g. Day Shift">
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="small fw-bold text-muted mb-1">Start Time</label>
                        <input type="time" name="shifts[${index}][start_time]" class="form-control form-control-sm rounded-3 border-0 bg-light" 
                               value="${data ? (data.start_time ? data.start_time.substring(0,5) : '08:00') : '08:00'}" required>
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold text-muted mb-1">End Time</label>
                        <input type="time" name="shifts[${index}][end_time]" class="form-control form-control-sm rounded-3 border-0 bg-light" 
                               value="${data ? (data.end_time ? data.end_time.substring(0,5) : '16:00') : '16:00'}" required>
                    </div>
                </div>
            `;
            
            container.appendChild(shiftItem);
            feather.replace();
        }

        function removeShift(btn, date) {
            const item = btn.closest('.shift-item');
            const container = document.getElementById(`shifts-for-${date}`);
            item.remove();
            
            if (container.children.length === 0) {
                container.innerHTML = `<div class="empty-day-placeholder">No shifts assigned for this day</div>`;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            feather.replace();

            const assignModalEl = document.getElementById('assignModal');
            const assignModal = new bootstrap.Modal(assignModalEl);
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
                submitBtnText.innerText = 'Save Schedule';
                document.getElementById('employee_select_container').classList.remove('d-none');
                document.getElementById('modalEmployeeName').textContent = 'the selected employee';
                
                shiftIndex = 0;
                generateDaySections();
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
                    submitBtnText.innerText = 'Update Schedule';
                    
                    document.getElementById('employee_select_container').classList.add('d-none');
                    document.getElementById('modalEmployeeName').textContent = userName;
                    userIdHidden.value = userId;
                    document.getElementById('modal_notes').value = notes;

                    // Reset and populate
                    shiftIndex = 0;
                    generateDaySections();

                    if (scheduleData.shifts && scheduleData.shifts.length > 0) {
                        scheduleData.shifts.forEach(s => {
                            addShiftToDay(s.date, s);
                        });
                    }

                    assignModal.show();
                    feather.replace();
                });
            });
        });
    </script>
@endsection