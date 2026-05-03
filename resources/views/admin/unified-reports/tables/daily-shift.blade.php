<table id="custom-table" class="table table-striped table-bordered">
    <thead>
        <tr class="table-dark">
            <th>ID</th>
            <th>Security Guard</th>
            <th>Client</th>
            <th>Location</th>
            <th>Shift Date</th>
            <th>Shift Time</th>
            <th>Patrol Entries</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['reports'] as $report)
        <tr>
            <td>{{ $report->id }}</td>
            <td>{{ $report->security_guard }}</td>
            <td>{{ $report->client }}</td>
            <td>{{ $report->location }}</td>
            <td>{{ \Carbon\Carbon::parse($report->date)->format('j M Y') }}</td>
            <td>{{ $report->shift_time }}</td>
            <td>
                @if($report->patrolEntries->count() > 0)
                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#patrolModal{{ $report->id }}">
                        View {{ $report->patrolEntries->count() }} Entries
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="patrolModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title text-white">Patrol Entries - Report #{{ $report->id }}</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body bg-light">
                                    <div class="row g-3">
                                        @foreach($report->patrolEntries as $entry)
                                            <div class="col-md-4">
                                                <div class="card h-100 border-0 shadow-sm mb-0">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="avatar-xs me-2">
                                                                <span class="avatar-title rounded-circle bg-primary-subtle text-primary font-size-11">
                                                                    <i class="mdi mdi-clock-outline"></i>
                                                                </span>
                                                            </div>
                                                            <h6 class="mb-0 text-primary">{{ $entry->time_range }}</h6>
                                                        </div>
                                                        <p class="text-muted mb-0 font-size-13">
                                                            {{ $entry->summary }}
                                                        </p>
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
                    <span class="text-muted">No entries</span>
                @endif
            </td>
            <td>{{ $report->created_at->format('j M Y H:i') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center">No reports found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3">
    {{ $data['reports']->links() }}
</div>
