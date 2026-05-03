<table class="table table-striped table-bordered">
    <thead>
        <tr class="table-dark">
            <th>ID</th>
            <th>Employee</th>
            <th>Vehicle ID</th>
            <th>Odometer</th>
            <th>Documents</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['checklists'] as $checklist)
            <tr>
                <td>{{ $checklist->id }}</td>
                <td>{{ $checklist->user->name ?? 'N/A' }}</td>
                <td>{{ $checklist->vehicle_id ?? 'N/A' }}</td>
                <td>{{ $checklist->odometer_reading ?? 'N/A' }}</td>
                <td>
                    @if($checklist->documents)
                        <span class="badge bg-success">Uploaded</span>
                    @else
                        <span class="badge bg-danger">Missing</span>
                    @endif
                </td>
                <td>{{ $checklist->created_at->format('j M Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">No checklists found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3">
    {{ $data['checklists']->links() }}
</div>
