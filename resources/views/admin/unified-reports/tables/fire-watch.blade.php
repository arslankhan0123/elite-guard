<table id="custom-table" class="table table-striped table-bordered">
    <thead>
        <tr class="table-dark">
            <th>ID</th>
            <th>User</th>
            <th>Client/Site Name</th>
            <th>Address/Location</th>
            <th>Reason</th>
            <th>Commenced</th>
            <th>Terminated</th>
            <th>Interval</th>
            <th>Patrol Logs</th>
            <th>Created At</th>
        <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['reports'] as $report)
            <tr>
                <td>{{ $report->id }}</td>
                <td>
                    <strong>{{ $report->user->name ?? 'N/A' }}</strong><br>
                    <small class="text-muted">{{ $report->user->email ?? '' }}</small>
                </td>
                <td>{{ $report->client_site_name }}</td>
                <td>{{ $report->address_location }}</td>
                <td>{{ $report->reason_for_fire_watch }}</td>
                <td>
                    {{ $report->commenced_date }}<br>
                    <small class="text-muted">{{ $report->commenced_time }}</small>
                </td>
                <td>
                    {{ $report->terminated_date }}<br>
                    <small class="text-muted">{{ $report->terminated_time }}</small>
                </td>
                <td>{{ $report->patrol_interval }}</td>
                <td>
                    @if($report->patrolLogs->count() > 0)
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#patrolModal{{ $report->id }}">
                            View {{ $report->patrolLogs->count() }} Logs
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="patrolModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title text-white">Patrol Logs - Report #{{ $report->id }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body bg-light">
                                        <div class="row g-3">
                                            @foreach($report->patrolLogs as $log)
                                                <div class="col-md-4">
                                                    <div class="card h-100 border-0 shadow-sm mb-0">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                                <span class="badge bg-primary">{{ $log->round ?? 'N/A' }}</span>
                                                                <small class="text-muted fw-semibold">{{ $log->date }}</small>
                                                            </div>
                                                            <div class="d-flex align-items-center mb-2">
                                                                <div class="avatar-xs me-2">
                                                                    <span class="avatar-title rounded-circle bg-primary-subtle text-primary font-size-11">
                                                                        <i class="mdi mdi-clock-outline"></i>
                                                                    </span>
                                                                </div>
                                                                <h6 class="mb-0 text-primary">{{ $log->start_time }} - {{ $log->end_time }}</h6>
                                                            </div>
                                                            <p class="text-muted mb-2 font-size-13">
                                                                <strong>Findings:</strong> {{ $log->area_patrolled_findings ?? 'N/A' }}
                                                            </p>
                                                            @if($log->initials)
                                                                <div class="text-end">
                                                                    <small class="text-muted">Initials: <strong>{{ $log->initials }}</strong></small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
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
                        <span class="text-muted">No logs</span>
                    @endif
                </td>
                <td>{{ $report->created_at->format('j M Y H:i') }}</td>
            <td>
                <div class="d-flex align-items-center">
                    <a class="text-decoration-none text-dark me-2" href="{{ route('reports.show', ['type' => $type, 'id' => $report->id]) }}" data-bs-toggle="tooltip" title="View Details">
                        <button class="view_btn"></button>
                    </a>
                    <a class="text-decoration-none text-dark me-2" href="{{ route('reports.edit', ['type' => $type, 'id' => $report->id]) }}" data-bs-toggle="tooltip" title="Edit">
                        <button class="editBtn">
                            <svg height="1em" viewBox="0 0 512 512">
                                <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                            </svg>
                        </button>
                    </a>
                    <a class="text-decoration-none text-dark" href="{{ route('reports.download', ['type' => $type, 'id' => $report->id]) }}" data-bs-toggle="tooltip" title="Download PDF">
                        <button class="btn btn-sm btn-danger px-2 d-flex align-items-center justify-content-center" style="height: 1.875rem; width: 1.875rem; border-radius: 6px;">
                            <i class="mdi mdi-file-pdf-box" style="font-size: 16px; color: white;"></i>
                        </button>
                    </a>
                </div>
            </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="text-center">No reports found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3">
    {{ $data['reports']->links() }}
</div>
