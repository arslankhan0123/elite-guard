@extends('dashboardLayouts.main')
@section('title', $runSheet->exists ? 'Edit Weekly Runsheet' : 'Create Weekly Runsheet')
@section('breadcrumbTitle', $runSheet->exists ? 'Edit Weekly Runsheet' : 'Create Weekly Runsheet')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('weekly-run-sheets.index') }}">Runsheets</a></li>
<li class="breadcrumb-item active">{{ $runSheet->exists ? 'Edit' : 'Create' }}</li>
@endsection

@section('content')
@php
    $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
    $savedEntries = old('entries', $runSheet->entries?->map(fn ($entry) => [
        'day_of_week' => $entry->day_of_week,
        'site_id' => $entry->site_id,
        'tour_name' => $entry->tour_name,
        'start_time' => substr($entry->start_time, 0, 5),
        'end_time' => substr($entry->end_time, 0, 5),
    ])->values()->all() ?? []);
@endphp

<form method="POST" action="{{ $runSheet->exists ? route('weekly-run-sheets.update', $runSheet) : route('weekly-run-sheets.store') }}" id="runsheet-form">
    @csrf
    @if($runSheet->exists) @method('PUT') @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h4 class="card-title mb-1">{{ $runSheet->exists ? 'Update Weekly Runsheet' : 'New Weekly Runsheet' }}</h4>
                <p class="text-muted mb-0">Add as many site tours as required under each day.</p>
            </div>
            <a href="{{ route('weekly-run-sheets.index') }}" class="btn btn-light">Back</a>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Please correct the following:</strong>
                    <ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Runsheet Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $runSheet->name) }}" class="form-control" placeholder="e.g. Mobile Patrol Weekly Runsheet" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Week Starting <span class="text-danger">*</span></label>
                    <input type="date" name="week_start_date" value="{{ old('week_start_date', optional($runSheet->week_start_date)->format('Y-m-d')) }}" class="form-control" required>
                    <small class="text-muted">Saved as Monday of the selected week.</small>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Notes</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="Optional weekly instructions">{{ old('notes', $runSheet->notes) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3" id="day-board">
        @foreach($days as $dayNumber => $dayName)
        <div class="col-12">
            <div class="card border-0 shadow-sm day-card" data-day="{{ $dayNumber }}">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                    <div>
                        <h5 class="mb-0 text-dark">{{ $dayName }}</h5>
                        <small class="text-muted day-count">No tours</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary add-entry" data-day="{{ $dayNumber }}">
                        <i data-feather="plus" style="width:14px"></i> Add Tour
                    </button>
                </div>
                <div class="card-body entries-container py-2" data-day="{{ $dayNumber }}">
                    <div class="empty-day text-center text-muted py-3">No tours scheduled for {{ $dayName }}.</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="sticky-save mt-4 py-3 d-flex justify-content-end gap-2">
        <a href="{{ route('weekly-run-sheets.index') }}" class="btn btn-light">Cancel</a>
        <button type="submit" class="btn btn-primary px-4"><i data-feather="save" class="me-1" style="width:16px"></i> Save Weekly Runsheet</button>
    </div>
</form>

<style>
    .day-card { border-left: 4px solid #6f42c1 !important; }
    .tour-entry { background: #f8f7ff; border: 1px solid #e7e1ff; border-radius: 10px; padding: 12px; margin: 8px 0; }
    .tour-number { width: 28px; height: 28px; border-radius: 50%; background: #6f42c1; color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; }
    .remove-entry { width: 34px; height: 34px; padding: 0; }
    .sticky-save { position: sticky; bottom: 0; z-index: 5; background: rgba(245,247,251,.94); border-top: 1px solid #e5e7eb; }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sites = @json($sites->map(fn ($site) => ['id' => $site->id, 'name' => $site->name])->values());
    const savedEntries = Object.values(@json($savedEntries) || {});
    let nextIndex = 0;

    const escapeHtml = value => String(value ?? '').replace(/[&<>'"]/g, character => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
    })[character]);

    function siteOptions(selectedId) {
        return '<option value="">Select site</option>' + sites.map(site =>
            `<option value="${site.id}" ${String(site.id) === String(selectedId ?? '') ? 'selected' : ''}>${escapeHtml(site.name)}</option>`
        ).join('');
    }

    function addEntry(day, data = {}) {
        const container = document.querySelector(`.entries-container[data-day="${day}"]`);
        const index = nextIndex++;
        const wrapper = document.createElement('div');
        wrapper.className = 'tour-entry';
        wrapper.innerHTML = `
            <input type="hidden" name="entries[${index}][day_of_week]" value="${day}">
            <div class="row g-2 align-items-end">
                <div class="col-auto align-self-center"><span class="tour-number"></span></div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Tour Name</label>
                    <input type="text" name="entries[${index}][tour_name]" value="${escapeHtml(data.tour_name || '')}" class="form-control" placeholder="e.g. Perimeter Patrol" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Site</label>
                    <select name="entries[${index}][site_id]" class="form-select" required>${siteOptions(data.site_id)}</select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">Start Time</label>
                    <input type="time" name="entries[${index}][start_time]" value="${escapeHtml((data.start_time || '').substring(0, 5))}" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">End Time</label>
                    <input type="time" name="entries[${index}][end_time]" value="${escapeHtml((data.end_time || '').substring(0, 5))}" class="form-control" required>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-danger remove-entry" title="Remove tour"><i class="fas fa-trash"></i></button>
                </div>
            </div>`;

        container.appendChild(wrapper);
        wrapper.querySelector('.remove-entry').addEventListener('click', () => {
            wrapper.remove();
            refreshDay(day);
        });
        refreshDay(day);
    }

    function refreshDay(day) {
        const container = document.querySelector(`.entries-container[data-day="${day}"]`);
        const entries = container.querySelectorAll('.tour-entry');
        container.querySelector('.empty-day').style.display = entries.length ? 'none' : 'block';
        entries.forEach((entry, position) => entry.querySelector('.tour-number').textContent = position + 1);
        const count = container.closest('.day-card').querySelector('.day-count');
        count.textContent = entries.length ? `${entries.length} tour${entries.length === 1 ? '' : 's'}` : 'No tours';
    }

    document.querySelectorAll('.add-entry').forEach(button => {
        button.addEventListener('click', () => addEntry(Number(button.dataset.day)));
    });

    savedEntries.forEach(entry => addEntry(Number(entry.day_of_week), entry));
});
</script>
@endsection
