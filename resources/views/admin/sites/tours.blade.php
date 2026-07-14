@extends('dashboardLayouts.main')
@section('title', 'Site Tours')

@section('breadcrumbTitle', 'Site Tours')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{route('sites.index')}}">Sites</a></li>
<li class="breadcrumb-item active">Tours</li>
@endsection

@section('content')
<style>
    .nfc-card-header {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        padding: 1.4rem 1.75rem;
        border-radius: 0.75rem 0.75rem 0 0;
    }

    .nfc-card-header .header-icon-wrap {
        width: 42px;
        height: 42px;
        background: rgba(255, 255, 255, 0.18);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .nfc-table thead tr th {
        background: #f8f7ff;
        color: #5b21b6;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border-bottom: 2px solid #ede9fe;
        padding: 0.85rem 1rem;
        white-space: nowrap;
    }

    .nfc-table tbody tr td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .nfc-table tbody tr:hover {
        background: #faf8ff;
    }

    .uid-chip {
        font-family: 'Courier New', monospace;
        font-size: 0.78rem;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 4px 10px;
        cursor: pointer;
        color: #7c3aed;
        font-weight: 700;
        transition: background 0.2s;
    }

    .uid-chip:hover {
        background: #ede9fe;
    }

    /* Form Overrides matching modern UI */
    .form-floating > .form-control,
    .form-floating > .form-select,
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 3.5rem;
        border-radius: 0.375rem;
        border-color: #dee2e6;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
        background-color: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 0.85rem;
        margin-top: 0.3rem;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove {
        color: #64748b;
        margin-right: 5px;
        background: transparent;
        border: none;
        font-weight: bold;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove:hover {
        color: #ef4444;
    }

    .text-danger-custom {
        color: #dc3545;
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }

    .nfc-modal-header {
        background: #fff;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .modal-body {
        background-color: #f8fafc;
    }

    /* Card like fields container */
    .field-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1.25rem;
        margin-bottom: 1rem;
    }

    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }

    .form-control:focus, .form-select:focus, .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: #7c3aed;
        box-shadow: 0 0 0 0.2rem rgba(124, 58, 237, 0.15);
    }
    
    /* Error state */
    .has-error .form-control,
    .has-error .form-select,
    .has-error .select2-selection {
        border-color: #ef4444 !important;
    }
    .has-error label {
        color: #ef4444 !important;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm" style="border-radius: 0.75rem; overflow: hidden;">
            <div class="nfc-card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon-wrap">
                        <i data-feather="map" style="width:20px;height:20px;color:#fff;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-white" style="font-size:1.05rem;">{{ $site->name }} &mdash; Tours</h5>
                        <span class="text-white d-block" style="opacity:0.75;font-size:0.8rem;">Manage site tour configurations</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center me-3 bg-white rounded-pill px-2 py-1 shadow-sm" style="border: 1px solid #dee2e6;">
                        <a href="?week={{ $prevWeek }}" class="text-decoration-none text-muted px-2"><i data-feather="chevron-left" style="width:16px;"></i></a>
                        <span class="fw-bold px-2 text-primary" style="font-size: 0.85rem;"><i data-feather="calendar" class="me-1" style="width:13px;"></i> {{ $weekStartFormatted }} - {{ $weekEndFormatted }}</span>
                        <a href="?week={{ $nextWeek }}" class="text-decoration-none text-muted px-2"><i data-feather="chevron-right" style="width:16px;"></i></a>
                    </div>
                    @php
                        $isPastWeek = $weekStart->lte(\Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY));
                    @endphp
                    @if($site->siteTours->count() > 0)
                        @php
                            $masterTour = $site->siteTours->first();
                            $masterUsers = $site->siteTours->pluck('user_id')->filter()->toArray();
                            $masterDays = is_string($masterTour->scheduled_days) ? json_decode($masterTour->scheduled_days, true) : ($masterTour->scheduled_days ?? []);
                            $masterTourData = [
                                'name' => $masterTour->name,
                                'interval' => $masterTour->interval,
                                'open_time' => $masterTour->open_time,
                                'grace_time' => $masterTour->grace_time,
                                'tag_type' => $masterTour->tag_type,
                                'scheduled_days' => $masterDays,
                                'users' => array_values(array_unique($masterUsers))
                            ];
                        @endphp
                        <button type="button" class="btn btn-warning fw-semibold px-4 text-dark"
                            style="border-radius:8px; font-size:0.85rem;"
                            data-tour-config="{{ json_encode($masterTourData) }}"
                            id="btnUpdateWeekTour"
                            {{ $isPastWeek ? 'disabled' : '' }}>
                            <i data-feather="edit" style="width:15px;height:15px;" class="me-1"></i> Update Tour
                        </button>
                        <form action="{{ route('sites.tours.deleteWeek', ['site_id' => $site->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to completely delete all tours assigned in this week? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="week_start_date" value="{{ $weekStart->format('Y-m-d') }}">
                            <button type="submit" class="btn btn-danger fw-semibold px-3" style="border-radius:8px; font-size:0.85rem;" title="Delete All Tours for Week" {{ $isPastWeek ? 'disabled' : '' }}>
                                <i data-feather="trash-2" style="width:15px;height:15px;" class="me-0"></i>
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-light fw-semibold px-4"
                            style="border-radius:8px; font-size:0.85rem;"
                            data-bs-toggle="modal" data-bs-target="#tourModal" id="btnCreateTour"
                            {{ $isPastWeek ? 'disabled' : '' }}>
                            <i data-feather="plus-circle" style="width:15px;height:15px;" class="me-1"></i> New Tour
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="custom-table" class="table nfc-table mb-0">
                        <thead>
                            <tr>
                                <th>Tour Name</th>
                                <th>User Name</th>
                                <th>Tag Type</th>
                                <th>Days</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tours-tbody">
                            @foreach($site->siteTours as $tour)
                            <tr id="tour-row-{{ $tour->id }}">
                                <td class="fw-bold">
                                    {{ $tour->name }}
                                    @if($tour->user)
                                        <div class="small text-muted mt-1" style="font-weight: normal; font-size: 0.8rem;">
                                            <i data-feather="user" style="width:12px;height:12px;"></i> {{ $tour->user->name }}
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-bold">
                                    @if($tour->user)
                                        <i data-feather="user" style="width:12px;height:12px;"></i> {{ $tour->user->name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $tagType = strtolower($tour->tag_type);
                                        $badgeClass = 'bg-primary';
                                        if ($tagType === 'image') $badgeClass = 'bg-info text-dark';
                                        elseif ($tagType === 'both' || str_contains($tagType, '+')) $badgeClass = 'bg-warning text-dark';
                                    @endphp
                                    <span class="badge {{ $badgeClass }} text-uppercase px-2 py-1">{{ $tour->tag_type }}</span>
                                </td>
                                <td>
                                    @php
                                        $days = $tour->scheduled_days ?? [];
                                    @endphp
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($days as $day)
                                            <span class="badge bg-secondary px-2 py-1">{{ substr($day, 0, 3) }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group" aria-label="Site Actions">
                                        <a class="text-decoration-none text-dark view-tour-btn" href="javascript:void(0)" data-tour="{{ json_encode($tour) }}" data-bs-toggle="tooltip" title="View Site Tour Items">
                                            <button class="view_btn me-2">
                                            </button>
                                        </a>
                                        <a class="text-decoration-none me-2 text-dark ml-1 edit-tour-btn" href="javascript:void(0)" data-tour="{{ json_encode($tour) }}" data-bs-toggle="tooltip" title="Edit Tour" @if($isPastWeek) style="pointer-events: none; opacity: 0.5;" @endif>
                                            <button class="editBtn" style="pointer-events: none;">
                                                <svg height="1em" viewBox="0 0 512 512">
                                                    <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                                                </svg>
                                            </button>
                                        </a>
                                        <a href="{{ route('sites.tours.delete', $tour->id) }}" class="bin-button ml-1" data-bs-toggle="tooltip" title="Delete Tour" onclick="return confirm('Are you sure you want to delete this tour?')" @if($isPastWeek) style="pointer-events: none; opacity: 0.5;" @endif>
                                            <svg class="bin-top" viewBox="0 0 39 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <line y1="5" x2="39" y2="5" stroke="white" stroke-width="4"></line>
                                                <line x1="12" y1="1.5" x2="26.0357" y2="1.5" stroke="white" stroke-width="3"></line>
                                            </svg>
                                            <svg class="bin-bottom" viewBox="0 0 33 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <mask id="path-1-inside-1_8_{{ $tour->id }}" fill="white">
                                                    <path d="M0 0H33V35C33 37.2091 31.2091 39 29 39H4C1.79086 39 0 37.2091 0 35V0Z"></path>
                                                </mask>
                                                <path d="M0 0H33H0ZM37 35C37 39.4183 33.4183 43 29 43H4C-0.418278 43 -4 39.4183 -4 35H4H29H37ZM4 43C-0.418278 43 -4 39.4183 -4 35V0H4V35V43ZM37 0V35C37 39.4183 33.4183 43 29 43V35V0H37Z" fill="white" mask="url(#path-1-inside-1_8_{{ $tour->id }})"></path>
                                                <path d="M12 6L12 29" stroke="white" stroke-width="4"></path>
                                                <path d="M21 6V29" stroke="white" stroke-width="4"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="tourModal" tabindex="-1" aria-labelledby="tourModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius:0.75rem;">
            <div class="nfc-modal-header d-flex align-items-center justify-content-between">
                <h5 class="modal-title text-dark fw-bold" id="tourModalLabel">New Tour</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="tourForm">
                    @csrf
                    <input type="hidden" name="site_id" value="{{ $site->id }}">
                    <input type="hidden" name="tour_id" id="tour_id" value="">
                    <input type="hidden" name="week_start_date" id="week_start_date" value="{{ $weekStart->format('Y-m-d') }}">

                    <div class="field-card shadow-sm">
                        <!-- Tour Name -->
                        <div class="form-group mb-3" id="group-name">
                            <label for="name" class="form-label">Tour Name*</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Tour Name" required>
                            <div class="text-danger-custom d-none" id="error-name">Tour Name is required</div>
                        </div>
                    </div>

                    <div class="field-card shadow-sm">
                        <!-- Scheduled Days -->
                        <div class="form-group mb-4" id="group-scheduled_days">
                            <label for="scheduled_days" class="form-label">Scheduled Days*</label>
                            <select class="form-select select2-multiple" id="scheduled_days" name="scheduled_days[]" multiple="multiple" data-placeholder="Select Days" required>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                            </select>
                            <div class="text-danger-custom d-none" id="error-scheduled_days">Scheduled Days are required</div>
                        </div>


                        <div class="row mt-3">
                            <div class="col-md-4 form-group" id="group-interval">
                                <label for="interval" class="form-label">Interval</label>
                                <input type="number" class="form-control" id="interval" name="interval">
                            </div>
                            <div class="col-md-4 form-group" id="group-open_time">
                                <label for="open_time" class="form-label">Open Time</label>
                                <input type="number" class="form-control" id="open_time" name="open_time">
                            </div>
                            <div class="col-md-4 form-group" id="group-grace_time">
                                <label for="grace_time" class="form-label">Grace Time</label>
                                <input type="number" class="form-control" id="grace_time" name="grace_time">
                            </div>
                        </div>
                    </div>

                    <div class="field-card shadow-sm mb-0 mt-3">
                        <!-- Tag Type -->
                        <div class="form-group mb-0" id="group-tag_type">
                            <label class="form-label">Select Tag Type*</label>
                            <select class="form-select" id="tag_type" name="tag_type" required>
                                <option value="" disabled selected>Select...</option>
                                <option value="nfc">NFC</option>
                                <option value="image">Image</option>
                                <option value="both">NFC + Image</option>
                            </select>
                            <div class="text-danger-custom d-none" id="error-tag_type">Tag Type is required</div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer bg-light pb-4 pt-3 px-4 border-top">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-5 fw-bold" id="btnSaveTour" style="border-radius: 8px;">Save Tour</button>
            </div>
        </div>
    </div>
</div>

{{-- ITEMS MODAL --}}
<div class="modal fade" id="itemsModal" tabindex="-1" aria-labelledby="itemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius:0.75rem;">
            <div class="nfc-modal-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="modal-title text-dark fw-bold mb-0" id="itemsModalLabel">Site Tour Items</h5>
                    <select id="items-date-filter" class="form-select form-select-sm" style="width: auto;">
                    </select>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table nfc-table mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="items-tbody">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light pb-4 pt-3 px-4 border-top">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
    /* Hide the default chips for all multiple selects since we want custom summary text */
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        display: none !important;
    }
    .select2-container--bootstrap-5 .select2-search__field {
        width: 100% !important;
        cursor: pointer;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple {
        min-height: 3.5rem;
        display: flex;
        align-items: center;
        padding-left: 0.5rem;
    }
    
    /* CSS Checkboxes for Select2 Options */
    .select2-container--bootstrap-5 .select2-results__option {
        position: relative;
        padding: 0.4rem 1rem 0.4rem 2.5rem !important; /* Tighter vertical padding */
        margin: 0 !important;
        min-height: auto !important;
        color: #475569 !important;
        background-color: transparent !important;
        transition: background-color 0.2s;
        line-height: 1.5;
    }
    .select2-container--bootstrap-5 .select2-results__option:hover,
    .select2-container--bootstrap-5 .select2-results__option--highlighted {
        background-color: #f8fafc !important; /* Light gray on hover, not blue */
        color: #475569 !important;
    }
    /* The checkbox box */
    .select2-container--bootstrap-5 .select2-results__option::before {
        content: '';
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        width: 1.1rem;
        height: 1.1rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.2rem;
        background-color: #fff;
    }
    /* The checked state */
    .select2-container--bootstrap-5 .select2-results__option[aria-selected="true"]::before {
        background-color: #0d6efd;
        border-color: #0d6efd;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10l3 3l6-6'/%3e%3c/svg%3e");
        background-size: contain;
    }
    /* Remove default bootstrap-5 theme checkmark */
    .select2-container--bootstrap-5 .select2-results__option[aria-selected="true"]::after {
        display: none !important;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();

        // Single selects
        $('.select2-single, #schedule_type, #max_duration, #tag_type').each(function() {
            $(this).select2({
                theme: "bootstrap-5",
                width: '100%',
                dropdownParent: $(this).parent()
            });
        });

        // Multiple selects with CSS Checkboxes
        $('.select2-multiple, .select2-guards, .select2-tags').each(function() {
            var $el = $(this);
            var isTags = $el.hasClass('select2-tags');
            
            $el.select2({
                theme: "bootstrap-5",
                width: '100%',
                allowClear: true,
                closeOnSelect: false,
                tags: isTags,
                dropdownParent: $(this).parent(),
                tokenSeparators: isTags ? [',', ' '] : null,
                createTag: isTags ? function (params) {
                    var term = $.trim(params.term);
                    if (term === '') return null;
                    return { id: term, text: term };
                } : null
            }).on('select2:select select2:unselect', function (e) {
                var selectedData = $el.select2('data');
                var text = '';
                if(selectedData.length > 0) {
                    if(selectedData.length === 1) {
                        text = selectedData[0].text;
                    } else {
                        text = selectedData[0].text + ' (+' + (selectedData.length - 1) + ' others)';
                    }
                }
                $el.parent().find('.select2-search__field').attr('placeholder', text);
            }).on('select2:open', function() {
                var selectedData = $el.select2('data');
                var text = '';
                if(selectedData.length > 0) {
                    if(selectedData.length === 1) {
                        text = selectedData[0].text;
                    } else {
                        text = selectedData[0].text + ' (+' + (selectedData.length - 1) + ' others)';
                    }
                }
                $el.parent().find('.select2-search__field').attr('placeholder', text);
            });
        });

        // Fix for Select2 search input focus inside Bootstrap modal
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};

        function clearErrors() {
            $('.text-danger-custom').addClass('d-none');
            $('.has-error').removeClass('has-error');
            $('.form-control, .form-select').removeClass('is-invalid');
            $('.select2-guards').parent().find('.select2-search__field').attr('placeholder', 'Select Guard');
        }

        // Open Modal for Create
        $('#btnCreateTour').on('click', function() {
            $('#tourForm')[0].reset();
            $('#tour_id').val('');
            $('#tourModalLabel').text('New Tour');
            
            // Reset Select2 fields
            $('#scheduled_days').val(null).trigger('change');
            $('#users').val(null).trigger('change');
            $('#tag_type').val('').trigger('change');
            
            $('#interval').val('');
            $('#open_time').val('');
            $('#grace_time').val('');
            
            clearErrors();
        });

        $('#btnUpdateWeekTour').on('click', function() {
            var config = JSON.parse($(this).attr('data-tour-config'));
            $('#tourModalLabel').text('Update Tour for Week');
            $('#tourForm')[0].reset();
            $('#tour_id').val('week_update');
            clearErrors();
            
            $('#name').val(config.name);
            $('#interval').val(config.interval);
            $('#open_time').val(config.open_time);
            $('#grace_time').val(config.grace_time);
            $('#tag_type').val(config.tag_type).trigger('change');
            $('#scheduled_days').val(config.scheduled_days).trigger('change');
            $('#users').val(config.users).trigger('change');
            
            var modal = new bootstrap.Modal(document.getElementById('tourModal'));
            modal.show();
        });

        // Open Modal for Edit
        $(document).on('click', '.edit-tour-btn', function() {
            var tour = JSON.parse($(this).attr('data-tour'));
            $('#tourForm')[0].reset();
            clearErrors();
            $('#tourModalLabel').text('Edit Tour');
            
            $('#tour_id').val(tour.id);
            $('#name').val(tour.name);
            $('#tag_type').val(tour.tag_type).trigger('change');
            
            $('#interval').val(tour.interval);
            $('#open_time').val(tour.open_time);
            $('#grace_time').val(tour.grace_time);
            
            $('#scheduled_days').val(tour.scheduled_days).trigger('change');

            // Force update placeholder for all multi-selects
            setTimeout(function() {
               $('#scheduled_days').trigger({type: 'select2:select'});
            }, 100);

            var modal = new bootstrap.Modal(document.getElementById('tourModal'));
            modal.show();
        });

        var currentTourItems = [];

        function renderTourItems(date) {
            var tbody = $('#items-tbody');
            tbody.empty();
            
            var filteredItems = currentTourItems.filter(function(item) {
                return item.date === date;
            });

            if (filteredItems.length > 0) {
                filteredItems.forEach(function(item) {
                    var statusHtml = item.status 
                        ? '<span class="badge bg-success">Completed</span>' 
                        : '<span class="badge bg-warning text-dark">Pending</span>';
                    
                    var formattedDate = 'N/A';
                    if (item.date) {
                        var parts = item.date.split('-');
                        if (parts.length === 3) {
                            var year = parts[0];
                            var monthIndex = parseInt(parts[1], 10) - 1;
                            var day = parseInt(parts[2], 10);
                            var dateObj = new Date(year, monthIndex, day);
                            formattedDate = day + ' ' + dateObj.toLocaleString('en-US', { month: 'long' }) + ' ' + year;
                        } else {
                            formattedDate = item.date;
                        }
                    }

                    var tr = `<tr>
                        <td class="fw-bold">${formattedDate}</td>
                        <td class="fw-bold text-primary">${item.start_time}</td>
                        <td class="fw-bold text-primary">${item.end_time}</td>
                        <td>${item.type || 'N/A'}</td>
                        <td>${statusHtml}</td>
                    </tr>`;
                    tbody.append(tr);
                });
            } else {
                tbody.append('<tr><td colspan="5" class="text-center text-muted py-4">No items found for the selected date.</td></tr>');
            }
        }

        // View Tour Items Modal
        $(document).on('click', '.view-tour-btn', function() {
            var tour = JSON.parse($(this).attr('data-tour'));
            $('#itemsModalLabel').text(tour.name + ' — Items');
            
            currentTourItems = tour.items || [];
            
            var dateFilter = $('#items-date-filter');
            dateFilter.empty();
            
            if (currentTourItems.length > 0) {
                // Get unique dates
                var uniqueDates = [...new Set(currentTourItems.map(item => item.date))].sort();
                
                uniqueDates.forEach(function(d) {
                    // Format date for display
                    var displayDate = 'Unknown Date';
                    if (d) {
                        var parts = d.split('-');
                        if (parts.length === 3) {
                            var year = parts[0];
                            var monthIndex = parseInt(parts[1], 10) - 1;
                            var day = parseInt(parts[2], 10);
                            var dateObj = new Date(year, monthIndex, day);
                            displayDate = dateObj.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
                        } else {
                            displayDate = d;
                        }
                    }
                    dateFilter.append(new Option(displayDate, d));
                });
                
                // Get local today string in YYYY-MM-DD
                var now = new Date();
                var today = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
                
                var defaultDate = uniqueDates.includes(today) ? today : uniqueDates[0];
                
                dateFilter.val(defaultDate);
                dateFilter.show();
                
                renderTourItems(defaultDate);
            } else {
                dateFilter.hide();
                $('#items-tbody').html('<tr><td colspan="5" class="text-center text-muted py-4">No items generated for this tour yet.</td></tr>');
            }
            
            var modal = new bootstrap.Modal(document.getElementById('itemsModal'));
            modal.show();
        });

        $('#items-date-filter').on('change', function() {
            renderTourItems($(this).val());
        });

        // Save Tour (Create / Update)
        $('#btnSaveTour').on('click', function() {
            var id = $('#tour_id').val();
            var isEdit = id !== '' && id !== 'week_update';
            var url = isEdit ? '{{ url("sites/tours/update") }}/' + id : '{{ route("sites.tours.store", $site->id) }}';
            var formData = new FormData($('#tourForm')[0]);
            
            clearErrors();
            var isValid = true;

            function checkField(fieldId) {
                var val = $('#' + fieldId).val();
                if(!val || (Array.isArray(val) && val.length === 0)) {
                    $('#error-' + fieldId).removeClass('d-none');
                    $('#' + fieldId).addClass('is-invalid');
                    $('#group-' + fieldId).addClass('has-error');
                    isValid = false;
                }
            }

            checkField('name');
            checkField('tag_type');
            checkField('scheduled_days');

            if(!isValid) {
                toastr.error('Please fill all required fields');
                return;
            }

            var btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json().then(data => ({ok: response.ok, body: data})))
            .then(res => {
                if(res.ok) {
                    toastr.success('Tour saved successfully');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(res.body.message || 'Validation error');
                    btn.prop('disabled', false).text('Save Tour');
                }
            })
            .catch(error => {
                console.error(error);
                toastr.error('An error occurred');
                btn.prop('disabled', false).text('Save Tour');
            });
        });

        // Delete Tour
        $(document).on('click', '.delete-tour-btn', function() {
            if(!confirm('Are you sure you want to delete this tour?')) return;
            
            var id = $(this).data('id');
            var btn = $(this);
            
            fetch('{{ url("sites/tours/delete") }}/' + id, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json().then(data => ({ok: response.ok, body: data})))
            .then(res => {
                if(res.ok) {
                    toastr.success('Tour deleted successfully');
                    $('#tour-row-' + id).fadeOut(300, function() { $(this).remove(); });
                } else {
                    toastr.error('Error deleting tour');
                }
            });
        });
    });
</script>
@endsection
