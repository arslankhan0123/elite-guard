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
        <th>Action</th>
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
            <td colspan="10" class="text-center">No reports found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3">
    {{ $data['reports']->links() }}
</div>
