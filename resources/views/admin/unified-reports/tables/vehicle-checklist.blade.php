<table id="custom-table" class="table table-striped table-bordered">
    <thead>
        <tr class="table-dark">
            <th>ID</th>
            <th>User</th>
            <th>Date</th>
            <th>Time</th>
            <th>Vehicle</th>
            <th>Odometer</th>
            <th>Fuel</th>
            <th>Site</th>
            <th>Driver</th>
            <th>Document</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['checklists'] as $checklist)
            <tr>
                <td>{{ $checklist->id }}</td>
                <td>
                    <strong>{{ $checklist->user->name ?? 'N/A' }}</strong><br>
                    <small class="text-muted">{{ $checklist->user->email ?? '' }}</small>
                </td>
                <td>{{ $checklist->date }}</td>
                <td>{{ $checklist->time }}</td>
                <td>{{ $checklist->vehicle }}</td>
                <td>{{ $checklist->odometer_reading }}</td>
                <td>{{ $checklist->fuel }}</td>
                <td>{{ $checklist->assigned_site }}</td>
                <td>{{ $checklist->driver }}</td>
                <td>
                    @if($checklist->documents)
                        <a href="{{ $checklist->documents }}" target="_blank" class="btn btn-sm btn-primary">View File</a>
                    @else
                        <span class="text-muted">No File</span>
                    @endif
                </td>
                <td>{{ $checklist->created_at->format('j M Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="text-center">No checklists found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3">
    {{ $data['checklists']->links() }}
</div>
