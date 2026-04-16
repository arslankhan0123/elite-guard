@extends('dashboardLayouts.main')
@section('title', 'Orientation Create')

@section('breadcrumbTitle', 'Orientation Create')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('orientations.index') }}">Orientations</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header justify-content-between d-flex align-items-center">
                    <h4 class="card-title shine">Create New Orientation</h4>
                    <a href="{{ route('orientations.index') }}" class="btn btn-sm btn-secondary-subtle">
                        <i class="mdi mdi-arrow-left align-middle"></i> Back to Listing
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('orientations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Orientation Type <span class="text-danger">*</span></label>
                                <input type="text" name="type" class="form-control" value="{{ old('type') }}"
                                    required placeholder="e.g. Workplace Safety, Remote Work, etc.">
                                @error('type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-3">
                            <!-- <div class="col-md-6 mb-3">
                                <label class="form-label">Orientation Document <span class="text-danger">*</span></label>
                                <input type="file" name="document" class="form-control" required
                                    accept=".pdf,.doc,.docx,.txt,.png,.jpg,.jpeg">
                                <small class="text-muted">Accepted formats: PDF, DOC, DOCX, TXT, PNG, JPG (Max 5MB)</small>
                                @error('document')
                                    <br><span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div> -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Passing Percentage (%) <span class="text-danger">*</span></label>
                                <input type="number" name="passing_percentage" class="form-control" value="{{ old('passing_percentage', 80) }}"
                                    required min="0" max="100">
                                <small class="text-muted">User must achieve this score to sign the orientation.</small>
                                @error('passing_percentage')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Orientation Details / Description</label>
                                <textarea name="description" id="editor" class="form-control">{{ old('description') }}</textarea>
                                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Quiz Questions (MCQs)</h5>
                            <button type="button" id="add-question" class="btn btn-sm btn-success">
                                <i class="mdi mdi-plus me-1"></i> Add Question
                            </button>
                        </div>

                        <div id="questions-container">
                            <!-- Questions will be added here -->
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-4">Create Orientation</button>
                                <a href="{{ route('orientations.index') }}" class="btn btn-light px-4">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: [
                'heading', '|', 
                'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|',
                'undo', 'redo'
            ]
        })
        .catch(error => {
            console.error(error);
        });

    let questionIndex = 0;

    document.getElementById('add-question').addEventListener('click', function() {
        const container = document.getElementById('questions-container');
        const questionHtml = `
            <div class="card mb-3 border question-item" data-index="${questionIndex}">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Question #${questionIndex + 1}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-question">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Question Text</label>
                        <input type="text" name="questions[${questionIndex}][text]" class="form-control" required placeholder="Enter question here...">
                    </div>
                    <div class="options-container ms-4">
                        <h6>Options</h6>
                        <div class="option-list"></div>
                        <button type="button" class="btn btn-sm btn-soft-primary add-option">
                            <i class="mdi mdi-plus me-1"></i> Add Option
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', questionHtml);
        
        // Add initial 2 options for the new question
        const questionItem = container.querySelector(`[data-index="${questionIndex}"]`);
        addOption(questionItem, questionIndex);
        addOption(questionItem, questionIndex);

        questionIndex++;
    });

    function addOption(questionItem, qIndex) {
        const list = questionItem.querySelector('.option-list');
        const oIndex = list.children.length;
        const optionHtml = `
            <div class="input-group mb-2 option-item">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="radio" name="questions[${qIndex}][correct_option]" value="${oIndex}" required title="Mark as correct">
                </div>
                <input type="text" name="questions[${qIndex}][options][${oIndex}][text]" class="form-control" placeholder="Option text..." required>
                <button class="btn btn-outline-danger remove-option" type="button"><i class="mdi mdi-close"></i></button>
            </div>
        `;
        list.insertAdjacentHTML('beforeend', optionHtml);
        
        // Update correct_option indexes if needed (but since we use oIndex, we just append)
        // If we remove an option, we might need to re-index. Let's simplify and just keep adding.
    }

    document.getElementById('questions-container').addEventListener('click', function(e) {
        if (e.target.closest('.remove-question')) {
            e.target.closest('.question-item').remove();
            reIndexQuestions();
        }

        if (e.target.closest('.add-option')) {
            const questionItem = e.target.closest('.question-item');
            const qIndex = questionItem.getAttribute('data-index');
            addOption(questionItem, qIndex);
        }

        if (e.target.closest('.remove-option')) {
            const optionList = e.target.closest('.option-list');
            const questionItem = e.target.closest('.question-item');
            const qIndex = questionItem.getAttribute('data-index');
            e.target.closest('.option-item').remove();
            reIndexOptions(optionList, qIndex);
        }
    });

    function reIndexQuestions() {
        const questions = document.querySelectorAll('.question-item');
        questionIndex = 0;
        questions.forEach((q, idx) => {
            q.setAttribute('data-index', idx);
            q.querySelector('h6').textContent = `Question #${idx + 1}`;
            q.querySelector('input[name*="[text]"]').setAttribute('name', `questions[${idx}][text]`);
            
            const options = q.querySelectorAll('.option-item');
            options.forEach((o, oIdx) => {
                o.querySelector('input[type="radio"]').setAttribute('name', `questions[${idx}][correct_option]`);
                o.querySelector('input[type="radio"]').value = oIdx;
                o.querySelector('input[type="text"]').setAttribute('name', `questions[${idx}][options][${oIdx}][text]`);
            });
            questionIndex = idx + 1;
        });
    }

    function reIndexOptions(list, qIndex) {
        const options = list.querySelectorAll('.option-item');
        options.forEach((o, idx) => {
            o.querySelector('input[type="radio"]').value = idx;
            o.querySelector('input[type="text"]').setAttribute('name', `questions[${qIndex}][options][${idx}][text]`);
        });
    }
</script>
<style>
    .ck-editor__editable {
        min-height: 250px;
    }
</style>
@endsection
