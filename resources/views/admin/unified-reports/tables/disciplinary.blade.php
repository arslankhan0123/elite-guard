<table id="custom-table" class="table table-striped table-bordered">
    <thead>
        <tr class="table-dark">
            <th>ID</th>
            <th>Guard Name</th>
            <th>License #</th>
            <th>Site/Property</th>
            <th>Warning Date</th>
            <th>Violation Type</th>
            <th>Severity</th>
            <th>Summary</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['reports'] as $report)
        <tr>
            <td>{{ $report->id }}</td>
            <td>
                <strong>{{ $report->employee_name }}</strong><br>
                <small class="text-muted">ID: {{ $report->employee_id_license }}</small>
            </td>
            <td>{{ $report->employee_id_license }}</td>
            <td>{{ $report->site_property }}</td>
            <td>{{ \Carbon\Carbon::parse($report->warning_date)->format('j M Y') }}</td>
            <td>{{ $report->violation_type }}</td>
            <td>
                <span class="badge @if($report->classification_severity == 'Major') bg-danger @else bg-warning @endif">
                    {{ $report->classification_severity }}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#summaryModal{{ $report->id }}">
                    View Summary
                </button>

                <!-- Modal -->
                <div class="modal fade" id="summaryModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title text-white">Incident Summary - Report #{{ $report->id }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>{{ $report->incident_summary ?? 'No summary available.' }}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
            <td>{{ $report->created_at->format('j M Y H:i') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="text-center">No reports found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3">
    {{ $data['reports']->links() }}
</div>
