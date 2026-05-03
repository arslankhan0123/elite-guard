<table id="custom-table" class="table table-striped table-bordered">
    <thead>
        <tr class="table-dark">
            <th>ID</th>
            <th>User</th>
            <th>Client</th>
            <th>Supervisor</th>
            <th>Position</th>
            <th>Shift Date</th>
            <th>Fit for Duty?</th>
            <th>Believe Fit?</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['assessments'] as $assessment)
            <tr>
                <td>{{ $assessment->id }}</td>
                <td>
                    <strong>{{ $assessment->user->name ?? 'N/A' }}</strong><br>
                    <small class="text-muted">{{ $assessment->user->email ?? '' }}</small>
                </td>
                <td>{{ $assessment->client }}</td>
                <td>{{ $assessment->supervisor_first_name }} {{ $assessment->supervisor_last_name }}</td>
                <td>{{ $assessment->position_today }}</td>
                <td>{{ $assessment->shift_date }}</td>
                <td>
                    @if($assessment->compliance_fit_for_duty)
                        <span class="badge bg-success">Yes</span>
                    @else
                        <span class="badge bg-danger">No</span>
                    @endif
                </td>
                <td>
                    @if($assessment->believe_fit_for_duty)
                        <span class="badge bg-success">Yes</span>
                    @else
                        <span class="badge bg-danger">No</span>
                    @endif
                </td>
                <td>{{ $assessment->created_at->format('j M Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center">No assessments found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3">
    {{ $data['assessments']->links() }}
</div>
