<table id="custom-table" class="table table-striped table-bordered">
    <thead>
        <tr class="table-dark">
            <th>ID</th>
            <th>Employee</th>
            <th>Position/Site</th>
            <th>Current Shift</th>
            <th>Requested Adjustment</th>
            <th>Replacement & Reason</th>
            <th>Approval</th>
            <th>Created At</th>
        <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['adjustments'] as $adjustment)
            <tr>
                <td>{{ $adjustment->id }}</td>
                <td>
                    <strong>{{ $adjustment->employee_name }}</strong><br>
                    @if($adjustment->employee_id)
                        <small class="text-muted">ID: {{ $adjustment->employee_id }}</small>
                    @endif
                </td>
                <td>
                    {{ $adjustment->position_site }}<br>
                    @if($adjustment->department)
                        <small class="text-muted">Dept: {{ $adjustment->department }}</small>
                    @endif
                </td>
                <td>
                    <strong>Date:</strong> {{ $adjustment->current_date ? $adjustment->current_date->format('j M Y') : 'N/A' }}<br>
                    <strong>Time:</strong> {{ $adjustment->current_start_time ?? 'N/A' }} - {{ $adjustment->current_end_time ?? 'N/A' }}<br>
                    <small class="text-muted">Type: {{ $adjustment->current_shift_type ?? 'N/A' }}</small>
                </td>
                <td>
                    <div class="d-flex flex-wrap gap-1">
                        @if($adjustment->shift_swap) <span class="badge bg-primary">Shift Swap</span> @endif
                        @if($adjustment->late_start) <span class="badge bg-secondary">Late Start</span> @endif
                        @if($adjustment->coverage_request) <span class="badge bg-info">Coverage</span> @endif
                        @if($adjustment->early_release) <span class="badge bg-warning text-dark">Early Release</span> @endif
                        @if($adjustment->time_off_request) <span class="badge bg-danger">Time Off</span> @endif
                        @if($adjustment->overtime_approval) <span class="badge bg-success">Overtime</span> @endif
                    </div>
                    @if($adjustment->requested_date)
                        <div class="mt-1">
                            <small class="text-muted">
                                <strong>Req. Date:</strong> {{ $adjustment->requested_date->format('j M Y') }}<br>
                                <strong>Req. Time:</strong> {{ $adjustment->requested_start_time ?? 'N/A' }} - {{ $adjustment->requested_end_time ?? 'N/A' }}
                            </small>
                        </div>
                    @endif
                </td>
                <td>
                    @if($adjustment->replacement_employee)
                        <strong>Replacement:</strong> {{ $adjustment->replacement_employee }}<br>
                    @endif
                    <strong>Reason:</strong> {{ $adjustment->adjustment_reason ?? 'N/A' }}
                    @if($adjustment->additional_details)
                        <br><small class="text-muted">Details: {{ $adjustment->additional_details }}</small>
                    @endif
                </td>
                <td>
                    @if($adjustment->decision == 'Approved')
                        <span class="badge bg-success">Approved</span>
                    @elseif($adjustment->decision == 'Denied')
                        <span class="badge bg-danger">Denied</span>
                    @else
                        <span class="badge bg-warning text-dark">Pending</span>
                    @endif

                    @if($adjustment->supervisor_name)
                        <div class="mt-1">
                            <small class="text-muted">By: {{ $adjustment->supervisor_name }}</small>
                        </div>
                    @endif
                    @if($adjustment->approval_date)
                        <small class="text-muted">On: {{ $adjustment->approval_date->format('j M Y') }}</small>
                    @endif
                </td>
                <td>{{ $adjustment->created_at->format('j M Y H:i') }}</td>
            <td>
                <div class="d-flex align-items-center">
                    <a class="text-decoration-none text-dark me-2" href="{{ route('reports.show', ['type' => $type, 'id' => $adjustment->id]) }}" data-bs-toggle="tooltip" title="View Details">
                        <button class="view_btn"></button>
                    </a>
                    <a class="text-decoration-none text-dark me-2" href="{{ route('reports.edit', ['type' => $type, 'id' => $adjustment->id]) }}" data-bs-toggle="tooltip" title="Edit">
                        <button class="editBtn">
                            <svg height="1em" viewBox="0 0 512 512">
                                <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                            </svg>
                        </button>
                    </a>
                    <a class="text-decoration-none text-dark" href="{{ route('reports.download', ['type' => $type, 'id' => $adjustment->id]) }}" data-bs-toggle="tooltip" title="Download PDF">
                        <button class="btn btn-sm btn-danger px-2 d-flex align-items-center justify-content-center" style="height: 1.875rem; width: 1.875rem; border-radius: 6px;">
                            <i class="fa-solid fa-file-arrow-down" style="font-size: 18px; color: white;"></i>
                        </button>
                    </a>
                </div>
            </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center">No shift adjustment requests found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3">
    {{ $data['adjustments']->links() }}
</div>
