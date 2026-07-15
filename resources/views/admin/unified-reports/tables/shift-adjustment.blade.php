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
            <td><a class="text-decoration-none text-dark" href="{{ route('reports.show', ['type' => $type, 'id' => $adjustment->id]) }}" data-bs-toggle="tooltip" title="View Details"><button class="view_btn me-2"></button></a></td>
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
