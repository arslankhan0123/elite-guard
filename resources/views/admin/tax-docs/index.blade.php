@extends('dashboardLayouts.main')
@section('title', 'Tax Documents')

@section('breadcrumbTitle', 'Tax Document Management')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Tax Documents</li>
@endsection

@section('content')

<style>
    .taxdoc-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .taxdoc-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1) !important;
    }

    .type-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.4em 0.9em;
        border-radius: 50px;
        letter-spacing: 0.04em;
    }

    .doc-icon-wrap {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .accent-top {
        height: 5px;
    }
</style>

<div class="row g-4">
    {{-- Header --}}
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1">Tax Documents</h4>
                <p class="text-muted mb-0">
                    {{ $taxDocs->count() }} of {{ count(\App\Models\TaxDocument::TYPES) }} types uploaded.
                    @if(!empty($availableTypes))
                    <span class="text-primary fw-semibold">{{ count($availableTypes) }} slot(s) remaining.</span>
                    @endif
                </p>
            </div>
            @if(!empty($availableTypes))
            <a href="{{ route('tax-docs.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                <i data-feather="plus" class="me-1" style="width:16px;"></i> Upload Tax Document
            </a>
            @else
            <button class="btn btn-secondary rounded-pill px-4 fw-bold" disabled>
                <i data-feather="check-circle" class="me-1" style="width:16px;"></i> All Types Uploaded
            </button>
            @endif
        </div>
    </div>

    {{-- Cards --}}
    @if($taxDocs->isEmpty())
    <div class="col-12">
        <div class="text-center py-5">
            <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                style="width:80px; height:80px; background:linear-gradient(135deg,#ede9fe,#ddd6fe);">
                <i data-feather="file-text" style="width:36px; height:36px; color:#7c3aed;"></i>
            </div>
            <h5 class="fw-bold">No Tax Documents Yet</h5>
            <p class="text-muted mb-4">Upload the required tax forms (Td1-Fill, Td1-Ab, Td2-Ab) to get started.</p>
            <a href="{{ route('tax-docs.create') }}" class="btn btn-primary rounded-pill px-5 fw-bold">
                <i data-feather="upload" class="me-1" style="width:15px;"></i> Upload First Document
            </a>
        </div>
    </div>
    @else
    @php
    $typeColors = [
    'Td1-Fill' => ['#7c3aed', '#ede9fe', 'text-purple'],
    'Td1-Ab' => ['#0891b2', '#e0f2fe', 'text-info'],
    'Td2-Ab' => ['#059669', '#d1fae5', 'text-success'],
    ];
    @endphp

    @foreach($taxDocs as $doc)
    @php
    $ext = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
    [$accentColor, $bgLight, $textClass] = $typeColors[$doc->type] ?? ['#6b7280', '#f3f4f6', 'text-secondary'];
    @endphp
    <div class="col-sm-6 col-lg-4">
        <div class="taxdoc-card card h-100 shadow-sm">
            <div class="accent-top" style="background: {{ $accentColor }};"></div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="doc-icon-wrap" style="background:{{ $bgLight }};">
                        <i data-feather="file-text" style="width:24px; height:24px; color:{{ $accentColor }};"></i>
                    </div>
                    <div>
                        <span class="type-badge text-white" style="background:{{ $accentColor }};">
                            {{ $doc->type }}
                        </span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold">File Type</span>
                    <span class="badge bg-light text-dark fw-bold border" style="font-size:0.7rem;">{{ strtoupper($ext) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="text-muted small fw-semibold">Uploaded On</span>
                    <span class="small fw-semibold text-dark">{{ $doc->created_at->format('d M, Y') }}</span>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ $doc->file_path }}" target="_blank" rel="noopener noreferrer"
                        class="btn btn-sm rounded-pill px-3 fw-semibold flex-fill"
                        style="background:{{ $bgLight }}; color:{{ $accentColor }}; border:none;">
                        <i data-feather="download" style="width:13px;" class="me-1"></i> Download
                    </a>
                    <div class="btn-group" role="group" aria-label="Tax Document Actions">
                        <a class="text-decoration-none me-2 text-dark ml-1"
                            href="{{ route('tax-docs.edit', $doc->id) }}" data-bs-toggle="tooltip"
                            title="Edit Tax Document">
                            <button class="editBtn">
                                <svg height="1em" viewBox="0 0 512 512">
                                    <path
                                        d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z">
                                    </path>
                                </svg>
                            </button>
                        </a>
                        <a href="{{ route('tax-docs.delete', $doc->id) }}" class="bin-button ml-1"
                            data-bs-toggle="tooltip" title="Delete Tax Document"
                            onclick="return confirm('Are you sure you want to delete this tax document?')">
                            <svg class="bin-top" viewBox="0 0 39 7" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <line y1="5" x2="39" y2="5" stroke="white" stroke-width="4"></line>
                                <line x1="12" y1="1.5" x2="26.0357" y2="1.5" stroke="white"
                                    stroke-width="3"></line>
                            </svg>
                            <svg class="bin-bottom" viewBox="0 0 33 39" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <mask id="path-1-inside-1_8_19" fill="white">
                                    <path
                                        d="M0 0H33V35C33 37.2091 31.2091 39 29 39H4C1.79086 39 0 37.2091 0 35V0Z">
                                    </path>
                                </mask>
                                <path
                                    d="M0 0H33H0ZM37 35C37 39.4183 33.4183 43 29 43H4C-0.418278 43 -4 39.4183 -4 35H4H29H37ZM4 43C-0.418278 43 -4 39.4183 -4 35V0H4V35V43ZM37 0V35C37 39.4183 33.4183 43 29 43V35V0H37Z"
                                    fill="white" mask="url(#path-1-inside-1_8_19)"></path>
                                <path d="M12 6L12 29" stroke="white" stroke-width="4"></path>
                                <path d="M21 6V29" stroke="white" stroke-width="4"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Placeholder cards for missing types --}}
    @foreach($availableTypes as $type)
    @php [$accentColor, $bgLight] = $typeColors[$type] ?? ['#6b7280', '#f3f4f6']; @endphp
    <div class="col-sm-6 col-lg-4">
        <div class="card h-100 border-dashed rounded-4 d-flex align-items-center justify-content-center text-center p-4"
            style="border: 2px dashed {{ $accentColor }}20; background: {{ $bgLight }}40; min-height: 220px;">
            <div class="doc-icon-wrap mx-auto mb-3" style="background:{{ $bgLight }}; opacity:0.7;">
                <i data-feather="upload-cloud" style="width:24px; height:24px; color:{{ $accentColor }};"></i>
            </div>
            <p class="fw-bold mb-1" style="color:{{ $accentColor }};">{{ $type }}</p>
            <p class="text-muted small mb-3">Not yet uploaded</p>
            <a href="{{ route('tax-docs.create') }}" class="btn btn-sm rounded-pill px-3 fw-semibold"
                style="background:{{ $bgLight }}; color:{{ $accentColor }}; border: 1px solid {{ $accentColor }}40;">
                <i data-feather="plus" style="width:12px;" class="me-1"></i> Upload
            </a>
        </div>
    </div>
    @endforeach
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endsection