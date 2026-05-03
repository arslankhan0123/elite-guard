<table class="table table-striped table-bordered">
    <thead>
        <tr class="table-dark">
            <th>ID</th>
            <th>Employee</th>
            <th>Type</th>
            <th>Score</th>
            <th>Date</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['assessments'] as $assessment)
            <tr>
                <td>{{ $assessment->id }}</td>
                <td>{{ $assessment->user->name ?? 'N/A' }}</td>
                <td>{{ $assessment->assessment_type ?? 'N/A' }}</td>
                <td>{{ $assessment->score ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($assessment->created_at)->format('j M Y') }}</td>
                <td>{{ $assessment->created_at->format('j M Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">No assessments found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3">
    {{ $data['assessments']->links() }}
</div>
