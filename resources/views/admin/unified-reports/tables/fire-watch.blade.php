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
            <td><a class="text-decoration-none text-dark" href="{{ route('reports.show', ['type' => $type, 'id' => $report->id]) }}" data-bs-toggle="tooltip" title="View Details"><button class="view_btn me-2"></button></a></td>
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
