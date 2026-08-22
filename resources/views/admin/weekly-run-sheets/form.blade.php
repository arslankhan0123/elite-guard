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
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Route Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $runSheet->name) }}" class="form-control" placeholder="e.g. Mobile Patrol Route" required>
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
                <div class="card-header bg-white d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
                    <div class="d-flex align-items-center gap-3">
                        <h5 class="mb-0 text-dark fw-bold">{{ $dayName }}</h5>
                        <small class="text-muted day-count">No tours</small>
                    </div>
                    <div class="d-flex align-items-center gap-2 ms-auto flex-wrap">
                        @php
                            $dayKey = strtolower($dayName);
                            $startCol = "{$dayKey}_start_time";
                            $endCol = "{$dayKey}_end_time";
                            $startTimeVal = old($startCol, $runSheet->$startCol ? substr($runSheet->$startCol, 0, 5) : '08:00');
                            $endTimeVal = old($endCol, $runSheet->$endCol ? substr($runSheet->$endCol, 0, 5) : '16:00');
                        @endphp
                        <div class="d-flex align-items-center gap-1">
                            <label class="form-label small fw-semibold mb-0 text-muted me-1">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="{{ $startCol }}" value="{{ $startTimeVal }}" class="form-control form-control-sm" style="width: 130px;" required>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <label class="form-label small fw-semibold mb-0 text-muted me-1">End Time <span class="text-danger">*</span></label>
                            <input type="time" name="{{ $endCol }}" value="{{ $endTimeVal }}" class="form-control form-control-sm" style="width: 130px;" required>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary add-entry ms-2" data-day="{{ $dayNumber }}">
                            <i data-feather="plus" style="width:14px"></i> Add Tour
                        </button>
                    </div>
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
    .remove-entry { flex-shrink: 0; }
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
                    <label class="form-label small fw-semibold mb-1">Start Time <small class="text-muted fw-normal">(Optional)</small></label>
                    <input type="time" name="entries[${index}][start_time]" value="${escapeHtml((data.start_time || '').substring(0, 5))}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">End Time <small class="text-muted fw-normal">(Optional)</small></label>
                    <input type="time" name="entries[${index}][end_time]" value="${escapeHtml((data.end_time || '').substring(0, 5))}" class="form-control">
                </div>
                <div class="col-auto">
                    <button type="button" class="bin-button remove-entry" title="Remove tour" aria-label="Remove tour">
                        <svg class="bin-top" viewBox="0 0 39 7" fill="none" xmlns="http://www.w3.org/2000/svg"><line y1="5" x2="39" y2="5" stroke="white" stroke-width="4"></line><line x1="12" y1="1.5" x2="26.0357" y2="1.5" stroke="white" stroke-width="3"></line></svg>
                        <svg class="bin-bottom" viewBox="0 0 33 39" fill="none" xmlns="http://www.w3.org/2000/svg"><mask id="entry-bin-${index}" fill="white"><path d="M0 0H33V35C33 37.2091 31.2091 39 29 39H4C1.79086 39 0 37.2091 0 35V0Z"></path></mask><path d="M0 0H33H0ZM37 35C37 39.4183 33.4183 43 29 43H4C-.418 43-4 39.418-4 35H4H29H37ZM4 43C-.418 43-4 39.418-4 35V0H4V35V43ZM37 0V35C37 39.418 33.418 43 29 43V35V0H37Z" fill="white" mask="url(#entry-bin-${index})"></path><path d="M12 6V29M21 6V29" stroke="white" stroke-width="4"></path></svg>
                    </button>
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
