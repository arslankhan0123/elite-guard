@extends('dashboardLayouts.main')
@section('title', $site->name . ' - Check Points')

@section('breadcrumbTitle', $site->name . ' - Check Points')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{route('sites.index')}}">Sites</a></li>
<li class="breadcrumb-item active">{{ $site->name }} - Check Points</li>
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

    /* Modal overrides */
    .nfc-modal-header {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        padding: 1.5rem 1.75rem;
        border-radius: 0.75rem 0.75rem 0 0;
    }

    .nfc-modal-header .modal-icon-wrap {
        width: 46px;
        height: 46px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .nfc-field-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 0.4rem;
    }

    .nfc-field-hint {
        font-size: 0.78rem;
        color: #94a3b8;
        margin-top: 0.3rem;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm" style="border-radius: 0.75rem; overflow: hidden;">
            <div class="nfc-card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon-wrap">
                        <i data-feather="wifi" style="width:20px;height:20px;color:#fff;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-white" style="font-size:1.05rem;">{{ $site->name }} &mdash; Check Points</h5>
                        <span class="text-white d-block" style="opacity:0.75;font-size:0.8rem;">Manage checkpoints for this site</span>
                    </div>
                </div>
                <button type="button" class="btn btn-light fw-semibold px-4"
                    style="border-radius:8px; font-size:0.85rem;"
                    data-bs-toggle="modal" data-bs-target="#createNfcModal">
                    <i data-feather="plus-circle" style="width:15px;height:15px;" class="me-1"></i> Create Check Point
                </button>
            </div>
            <div class="card-body p-4">

                {{-- Success / Error alerts --}}
                <div id="ajax-alert" class="d-none"></div>

                <div class="table-responsive">
                    <table id="custom-table" class="table nfc-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>UID <span class="fw-normal ms-1" style="text-transform:none;letter-spacing:0; color:white">(click to copy)</span></th>
                                <th>Tag Name</th>
                                <th>Site</th>
                                <th>Company</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="nfc-tags-tbody">
                            @foreach($site->nfcTags as $tag)
                            <tr id="nfc-row-{{ $tag->id }}">
                                <td class="text-muted fw-semibold">{{ $tag->id }}</td>
                                <td>
                                    <span class="uid-chip"
                                        onclick="copyToClipboard('{{ $tag->uid }}')"
                                        data-bs-toggle="tooltip" title="Click to copy UID">
                                        <i data-feather="copy" style="width:11px;height:11px;" class="me-1"></i>{{ $tag->uid }}
                                    </span>
                                </td>
                                <td class="fw-bold">{{ $tag->name }}</td>
                                <td>{{ $tag->site->name ?? 'N/A' }}</td>
                                <td>{{ $tag->site->company->name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if($tag->status)
                                    <span class="badge bg-soft-success text-success fw-bold px-3 rounded-pill">Active</span>
                                    @else
                                    <span class="badge bg-soft-secondary text-secondary fw-bold px-3 rounded-pill">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a class="text-decoration-none edit-nfc-btn" href="#"
                                            data-id="{{ $tag->id }}"
                                            data-uid="{{ $tag->uid }}"
                                            data-name="{{ $tag->name }}"
                                            data-status="{{ $tag->status ? '1' : '0' }}"
                                            data-type="{{ $tag->type }}"
                                            data-bs-toggle="modal" data-bs-target="#editNfcModal"
                                            title="Edit Check Point">
                                            <button class="editBtn">
                                                <svg height="1em" viewBox="0 0 512 512">
                                                    <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                                                </svg>
                                            </button>
                                        </a>
                                        <button class="bin-button delete-nfc-btn"
                                            data-id="{{ $tag->id }}"
                                            data-name="{{ $tag->name }}"
                                            data-bs-toggle="tooltip" title="Delete Check Point">
                                            <svg class="bin-top" viewBox="0 0 39 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <line y1="5" x2="39" y2="5" stroke="white" stroke-width="4"></line>
                                                <line x1="12" y1="1.5" x2="26.0357" y2="1.5" stroke="white" stroke-width="3"></line>
                                            </svg>
                                            <svg class="bin-bottom" viewBox="0 0 33 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <mask id="path-1-inside-1_8_19" fill="white">
                                                    <path d="M0 0H33V35C33 37.2091 31.2091 39 29 39H4C1.79086 39 0 37.2091 0 35V0Z"></path>
                                                </mask>
                                                <path d="M0 0H33H0ZM37 35C37 39.4183 33.4183 43 29 43H4C-0.418278 43 -4 39.4183 -4 35H4H29H37ZM4 43C-0.418278 43 -4 39.4183 -4 35V0H4V35V43ZM37 0V35C37 39.4183 33.4183 43 29 43V35V0H37Z" fill="white" mask="url(#path-1-inside-1_8_19)"></path>
                                                <path d="M12 6L12 29" stroke="white" stroke-width="4"></path>
                                                <path d="M21 6V29" stroke="white" stroke-width="4"></path>
                                            </svg>
                                        </button>
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

{{-- ========================= CREATE NFC MODAL ========================= --}}
<div class="modal fade" id="createNfcModal" tabindex="-1" aria-labelledby="createNfcModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:0.75rem; overflow:hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.18);">

            {{-- Modal Header --}}
            <div class="nfc-modal-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-icon-wrap">
                        <i data-feather="wifi" style="width:22px;height:22px;color:#fff;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="createNfcModalLabel" style="font-size:1.1rem;">Create Check Point</h5>
                        <span class="text-white d-block" style="opacity:0.75; font-size:0.8rem;">Add a new Check Point to <strong>{{ $site->name }}</strong></span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="opacity:0.8;"></button>
            </div>

            <form id="createNfcForm">
                @csrf
                <input type="hidden" name="site_id" id="modal_site_id" value="{{ $site->id }}">

                {{-- Modal Body --}}
                <div class="modal-body p-4" style="background:#fff;">

                    {{-- Validation errors --}}
                    <div id="modal-errors" class="alert alert-danger border-0 rounded-3 d-none py-2 px-3 mb-4" style="font-size:0.84rem; background:#fef2f2;"></div>

                    <div class="row g-3">
                        {{-- Tag Name --}}
                        <div class="col-md-4">
                            <label class="nfc-field-label">Tag Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="modal_name" class="form-control"
                                style="border-radius:8px; padding:0.65rem 0.9rem;"
                                placeholder="e.g. Entrance Gate A, Patrol Point 5" required>
                        </div>

                        {{-- Tag Type --}}
                        <div class="col-md-4">
                            <label class="nfc-field-label">Tag Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_tag_type" name="type" style="border-radius:8px; padding:0.65rem 0.9rem;" required>
                                <option value="" disabled selected>Select...</option>
                                <option value="nfc">NFC</option>
                                <option value="image">Image</option>
                                <option value="both">NFC + Image</option>
                            </select>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-4">
                            <label class="nfc-field-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="modal_status" class="form-select" style="border-radius:8px; padding:0.65rem 0.9rem;" required>
                                <option value="1">✅ Active</option>
                                <option value="0">⛔ Inactive</option>
                            </select>
                        </div>

                        {{-- NFC UID --}}
                        <div class="col-12" id="modal_uid_container">
                            <label class="nfc-field-label">NFC UID <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="border-radius:8px 0 0 8px; background:#f8f7ff; border-color:#e2e8f0;">
                                    <i data-feather="hash" style="width:15px;height:15px;color:#7c3aed;"></i>
                                </span>
                                <input type="text" name="uid" id="modal_uid" class="form-control"
                                    style="border-color:#e2e8f0; font-family:monospace; font-size:0.9rem;"
                                    placeholder="e.g. NFC-8273645" required>
                                <button class="btn fw-semibold" type="button" onclick="generateModalUid()"
                                    style="border-radius:0 8px 8px 0; background:#ede9fe; color:#7c3aed; border:1px solid #c4b5fd; font-size:0.85rem;">
                                    <i data-feather="refresh-cw" style="width:13px;height:13px;" class="me-1"></i>Generate
                                </button>
                            </div>
                            <p class="nfc-field-hint mb-0">This ID will be written to the physical NFC card or chip.</p>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer" style="background:#f8f9fa; border-top:1px solid #e9ecef; padding:1rem 1.5rem; border-radius:0 0 0.75rem 0.75rem;">
                    <button type="button" class="btn btn-outline-secondary px-4" style="border-radius:8px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold" style="border-radius:8px; background:linear-gradient(135deg,#7c3aed,#6d28d9); border:none;" id="createNfcBtn">
                        <i data-feather="save" style="width:15px;height:15px;" class="me-1"></i> Save Check Point
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================= EDIT NFC MODAL ========================= --}}
<div class="modal fade" id="editNfcModal" tabindex="-1" aria-labelledby="editNfcModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:0.75rem; overflow:hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.18);">

            {{-- Modal Header --}}
            <div class="nfc-modal-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-icon-wrap">
                        <i data-feather="wifi" style="width:22px;height:22px;color:#fff;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="editNfcModalLabel" style="font-size:1.1rem;">Edit Check Point</h5>
                        <span class="text-white d-block" style="opacity:0.75; font-size:0.8rem;">Update Check Point details</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="opacity:0.8;"></button>
            </div>

            <form id="editNfcForm">
                @csrf
                <input type="hidden" name="site_id" id="edit_modal_site_id" value="{{ $site->id }}">
                <input type="hidden" name="tag_id" id="edit_modal_tag_id">

                {{-- Modal Body --}}
                <div class="modal-body p-4" style="background:#fff;">

                    {{-- Validation errors --}}
                    <div id="edit-modal-errors" class="alert alert-danger border-0 rounded-3 d-none py-2 px-3 mb-4" style="font-size:0.84rem; background:#fef2f2;"></div>

                    <div class="row g-3">
                        {{-- Tag Name --}}
                        <div class="col-md-4">
                            <label class="nfc-field-label">Tag Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_modal_name" class="form-control"
                                style="border-radius:8px; padding:0.65rem 0.9rem;"
                                placeholder="e.g. Entrance Gate A, Patrol Point 5" required>
                        </div>

                        {{-- Tag Type --}}
                        <div class="col-md-4">
                            <label class="nfc-field-label">Tag Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_modal_tag_type" name="type" style="border-radius:8px; padding:0.65rem 0.9rem;" required>
                                <option value="" disabled>Select...</option>
                                <option value="nfc">NFC</option>
                                <option value="image">Image</option>
                                <option value="both">NFC + Image</option>
                            </select>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-4">
                            <label class="nfc-field-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="edit_modal_status" class="form-select" style="border-radius:8px; padding:0.65rem 0.9rem;" required>
                                <option value="1">✅ Active</option>
                                <option value="0">⛔ Inactive</option>
                            </select>
                        </div>

                        {{-- NFC UID --}}
                        <div class="col-12" id="edit_modal_uid_container">
                            <label class="nfc-field-label">NFC UID <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="border-radius:8px 0 0 8px; background:#f8f7ff; border-color:#e2e8f0;">
                                    <i data-feather="hash" style="width:15px;height:15px;color:#7c3aed;"></i>
                                </span>
                                <input type="text" name="uid" id="edit_modal_uid" class="form-control"
                                    style="border-color:#e2e8f0; font-family:monospace; font-size:0.9rem;"
                                    placeholder="e.g. NFC-8273645" required>
                                <button class="btn fw-semibold" type="button" onclick="generateEditModalUid()"
                                    style="border-radius:0 8px 8px 0; background:#ede9fe; color:#7c3aed; border:1px solid #c4b5fd; font-size:0.85rem;">
                                    <i data-feather="refresh-cw" style="width:13px;height:13px;" class="me-1"></i>Generate
                                </button>
                            </div>
                            <p class="nfc-field-hint mb-0">This ID will be written to the physical NFC card or chip.</p>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer" style="background:#f8f9fa; border-top:1px solid #e9ecef; padding:1rem 1.5rem; border-radius:0 0 0.75rem 0.75rem;">
                    <button type="button" class="btn btn-outline-secondary px-4" style="border-radius:8px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold" style="border-radius:8px; background:linear-gradient(135deg,#7c3aed,#6d28d9); border:none;" id="editNfcBtn">
                        <i data-feather="save" style="width:15px;height:15px;" class="me-1"></i> Update Check Point
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    /* ── Copy UID to clipboard ── */
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            toastr.success('UID copied: ' + text, 'Success');
        }).catch(() => {
            toastr.error('Failed to copy UID.', 'Error');
        });
    }

    /* ── Generate a random NFC UID inside the modal ── */
    function generateModalUid() {
        const chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        let result = 'NFC-';
        for (let i = 0; i < 10; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('modal_uid').value = result;
    }

    /* ── Generate a random NFC UID inside the edit modal ── */
    function generateEditModalUid() {
        const chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        let result = 'NFC-';
        for (let i = 0; i < 10; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('edit_modal_uid').value = result;
    }

    /* ── Show a dismissible alert above the table ── */
    function showAlert(type, message) {
        const el = document.getElementById('ajax-alert');
        el.className = `alert alert-${type} alert-dismissible fade show rounded-3`;
        el.innerHTML = `${message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    }

    /* ── AJAX Delete Tag Event ── */
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.delete-nfc-btn');
        if (btn) {
            e.preventDefault();
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            if (confirm(`Are you sure you want to delete the Check Point "${name}"?`)) {
                let deleteUrl = '{{ route("nfc.deleteAjax", ":id") }}'.replace(':id', id);

                fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                            'Accept': 'application/json',
                        }
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        const row = document.getElementById('nfc-row-' + id);
                        if (row) {
                            row.style.transition = 'all 0.4s ease';
                            row.style.opacity = '0';
                            row.style.transform = 'translateY(-10px)';
                            setTimeout(() => {
                                row.remove();
                            }, 400);
                        }
                        showAlert('success', '✓ Check Point <strong>' + name + '</strong> deleted successfully!');
                        toastr.success('Check Point "' + name + '" has been deleted.', 'Deleted');
                    })
                    .catch(err => {
                        showAlert('danger', err.message || 'An error occurred while deleting the Check Point.');
                        toastr.error('An error occurred while deleting the Check Point.', 'Error');
                    });
            }
        }
    });

    /* ── Populating Edit Modal ── */
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.edit-nfc-btn');
        if (btn) {
            e.preventDefault();
            document.getElementById('edit_modal_tag_id').value = btn.dataset.id;
            document.getElementById('edit_modal_name').value = btn.dataset.name;
            document.getElementById('edit_modal_uid').value = btn.dataset.uid || '';
            document.getElementById('edit_modal_status').value = btn.dataset.status;
            document.getElementById('edit_modal_tag_type').value = btn.dataset.type || '';
            document.getElementById('edit-modal-errors').classList.add('d-none');
            document.getElementById('edit-modal-errors').innerHTML = '';
            toggleEditUidField();
        }
    });

    /* ── Tag Type Toggle Logic ── */
    const createTagType = document.getElementById('modal_tag_type');
    const createUidContainer = document.getElementById('modal_uid_container');
    const createUidInput = document.getElementById('modal_uid');

    function toggleCreateUidField() {
        if (createTagType.value === 'image') {
            createUidContainer.style.display = 'none';
            createUidInput.removeAttribute('required');
            createUidInput.value = '';
        } else {
            createUidContainer.style.display = 'block';
            createUidInput.setAttribute('required', 'required');
        }
    }
    createTagType.addEventListener('change', toggleCreateUidField);

    const editTagType = document.getElementById('edit_modal_tag_type');
    const editUidContainer = document.getElementById('edit_modal_uid_container');
    const editUidInput = document.getElementById('edit_modal_uid');

    function toggleEditUidField() {
        if (editTagType.value === 'image') {
            editUidContainer.style.display = 'none';
            editUidInput.removeAttribute('required');
            editUidInput.value = '';
        } else {
            editUidContainer.style.display = 'block';
            editUidInput.setAttribute('required', 'required');
        }
    }
    editTagType.addEventListener('change', toggleEditUidField);

    /* ── AJAX Update form submission ── */
    document.getElementById('editNfcForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('edit_modal_tag_id').value;
        const btn = document.getElementById('editNfcBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';

        const formData = new FormData(this);
        let updateUrl = '{{ route("nfc.updateAjax", ":id") }}'.replace(':id', id);

        fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: formData,
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                /* Close the modal */
                bootstrap.Modal.getInstance(document.getElementById('editNfcModal')).hide();

                /* Update existing row */
                updateNfcRow(data.tag);

                /* Re-init feather icons */
                feather.replace();

                showAlert('success', '✓ Check Point <strong>' + data.tag.name + '</strong> updated successfully!');
                toastr.success('Check Point "' + data.tag.name + '" has been updated.', 'Updated');
            })
            .catch(err => {
                const errDiv = document.getElementById('edit-modal-errors');
                let msgs = '';
                if (err.errors) {
                    Object.values(err.errors).forEach(e => {
                        e.forEach(m => {
                            msgs += `<div>• ${m}</div>`;
                            toastr.error(m, 'Validation Error');
                        });
                    });
                } else if (err.message) {
                    msgs = err.message;
                    toastr.error(err.message, 'Error');
                } else {
                    msgs = 'An error occurred. Please try again.';
                    toastr.error('An error occurred. Please try again.', 'Error');
                }
                errDiv.innerHTML = msgs;
                errDiv.classList.remove('d-none');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i data-feather="save" style="width:15px;height:15px;" class="me-1"></i> Update Check Point';
                feather.replace();
            });
    });

    /* ── Update existing <tr> contents ── */
    function updateNfcRow(tag) {
        const row = document.getElementById('nfc-row-' + tag.id);
        if (!row) return;

        const statusBadge = tag.status ?
            '<span class="badge bg-soft-success text-success fw-bold px-3 rounded-pill">Active</span>' :
            '<span class="badge bg-soft-secondary text-secondary fw-bold px-3 rounded-pill">Inactive</span>';

        const siteName = tag.site ? tag.site.name : 'N/A';
        const companyName = (tag.site && tag.site.company) ? tag.site.company.name : 'N/A';

        const deleteUrl = '{{ route("nfc.delete", ":id") }}'.replace(':id', tag.id);

        row.innerHTML = `
            <td class="text-muted fw-semibold">${tag.id}</td>
            <td>
                <span class="uid-chip"
                    onclick="copyToClipboard('${tag.uid || ''}')"
                    data-bs-toggle="tooltip" title="Click to copy UID">
                    <i data-feather="copy" style="width:11px;height:11px;" class="me-1"></i>${tag.uid || ''}
                </span>
            </td>
            <td class="fw-bold">${tag.name}</td>
            <td>${siteName}</td>
            <td>${companyName}</td>
            <td class="text-center">${statusBadge}</td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                    <a class="text-decoration-none edit-nfc-btn" href="#"
                        data-id="${tag.id}"
                        data-uid="${tag.uid || ''}"
                        data-name="${tag.name}"
                        data-status="${tag.status ? '1' : '0'}"
                        data-type="${tag.type || ''}"
                        data-bs-toggle="modal" data-bs-target="#editNfcModal"
                        title="Edit Check Point">
                        <button class="editBtn">
                            <svg height="1em" viewBox="0 0 512 512"><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path></svg>
                        </button>
                    </a>
                    <button class="bin-button delete-nfc-btn"
                        data-id="${tag.id}"
                        data-name="${tag.name}"
                        data-bs-toggle="tooltip" title="Delete Check Point">
                        <svg class="bin-top" viewBox="0 0 39 7" fill="none" xmlns="http://www.w3.org/2000/svg"><line y1="5" x2="39" y2="5" stroke="white" stroke-width="4"></line><line x1="12" y1="1.5" x2="26.0357" y2="1.5" stroke="white" stroke-width="3"></line></svg>
                        <svg class="bin-bottom" viewBox="0 0 33 39" fill="none" xmlns="http://www.w3.org/2000/svg"><mask id="path-3-inside-${tag.id}" fill="white"><path d="M0 0H33V35C33 37.2091 31.2091 39 29 39H4C1.79086 39 0 37.2091 0 35V0Z"></path></mask><path d="M0 0H33H0ZM37 35C37 39.4183 33.4183 43 29 43H4C-0.418278 43 -4 39.4183 -4 35H4H29H37ZM4 43C-0.418278 43 -4 39.4183 -4 35V0H4V35V43ZM37 0V35C37 39.4183 33.4183 43 29 43V35V0H37Z" fill="white" mask="url(#path-3-inside-${tag.id})"></path><path d="M12 6L12 29" stroke="white" stroke-width="4"></path><path d="M21 6V29" stroke="white" stroke-width="4"></path></svg>
                    </button>
                </div>
            </td>
        `;

        /* Re-init Bootstrap tooltips for the updated row */
        row.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el);
        });
    }

    /* ── Auto-generate UID when modal opens ── */
    document.getElementById('createNfcModal').addEventListener('show.bs.modal', function() {
        document.getElementById('modal_tag_type').value = 'nfc';
        toggleCreateUidField();
        generateModalUid();
        document.getElementById('modal_name').value = '';
        document.getElementById('modal_status').value = '1';
        document.getElementById('modal-errors').classList.add('d-none');
        document.getElementById('modal-errors').innerHTML = '';
    });

    /* ── AJAX form submission ── */
    document.getElementById('createNfcForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = document.getElementById('createNfcBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        const formData = new FormData(this);

        fetch('{{ route("nfc.storeAjax") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: formData,
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                /* Close the modal */
                bootstrap.Modal.getInstance(document.getElementById('createNfcModal')).hide();

                /* Append new row to the table */
                appendNfcRow(data.tag);

                /* Re-init feather icons for the new row */
                feather.replace();

                showAlert('success', '✓ Check Point <strong>' + data.tag.name + '</strong> created successfully!');
                toastr.success('Check Point "' + data.tag.name + '" has been created.', 'Created');
            })
            .catch(err => {
                /* Show validation errors */
                const errDiv = document.getElementById('modal-errors');
                let msgs = '';
                if (err.errors) {
                    Object.values(err.errors).forEach(e => {
                        e.forEach(m => {
                            msgs += `<div>• ${m}</div>`;
                            toastr.error(m, 'Validation Error')
                        });
                    });
                } else if (err.message) {
                    msgs = err.message;
                    toastr.error(err.message, 'Error');
                } else {
                    msgs = 'An error occurred. Please try again.';
                    toastr.error('An error occurred. Please try again.', 'Error');
                }
                errDiv.innerHTML = msgs;
                errDiv.classList.remove('d-none');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i data-feather="check" style="width:16px;height:16px;" class="me-1"></i> Create Tag';
                feather.replace();
            });
    });

    /* ── Build and prepend a new <tr> ── */
    function appendNfcRow(tag) {
        const statusBadge = tag.status ?
            '<span class="badge bg-soft-success text-success fw-bold px-3 rounded-pill">Active</span>' :
            '<span class="badge bg-soft-secondary text-secondary fw-bold px-3 rounded-pill">Inactive</span>';

        const siteName = tag.site ? tag.site.name : 'N/A';
        const companyName = (tag.site && tag.site.company) ? tag.site.company.name : 'N/A';

        const deleteUrl = '{{ route("nfc.delete", ":id") }}'.replace(':id', tag.id);

        const tr = document.createElement('tr');
        tr.id = 'nfc-row-' + tag.id;
        tr.style.animation = 'fadeIn 0.4s ease';
        tr.innerHTML = `
            <td class="text-muted fw-semibold">${tag.id}</td>
            <td>
                <span class="uid-chip"
                    onclick="copyToClipboard('${tag.uid || ''}')"
                    data-bs-toggle="tooltip" title="Click to copy UID">
                    <i data-feather="copy" style="width:11px;height:11px;" class="me-1"></i>${tag.uid || ''}
                </span>
            </td>
            <td class="fw-bold">${tag.name}</td>
            <td>${siteName}</td>
            <td>${companyName}</td>
            <td class="text-center">${statusBadge}</td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                    <a class="text-decoration-none edit-nfc-btn" href="#"
                        data-id="${tag.id}"
                        data-uid="${tag.uid || ''}"
                        data-name="${tag.name}"
                        data-status="${tag.status ? '1' : '0'}"
                        data-type="${tag.type || ''}"
                        data-bs-toggle="modal" data-bs-target="#editNfcModal"
                        title="Edit Check Point">
                        <button class="editBtn">
                            <svg height="1em" viewBox="0 0 512 512"><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path></svg>
                        </button>
                    </a>
                    <button class="bin-button delete-nfc-btn"
                        data-id="${tag.id}"
                        data-name="${tag.name}"
                        data-bs-toggle="tooltip" title="Delete Check Point">
                        <svg class="bin-top" viewBox="0 0 39 7" fill="none" xmlns="http://www.w3.org/2000/svg"><line y1="5" x2="39" y2="5" stroke="white" stroke-width="4"></line><line x1="12" y1="1.5" x2="26.0357" y2="1.5" stroke="white" stroke-width="3"></line></svg>
                        <svg class="bin-bottom" viewBox="0 0 33 39" fill="none" xmlns="http://www.w3.org/2000/svg"><mask id="path-3-inside-${tag.id}" fill="white"><path d="M0 0H33V35C33 37.2091 31.2091 39 29 39H4C1.79086 39 0 37.2091 0 35V0Z"></path></mask><path d="M0 0H33H0ZM37 35C37 39.4183 33.4183 43 29 43H4C-0.418278 43 -4 39.4183 -4 35H4H29H37ZM4 43C-0.418278 43 -4 39.4183 -4 35V0H4V35V43ZM37 0V35C37 39.4183 33.4183 43 29 43V35V0H37Z" fill="white" mask="url(#path-3-inside-${tag.id})"></path><path d="M12 6L12 29" stroke="white" stroke-width="4"></path><path d="M21 6V29" stroke="white" stroke-width="4"></path></svg>
                    </button>
                </div>
            </td>
        `;

        /* Prepend so newest is on top */
        const tbody = document.getElementById('nfc-tags-tbody');
        tbody.insertBefore(tr, tbody.firstChild);

        /* Re-init Bootstrap tooltips for the new row */
        tr.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el);
        });
    }
</script>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection