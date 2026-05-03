<table id="custom-table" class="table table-striped table-bordered">
    <thead>
        <tr class="table-dark">
            <th>ID</th>
            <th>Incident Type</th>
            <th>Location</th>
            <th>Property</th>
            <th>Report Date/Time</th>
            <th>Reported By</th>
            <th>Images</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['reports'] as $report)
        <tr>
            <td>{{ $report->id }}</td>
            <td>
                <span class="badge bg-danger">{{ $report->incident_type }}</span>
            </td>
            <td>{{ $report->location }}</td>
            <td>{{ $report->property }}</td>
            <td>{{ \Carbon\Carbon::parse($report->date_of_report)->format('j M Y') }} {{ $report->time_of_report }}</td>
            <td>{{ $report->user->name ?? $report->reported_by }}</td>
            <td>
                @if($report->images->count() > 0)
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($report->images as $image)
                            <a href="{{ $image->image_path }}" target="_blank">
                                <img src="{{ $image->image_path }}" alt="Incident" style="width: 40px; height: 40px; object-fit: cover;" class="rounded border">
                            </a>
                        @endforeach
                    </div>
                @else
                    <span class="text-muted">No images</span>
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
