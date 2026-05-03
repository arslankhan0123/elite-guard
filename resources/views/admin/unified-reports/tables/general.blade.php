<table id="custom-table" class="table table-striped table-bordered">
    <thead>
        <tr class="table-dark">
            <th>ID</th>
            <th>Report Type</th>
            <th>Property</th>
            <th>Location</th>
            <th>Report Date/Time</th>
            <th>Reported By</th>
            <th>Observations</th>
            <th>Images</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['reports'] as $report)
        <tr>
            <td>{{ $report->id }}</td>
            <td>{{ $report->report_type }}</td>
            <td>{{ $report->property_name }}</td>
            <td>{{ $report->property_location }}</td>
            <td>{{ \Carbon\Carbon::parse($report->report_date)->format('j M Y') }} {{ $report->report_time }}</td>
            <td>{{ $report->user->name ?? $report->reported_by }}</td>
            <td>{{ Str::limit($report->observation_situation, 50) }}</td>
            <td>
                @if($report->images->count() > 0)
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($report->images as $image)
                            @if($image->observation_image_path)
                                <a href="{{ $image->observation_image_path }}" target="_blank" title="Observation">
                                    <img src="{{ $image->observation_image_path }}" alt="Obs" style="width: 30px; height: 30px; object-fit: cover;" class="rounded border">
                                </a>
                            @endif
                            @if($image->cleared_area_image_path)
                                <a href="{{ $image->cleared_area_image_path }}" target="_blank" title="Cleared Area">
                                    <img src="{{ $image->cleared_area_image_path }}" alt="Clr" style="width: 30px; height: 30px; object-fit: cover;" class="rounded border">
                                </a>
                            @endif
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
            <td colspan="9" class="text-center">No reports found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3">
    {{ $data['reports']->links() }}
</div>
