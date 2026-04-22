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
                                            <div class="btn-group" role="group" aria-label="Employee Actions">
                                                <button type="button" class="btn btn-sm me-2 btn-outline-primary rounded-pill px-3"
                                                    title="Assign Sites" data-bs-toggle="modal"
                                                    data-bs-target="#assignSitesModal"
                                                    data-employee-id="{{ $employee->user->id }}"
                                                    data-employee-name="{{ $employee->user->name }}"
                                                    data-schedules="{{ $employee->user->schedules->toJson() }}"
                                                    data-notes="{{ $weeklyNote }}">
                                                    <i data-feather="map" style="width: 14px; height: 14px;"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm me-2 btn-outline-success rounded-pill px-3"
                                                    title="Offer Letter" data-bs-toggle="modal"
                                                    data-bs-target="#offerLetterModal"
                                                    data-user-id="{{ $employee->user->id }}"
                                                    data-name="{{ $employee->user->name }}"
                                                    data-job-title="{{ $employee->user->offerLetter->job_title ?? '' }}"
                                                    data-joining-date="{{ $employee->user->offerLetter->joining_date ?? '' }}"
                                                    data-salary="{{ $employee->user->offerLetter->salary ?? '' }}"
                                                    data-description="{{ $employee->user->offerLetter->description ?? '' }}"
                                                    data-is-email-sent="{{ ($employee->user->offerLetter->is_email_sent ?? false) ? '1' : '0' }}"
                                                    data-is-accepted="{{ ($employee->user->offerLetter->is_accepted ?? false) ? '1' : '0' }}"
                                                    data-signed-at="{{ $employee->user->offerLetter->signed_at ?? '' }}"
                                                    data-signature="{{ $employee->user->offerLetter->signature ?? '' }}">
                                                    <i data-feather="mail" style="width: 14px; height: 14px;"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm me-2 btn-outline-info rounded-pill px-3"
                                                    title="Pay Slip" data-bs-toggle="modal"
                                                    data-bs-target="#paySlipModal"
                                                    data-user-id="{{ $employee->user->id }}"
                                                    data-name="{{ $employee->user->name }}">
                                                    <i data-feather="file-text" style="width: 14px; height: 14px;"></i>
                                                </button>
                                                <a class="text-decoration-none me-2 text-dark ml-1" href="{{ route('employees.edit', $employee->id) }}" data-bs-toggle="tooltip" title="Edit Employee">
                                                    <button class="editBtn">
                                                        <svg height="1em" viewBox="0 0 512 512">
                                                            <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                                                        </svg>
                                                    </button>
                                                </a>
                                                <a href="{{ route('employees.delete', $employee->id) }}" class="bin-button ml-1" data-bs-toggle="tooltip" title="Delete Employee" onclick="return confirm('Are you sure you want to delete this employee?')">
                                                    <svg class="bin-top" viewBox="0 0 39 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <line y1="5" x2="39" y2="5" stroke="white" stroke-width="4"></line>
                                                        <line x1="12" y1="1.5" x2="26.0357" y2="1.5" stroke="white" stroke-width="3"></line>
                                                    </svg>
                                                    <svg class="bin-bottom" viewBox="0 0 33 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <mask id="path-1-inside-1_8_19" fill="white">
                                                            <path d="M0 0H33V35C33 37.2091 31.2091 39 29 39H4C1.79086 39 0 37.2091 0 35V0Z"></path>
                                                        </mask>
                                                        <path d="M0 0H33H0ZM37 35C37 39.4183 33.4183 43 29 43H4C-0.418278 43 -4 39.4183 -4 35H4H29H37ZM4 43C-0.418278 43 -4 39.4183 -4 35V0H4V35V43ZM37 0V35C37 39.4183 33.4183 43 29 43V35V0H37Z" fill="white" mask="url(#path-1-inside-1_8_19)"></path>
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
        <!-- ... same as before, no change needed here ... -->
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
                        <p class="text-muted mb-3">Manage shifts for <strong id="modalEmployeeName"></strong> for week <span
                                class="badge bg-light text-primary">{{ \Carbon\Carbon::parse($currentMonday)->format('d M') }}
                                - {{ \Carbon\Carbon::parse($currentMonday)->addDays(6)->format('d M, Y') }}</span></p>
                        
                        <div id="shifts-container" class="mb-3">
                            <!-- Shift rows will be added here dynamically -->
                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill w-100 mb-3 fw-bold" onclick="addShiftRow()">
                            <i data-feather="plus-circle" style="width: 14px; height: 14px;"></i> Add Another Shift
                        </button>

                        <div class="mt-3">
                            <label class="form-label fw-bold small">Weekly Notes (Optional)</label>
                            <textarea name="notes" id="modal_notes_employee" class="form-control rounded-3" rows="2"
                                placeholder="General instructions for the week..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i data-feather="check" style="width: 16px; height: 16px;" class="me-1"></i> Save All Shifts
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Offer Letter Modal -->
    <div class="modal fade" id="offerLetterModal" tabindex="-1" aria-labelledby="offerLetterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="offerLetterModalLabel">
                        <i data-feather="mail" class="me-2 text-success"></i> Employee Offer Letter
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="offerLetterForm" action="{{ route('employees.updateOfferLetter') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" id="offer_user_id">
                    
                    <div class="modal-body">
                        <p class="text-muted">Managing offer letter details for <strong id="offerEmployeeName"></strong></p>

                        <!-- Accepted Details (Signature & Signed At) -->
                        <div id="offer_accepted_section" class="col-12 mt-3 d-none">
                            <div class="p-3 rounded-4 border bg-light">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-success">
                                            <i data-feather="check-circle" class="me-1" style="width: 14px;"></i> Signed At
                                        </label>
                                        <input type="text" id="offer_signed_at" class="form-control rounded-3 bg-white" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-success">
                                            <i data-feather="edit-3" class="me-1" style="width: 14px;"></i> Signature
                                        </label>
                                        <input type="text" id="offer_signature" class="form-control rounded-3 bg-white" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Joining Date</label>
                                <input type="date" name="joining_date" id="offer_joining_date" class="form-control rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Job Title</label>
                                <input type="text" name="job_title" id="offer_job_title" class="form-control rounded-3" placeholder="e.g. Security Guard">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Salary / Wage</label>
                                <input type="text" name="salary" id="offer_salary" class="form-control rounded-3" placeholder="e.g. $20/hr or $4000/month">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small">Description / Terms & Conditions</label>
                                <textarea name="description" id="offer_description" class="form-control rounded-3" rows="6" placeholder="Detailed job description and terms..."></textarea>
                            </div>
                            
                            <!-- Send Email Checkbox -->
                            <div class="col-12 mt-3 p-3 rounded-4" style="background-color: #f5f3ff; border: 1px dashed #7c3aed;">
                                <div class="form-check form-switch d-flex align-items-center gap-3">
                                    <input class="form-check-input" type="checkbox" name="send_email" id="offer_send_email" value="1" style="width: 45px; height: 22px; cursor: pointer;">
                                    <label class="form-check-label fw-bold text-primary mb-0" for="offer_send_email" style="cursor: pointer;">
                                        <i data-feather="send" class="me-1" style="width: 14px;"></i> Send Offer Letter to Employee via Email
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2 ms-5">If enabled, the employee will receive a professional offer email with the details above.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                            <i data-feather="save" style="width: 16px; height: 16px;" class="me-1"></i> Save Offer Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        </div>
    </div>

    <!-- Pay Slip Modal -->
    <div class="modal fade" id="paySlipModal" tabindex="-1" aria-labelledby="paySlipModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="paySlipModalLabel">
                        <i data-feather="file-text" class="me-2 text-info"></i> Employee Pay Slip
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="paySlipForm" action="{{ route('employees.updatePaySlip') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" id="payslip_user_id">
                    
                    <div class="modal-body">
                        <p class="text-muted mb-4">Upload monthly pay slip for <strong id="payslipEmployeeName"></strong> (<strong id="userIdText"></strong>)</p>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Select Month</label>
                                <select name="month" id="payslip_month" class="form-select rounded-3">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Select Year</label>
                                <select name="year" id="payslip_year" class="form-select rounded-3">
                                    <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                                    <option value="{{ date('Y') - 1 }}">{{ date('Y') - 1 }}</option>
                                    <option value="{{ date('Y') + 1 }}">{{ date('Y') + 1 }}</option>
                                </select>
                            </div>
                            <div class="col-12 mt-3">
                                <label class="form-label fw-bold small">Pay Slip File (PDF, DOC, PNG, JPG)</label>
                                <div class="input-group">
                                    <input type="file" name="file" id="payslip_file" class="form-control rounded-3" required
                                        accept=".pdf,.doc,.docx,.png,.jpg,.jpeg">
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i data-feather="info" style="width: 12px; height: 12px;"></i>
                                    If a pay slip already exists for the selected month/year, it will be updated.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info text-white rounded-pill px-4 fw-bold">
                            <i data-feather="upload" style="width: 16px; height: 16px;" class="me-1"></i> Upload Pay Slip
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
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
        const currentMonday = "{{ $currentMonday }}";
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
                        <input type="time" name="shifts[${index}][start_time]" class="form-control form-control-sm rounded-3" value="${data ? data.start_time : '08:00'}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">End Time</label>
                        <input type="time" name="shifts[${index}][end_time]" class="form-control form-control-sm rounded-3" value="${data ? data.end_time : '16:00'}" required>
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
            const modal = document.getElementById('assignSitesModal');

            modal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const employeeId = button.getAttribute('data-employee-id');
                const employeeName = button.getAttribute('data-employee-name');
                const schedules = JSON.parse(button.getAttribute('data-schedules') || '[]');
                const notes = button.getAttribute('data-notes')?.trim() || '';

                document.getElementById('modalEmployeeName').textContent = employeeName;
                document.getElementById('modal_user_id').value = employeeId;
                document.getElementById('modal_notes_employee').value = notes;

                // Clear existing shift rows
                document.getElementById('shifts-container').innerHTML = '';
                shiftCount = 0;

                if (schedules.length > 0) {
                    // Group schedules by shift pattern (site, start_time, end_time, shift_name)
                    const groups = {};
                    schedules.forEach(s => {
                        const key = `${s.site_id}-${s.start_time}-${s.end_time}-${s.shift_name}`;
                        if (!groups[key]) {
                            groups[key] = {
                                site_id: s.site_id,
                                start_time: s.start_time ? s.start_time.substring(0, 5) : '08:00',
                                end_time: s.end_time ? s.end_time.substring(0, 5) : '16:00',
                                shift_name: s.shift_name,
                                dates: []
                            };
                        }
                        groups[key].dates.push(s.date);
                    });

                    Object.values(groups).forEach(group => addShiftRow(group));
                } else {
                    addShiftRow(); // Add one empty row by default
                }

                feather.replace();
            });

            const offerModal = document.getElementById('offerLetterModal');
            offerModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const employeeName = button.getAttribute('data-name');
                const jobTitle = button.getAttribute('data-job-title');
                const joiningDate = button.getAttribute('data-joining-date');
                const salary = button.getAttribute('data-salary');
                const description = button.getAttribute('data-description');
                const isEmailSent = button.getAttribute('data-is-email-sent') === '1';
                const isAccepted = button.getAttribute('data-is-accepted') === '1';
                const signedAt = button.getAttribute('data-signed-at');
                const signature = button.getAttribute('data-signature');

                document.getElementById('offer_user_id').value = userId;
                document.getElementById('offerEmployeeName').textContent = employeeName;
                document.getElementById('offer_job_title').value = jobTitle;
                document.getElementById('offer_joining_date').value = joiningDate;
                document.getElementById('offer_salary').value = salary;
                document.getElementById('offer_description').value = description;
                document.getElementById('offer_send_email').checked = isEmailSent;

                // Handle Accepted Section
                const acceptedSection = document.getElementById('offer_accepted_section');
                if (isAccepted || signedAt || signature) {
                    acceptedSection.classList.remove('d-none');
                    document.getElementById('offer_signed_at').value = signedAt || 'N/A';
                    document.getElementById('offer_signature').value = signature || 'Digital Signature N/A';
                } else {
                    acceptedSection.classList.add('d-none');
                }

                feather.replace();
            });

            const payslipModal = document.getElementById('paySlipModal');
            payslipModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const employeeName = button.getAttribute('data-name');

                document.getElementById('payslip_user_id').value = userId;
                document.getElementById('userIdText').textContent = userId;
                document.getElementById('payslipEmployeeName').textContent = employeeName;

                feather.replace();
            });
        });
    </script>
@endsection