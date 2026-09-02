@extends('dashboardLayouts.main')

@section('title', 'Weekly Schedule')
@section('breadcrumbTitle', 'Weekly Schedule')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Schedule</li>
@endsection

@section('content')
    @php
        if (!function_exists('getShiftHours')) {
            function getShiftHours($startTime, $endTime) {
                if (!$startTime || !$endTime) return 0;
                $start = strtotime($startTime);
                $end = strtotime($endTime);
                if ($end < $start) {
                    $end += 86400; // Add 24 hours
                }
                return ($end - $start) / 3600.0;
            }
        }

        $daysOfWeek = [];
        for ($i = 0; $i < 7; $i++) {
            $daysOfWeek[] = $weekStart->copy()->addDays($i); // Monday to Sunday
        }

        $gridData = []; // [row_id => ['name' => 'Site / Runsheet Name', 'days' => [0 => [], 1 => [], ..., 6 => []]]]
        $siteTotalHours = [];
        $weeklyTotalHours = 0;

        foreach($schedules as $schedule) {
            $user = $schedule->user;
            if (!$user) continue;

            foreach($schedule->shifts as $shift) {
                $shiftHours = getShiftHours($shift->start_time, $shift->end_time);
                $weeklyTotalHours += $shiftHours;

                $shiftDate = \Carbon\Carbon::parse($shift->date);
                $dayIndex = $shiftDate->dayOfWeekIso - 1; // 0 = Monday, 6 = Sunday
                if ($dayIndex < 0 || $dayIndex > 6) {
                    continue;
                }

                $rowId = null;
                $rowName = null;

                if ($shift->type === 'runsheet' || $shift->weekly_run_sheet_id) {
                    $rsId = $shift->weekly_run_sheet_id ?? 'general';
                    $rowId = 'runsheet_' . $rsId;
                    $rsName = $shift->weeklyRunSheet->name ?? ($shift->shift_name ?: 'Mobile Patrol');
                    $rowName = 'Runsheet - ' . $rsName;
                } elseif ($shift->site) {
                    $rowId = (string) $shift->site_id;
                    $rowName = $shift->site->name;
                } else {
                    $rowId = 'unassigned_site';
                    $rowName = 'Unassigned Shift';
                }

                if (!isset($gridData[$rowId])) {
                    $gridData[$rowId] = [
                        'name' => $rowName,
                        'days' => array_fill(0, 7, [])
                    ];
                }

                if (!isset($siteTotalHours[$rowId])) {
                    $siteTotalHours[$rowId] = 0;
                }
                $siteTotalHours[$rowId] += $shiftHours;

                $userName = $user->name;
                $employeeId = $user->employee ? $user->employee->id : null;
                if (!isset($gridData[$rowId]['days'][$dayIndex][$userName])) {
                    $gridData[$rowId]['days'][$dayIndex][$userName] = [
                        'hours' => 0,
                        'employee_id' => $employeeId
                    ];
                }
                $gridData[$rowId]['days'][$dayIndex][$userName]['hours'] += $shiftHours;
            }
        }

        uasort($gridData, function($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });
    @endphp

    @php
        $queryParams = array_filter([
            'user_id' => $userId ?? null,
            'shift_type' => $shiftType ?? null,
            'site_id' => $siteId ?? null,
            'runsheet_id' => $runsheetId ?? null,
        ]);
    @endphp

    <div class="row">
        <div class="col-12">
            <!-- Week Navigation Header & Filters -->
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
                            <div class="me-2">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold" style="font-size: 0.85rem;">
                                    Weekly Total: {{ round($weeklyTotalHours, 1) }}h
                                </span>
                            </div>
                            <a href="{{ route('schedules.index', array_merge(['date' => $weekStart->copy()->subWeek()->format('Y-m-d')], $queryParams)) }}"
                                class="btn btn-outline-secondary rounded-pill px-3">
                                <i data-feather="chevron-left" class="me-1" style="width: 16px;"></i> Previous
                            </a>
                            <a href="{{ route('schedules.index', array_merge(['date' => Carbon\Carbon::now()->startOfWeek()->format('Y-m-d')], $queryParams)) }}"
                                class="btn btn-light rounded-pill px-3">
                                Current Week
                            </a>
                            <a href="{{ route('schedules.index', array_merge(['date' => $weekStart->copy()->addWeek()->format('Y-m-d')], $queryParams)) }}"
                                class="btn btn-outline-secondary rounded-pill px-3">
                                Next <i data-feather="chevron-right" class="ms-1" style="width: 16px;"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Filter Controls Bar -->
                    <div class="border-top pt-3 mt-3">
                        <form action="{{ route('schedules.index') }}" method="GET" id="scheduleFilterForm">
                            <input type="hidden" name="date" value="{{ $weekStart->format('Y-m-d') }}">
                            
                            <div class="row g-2 align-items-end">
                                <!-- Filter by Employee -->
                                <div class="col-md-3 col-sm-6">
                                    <label for="user_id_filter" class="form-label small text-muted fw-semibold mb-1">Employee</label>
                                    <select name="user_id" id="user_id_filter" class="form-select form-select-sm border-0 bg-light rounded-3 fw-medium" style="height: 38px;">
                                        <option value="">All Employees</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}" {{ ($userId ?? '') == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filter by Shift Type -->
                                <div class="col-md-3 col-sm-6">
                                    <label for="shift_type_filter" class="form-label small text-muted fw-semibold mb-1">Type</label>
                                    <select name="shift_type" id="shift_type_filter" class="form-select form-select-sm border-0 bg-light rounded-3 fw-medium" onchange="toggleFilterTargetDropdowns()" style="height: 38px;">
                                        <option value="all" {{ ($shiftType ?? 'all') == 'all' ? 'selected' : '' }}>All Types</option>
                                        <option value="site" {{ ($shiftType ?? '') == 'site' ? 'selected' : '' }}>Assigned Site</option>
                                        <option value="runsheet" {{ ($shiftType ?? '') == 'runsheet' ? 'selected' : '' }}>Runsheet</option>
                                    </select>
                                </div>

                                <!-- Dynamic Site Filter -->
                                <div class="col-md-3 col-sm-6" id="site_filter_wrapper" style="{{ ($shiftType ?? '') == 'site' ? 'display: block;' : 'display: none;' }}">
                                    <label for="site_id_filter" class="form-label small text-muted fw-semibold mb-1">Select Site</label>
                                    <select name="site_id" id="site_id_filter" class="form-select form-select-sm border-0 bg-light rounded-3 fw-medium" style="height: 38px;">
                                        <option value="">All Sites</option>
                                        @foreach($sites as $st)
                                            <option value="{{ $st->id }}" {{ ($siteId ?? '') == $st->id ? 'selected' : '' }}>
                                                {{ $st->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Dynamic Runsheet Filter -->
                                <div class="col-md-3 col-sm-6" id="runsheet_filter_wrapper" style="{{ ($shiftType ?? '') == 'runsheet' ? 'display: block;' : 'display: none;' }}">
                                    <label for="runsheet_id_filter" class="form-label small text-muted fw-semibold mb-1">Select Runsheet</label>
                                    <select name="runsheet_id" id="runsheet_id_filter" class="form-select form-select-sm border-0 bg-light rounded-3 fw-medium" style="height: 38px;">
                                        <option value="">All Runsheets</option>
                                        @foreach($weeklyRunSheets as $rs)
                                            <option value="{{ $rs->id }}" {{ ($runsheetId ?? '') == $rs->id ? 'selected' : '' }}>
                                                {{ $rs->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Placeholder for alignment when "All Types" is selected -->
                                <div class="col-md-3 col-sm-6" id="default_filter_wrapper" style="{{ (empty($shiftType) || $shiftType == 'all') ? 'display: block;' : 'display: none;' }}">
                                    <label class="form-label small text-muted fw-semibold mb-1">Selection</label>
                                    <select class="form-select form-select-sm border-0 bg-light rounded-3 text-muted" disabled style="height: 38px;">
                                        <option>Select Type First</option>
                                    </select>
                                </div>

                                <!-- Filter & Reset Buttons -->
                                <div class="col-md-3 col-sm-6 d-flex align-items-center gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold d-flex align-items-center" style="height: 38px;">
                                        <i data-feather="filter" class="me-1" style="width: 14px;"></i> Filter
                                    </button>
                                    @if(!empty($userId) || (!empty($shiftType) && $shiftType != 'all') || !empty($siteId) || !empty($runsheetId))
                                        <a href="{{ route('schedules.index', ['date' => $weekStart->format('Y-m-d')]) }}" class="btn btn-light btn-sm rounded-pill px-3 text-muted fw-semibold d-flex align-items-center" style="height: 38px;">
                                            Reset
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <!-- Assignments Grid Table -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center mb-0" style="border-color: #e2e8f0;">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 180px; background-color: #f8fafc; color: #1e293b; font-weight: 700;" class="text-start">Sites</th>
                                    @foreach($daysOfWeek as $day)
                                        <th style="background-color: #f8fafc; color: #1e293b; font-weight: 700;">
                                            <div class="fw-bold">{{ $day->format('l') }}</div>
                                            <div class="small text-muted">{{ $day->format('d-M') }}</div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gridData as $siteId => $siteData)
                                    <tr>
                                        <td class="fw-bold text-dark text-start align-middle" style="background-color: #fcfdfe;">
                                            {{ $siteData['name'] }}
                                            <div class="small text-muted fw-semibold mt-1">Total: {{ round($siteTotalHours[$siteId] ?? 0, 1) }}h</div>
                                        </td>
                                        @foreach($siteData['days'] as $dayIndex => $dayUsers)
                                            <td class="align-middle">
                                                @if(count($dayUsers) > 0)
                                                    @foreach($dayUsers as $userName => $userData)
                                                        <div class="fw-bold text-primary my-1">
                                                            @if(!empty($userData['employee_id']))
                                                                <a href="{{ route('employees.show', $userData['employee_id']) }}" class="text-primary text-decoration-underline fw-bold">
                                                                    {{ $userName }}
                                                                </a>
                                                            @else
                                                                {{ $userName }}
                                                            @endif
                                                            <span class="text-secondary small fw-normal">({{ round($userData['hours'], 1) }}h)</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted small">---</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-muted">
                                                <i data-feather="inbox" style="width: 48px; height: 48px; opacity: 0.3;" class="mb-3"></i>
                                                <p class="mb-0">No assignments found for this week.</p>
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

    <!-- Assign Sites Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
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

        .modal-xl {
            max-width: 90%;
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
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .shift-item {
            background: #fdfcff;
            border: 1px solid #e9ecef;
            border-radius: 1rem;
            padding: 1rem;
            flex: 0 0 calc(33.33% - 0.7rem);
            position: relative;
            animation: slideIn 0.3s ease-out;
        }

        @media (max-width: 992px) {
            .shift-item {
                flex: 0 0 calc(50% - 0.5rem);
            }
        }

        @media (max-width: 768px) {
            .shift-item {
                flex: 0 0 100%;
            }
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
        const weeklyRunSheets = @json($weeklyRunSheets);

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
            
            const isRunsheet = data && data.type === 'runsheet';
            
            let sitesHtml = `<option value="">Select Site</option>`;
            sites.forEach(site => {
                sitesHtml += `<option value="${site.id}" ${data && !isRunsheet && data.site_id == site.id ? 'selected' : ''}>${site.name}</option>`;
            });

            let weeklyRunSheetsHtml = `<option value="">Select Runsheet</option>`;
            weeklyRunSheets.forEach(rs => {
                weeklyRunSheetsHtml += `<option value="${rs.id}" ${data && data.weekly_run_sheet_id == rs.id ? 'selected' : ''}>${rs.name}</option>`;
            });

            shiftItem.innerHTML = `
                <button type="button" class="btn-remove-shift" onclick="removeShift(this, '${date}')">
                    <i data-feather="x" style="width: 12px; height: 12px;"></i>
                </button>
                <input type="hidden" name="shifts[${index}][id]" value="${data ? data.id : ''}">
                <input type="hidden" name="shifts[${index}][date]" value="${date}">

                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1">Shift Type</label>
                        <select name="shifts[${index}][type]" class="form-select form-select-sm rounded-3 border-0 bg-light shift-type-select" required onchange="onShiftTypeChange(this, '${date}')">
                            <option value="site" ${!isRunsheet ? 'selected' : ''}>Assigned Site</option>
                            <option value="runsheet" ${isRunsheet ? 'selected' : ''}>Runsheet</option>
                        </select>
                    </div>
                    <div class="col-md-4 site-select-container" style="${isRunsheet ? 'display:none;' : ''}">
                        <label class="small fw-bold text-muted mb-1">Assigned Site</label>
                        <select name="shifts[${index}][site_id]" class="form-select form-select-sm rounded-3 border-0 bg-light site-select-input" ${!isRunsheet ? 'required' : ''} onchange="onSiteSelectChange(this)">
                            ${sitesHtml}
                        </select>
                    </div>
                    <div class="col-md-4 runsheet-select-container" style="${!isRunsheet ? 'display:none;' : ''}">
                        <label class="small fw-bold text-muted mb-1">Select Runsheet</label>
                        <select name="shifts[${index}][weekly_run_sheet_id]" class="form-select form-select-sm rounded-3 border-0 bg-light runsheet-select-input" ${isRunsheet ? 'required' : ''} onchange="onRunsheetSelectChange(this, '${date}')">
                            ${weeklyRunSheetsHtml}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1">Shift Name</label>
                        <input type="text" name="shifts[${index}][shift_name]" class="form-control form-control-sm rounded-3 border-0 bg-light shift-name-input" 
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

        		function onShiftTypeChange(selectElement, date) {
            const shiftItem = selectElement.closest('.shift-item');
            if (!shiftItem) return;

            const type = selectElement.value;
            const siteContainer = shiftItem.querySelector('.site-select-container');
            const runsheetContainer = shiftItem.querySelector('.runsheet-select-container');
            const siteSelect = shiftItem.querySelector('.site-select-input');
            const runsheetSelect = shiftItem.querySelector('.runsheet-select-input');

            if (type === 'runsheet') {
                siteContainer.style.display = 'none';
                siteSelect.removeAttribute('required');
                siteSelect.value = '';
                runsheetContainer.style.display = 'block';
                runsheetSelect.setAttribute('required', 'required');
                if (runsheetSelect.value) {
                    onRunsheetSelectChange(runsheetSelect, date);
                }
            } else {
                runsheetContainer.style.display = 'none';
                runsheetSelect.removeAttribute('required');
                siteContainer.style.display = 'block';
                siteSelect.setAttribute('required', 'required');
            }
        }

        function onRunsheetSelectChange(selectElement, date) {
            const weeklyRunSheetId = selectElement.value;
            const shiftItem = selectElement.closest('.shift-item');
            if (!shiftItem) return;

            const siteSelect = shiftItem.querySelector('.site-select-input');
            const shiftNameInput = shiftItem.querySelector('.shift-name-input');
            const startTimeInput = shiftItem.querySelector('input[type="time"][name*="[start_time]"]');
            const endTimeInput = shiftItem.querySelector('input[type="time"][name*="[end_time]"]');

            if (!weeklyRunSheetId) return;

            const runsheet = weeklyRunSheets.find(rs => rs.id == weeklyRunSheetId);
            if (runsheet) {
                const dayOfWeek = moment(date).isoWeekday();
                const entry = (runsheet.entries || []).find(e => e.day_of_week == dayOfWeek) || (runsheet.entries || [])[0];
                const dayTime = (runsheet.day_times || {})[dayOfWeek] || (runsheet.day_times || {})[1] || {};

                const startTimeVal = (entry && entry.start_time) ? entry.start_time : (dayTime.start_time || '08:00');
                const endTimeVal = (entry && entry.end_time) ? entry.end_time : (dayTime.end_time || '16:00');

                if (siteSelect) siteSelect.value = '';

                if (entry) {
                    if (shiftNameInput) shiftNameInput.value = entry.tour_name || runsheet.name;
                } else {
                    if (shiftNameInput) shiftNameInput.value = runsheet.name;
                }

                if (startTimeInput) startTimeInput.value = startTimeVal.substring(0, 5);
                if (endTimeInput) endTimeInput.value = endTimeVal.substring(0, 5);
            }
        }

        function onSiteSelectChange(selectElement) {
            const siteId = selectElement.value;
            if (!siteId) return;

            const site = sites.find(s => s.id == siteId);
            if (site) {
                const parentItem = selectElement.closest('.shift-item');
                if (parentItem) {
                    const startTimeInput = parentItem.querySelector('input[type="time"][name*="[start_time]"]');
                    const endTimeInput = parentItem.querySelector('input[type="time"][name*="[end_time]"]');
                    
                    if (startTimeInput) {
                        startTimeInput.value = site.start_time ? site.start_time.substring(0, 5) : '08:00';
                    }
                    if (endTimeInput) {
                        endTimeInput.value = site.end_time ? site.end_time.substring(0, 5) : '16:00';
                    }
                }
            }
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

        function toggleFilterTargetDropdowns() {
            const typeSelect = document.getElementById('shift_type_filter');
            if (!typeSelect) return;
            const typeVal = typeSelect.value;

            const siteWrapper = document.getElementById('site_filter_wrapper');
            const runsheetWrapper = document.getElementById('runsheet_filter_wrapper');
            const defaultWrapper = document.getElementById('default_filter_wrapper');

            const siteSelect = document.getElementById('site_id_filter');
            const runsheetSelect = document.getElementById('runsheet_id_filter');

            if (typeVal === 'site') {
                if (siteWrapper) siteWrapper.style.display = 'block';
                if (runsheetWrapper) runsheetWrapper.style.display = 'none';
                if (defaultWrapper) defaultWrapper.style.display = 'none';
                if (runsheetSelect) runsheetSelect.value = '';
            } else if (typeVal === 'runsheet') {
                if (siteWrapper) siteWrapper.style.display = 'none';
                if (runsheetWrapper) runsheetWrapper.style.display = 'block';
                if (defaultWrapper) defaultWrapper.style.display = 'none';
                if (siteSelect) siteSelect.value = '';
            } else {
                if (siteWrapper) siteWrapper.style.display = 'none';
                if (runsheetWrapper) runsheetWrapper.style.display = 'none';
                if (defaultWrapper) defaultWrapper.style.display = 'block';
                if (siteSelect) siteSelect.value = '';
                if (runsheetSelect) runsheetSelect.value = '';
            }
        }
    </script>
@endsection