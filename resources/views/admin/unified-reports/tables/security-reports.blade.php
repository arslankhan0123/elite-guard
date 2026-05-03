<table class="table table-striped table-bordered">
    <thead>
        <tr class="table-dark">
            <th>ID</th>
            @if($type == 'general')
                <th>Report Type</th>
                <th>Property</th>
                <th>Location</th>
            @elseif($type == 'disciplinary')
                <th>Employee Name</th>
                <th>Property Name</th>
            @elseif($type == 'incident')
                <th>Incident Type</th>
                <th>Property Name</th>
            @elseif($type == 'daily-shift')
                <th>Shift Type</th>
                <th>Patrol Status</th>
            @endif
            <th>Reported By</th>
            <th>Date/Time</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['reports'] as $report)
            <tr>
                <td>{{ $report->id }}</td>
                @if($type == 'general')
                    <td>{{ $report->report_type }}</td>
                    <td>{{ $report->property_name }}</td>
                    <td>{{ $report->property_location }}</td>
                @elseif($type == 'disciplinary')
                    <td>{{ $report->employee_name }}</td>
                    <td>{{ $report->property_name }}</td>
                @elseif($type == 'incident')
                    <td>{{ $report->incident_type }}</td>
                    <td>{{ $report->property_name }}</td>
                @elseif($type == 'daily-shift')
                    <td>{{ $report->report_type }}</td>
                    <td>{{ $report->patrolEntries->count() }} Entries</td>
                @endif
                <td>{{ $report->user->name ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($report->report_date ?? $report->created_at)->format('j M Y') }}</td>
                <td>{{ $report->created_at->format('j M Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center">No records found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3">
    {{ $data['reports']->links() }}
</div>
