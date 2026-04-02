@extends('dashboardLayouts.main')
@section('title', 'Degree Edit')

@section('breadcrumbTitle', 'Degree Edit')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{route('catalogs.index', ['country' => $degree->country])}}">Catalogs</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(document).ready(function() {
        $('.select2-multiple').select2({
            placeholder: 'Select Option(s)',
            allowClear: true,
            width: '100%'
        });

        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            allowInput: true
        });
    });
</script>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card card-h-100">
            <div class="card-header justify-content-between d-flex align-items-center">
                <h4 class="card-title">Degree Edit</h4>
                <a href="{{route('catalogs.index', ['country' => $degree->country])}}" class="btn btn-sm btn-secondary-subtle"><i class="mdi mdi-arrow-right align-middle"></i> Back</a>
            </div>
            <div class="card-body">
                <form action="{{ route('degrees.update', $degree->id) }}" method="POST">
                    @csrf

                    <!-- 1. Basic Info -->
                    <h5 class="mb-3 text-primary">1. Basic Information</h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">University</label>
                            <select class="form-select" name="university_id" id="university_id">
                                <option value="">Select University</option>
                                @foreach($universities as $university)
                                <option value="{{ $university->id }}" {{ old('university_id', $degree->university_id) == $university->id ? 'selected' : '' }}>{{ $university->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Discipline Category</label>
                            <select class="form-select" name="discipline_category" id="discipline_category">
                                <option value="">Select Category</option>
                                @foreach($categories as $category => $subcategories)
                                <option value="{{ $category }}" {{ old('discipline_category', $degree->discipline_category) == $category ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Discipline Subcategory</label>
                            <select class="form-select" name="discipline_subcategory" id="discipline_subcategory">
                                <option value="">Select Subcategory</option>
                            </select>
                        </div>
                        <script>
                            const disciplineData = @json($categories);
                            const currentSubcategory = "{{ old('discipline_subcategory', $degree->discipline_subcategory) }}";

                            document.getElementById('discipline_category').addEventListener('change', function() {
                                const category = this.value;
                                const subcategorySelect = document.getElementById('discipline_subcategory');
                                subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';

                                if (category && disciplineData[category]) {
                                    disciplineData[category].forEach(sub => {
                                        const option = document.createElement('option');
                                        option.value = sub;
                                        option.textContent = sub;
                                        if (sub === currentSubcategory) {
                                            option.selected = true;
                                        }
                                        subcategorySelect.appendChild(option);
                                    });
                                }
                            });

                            // Trigger change on load to initialize subcategories
                            window.addEventListener('load', function() {
                                if (document.getElementById('discipline_category').value) {
                                    document.getElementById('discipline_category').dispatchEvent(new Event('change'));
                                }
                            });
                        </script>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Program</label>
                            <select class="form-select" name="program">
                                <option value="">Select Program</option>
                                <option value="Foundation Year" {{ old('program', $degree->program) == 'Foundation Year' ? 'selected' : '' }}>Foundation Year</option>
                                <option value="Bachelor" {{ old('program', $degree->program) == 'Bachelor' ? 'selected' : '' }}>Bachelor</option>
                                <option value="Masters" {{ old('program', $degree->program) == 'Masters' ? 'selected' : '' }}>Masters</option>
                                <option value="PHD" {{ old('program', $degree->program) == 'PHD' ? 'selected' : '' }}>PHD</option>
                                <option value="Doctoral" {{ old('program', $degree->program) == 'Doctoral' ? 'selected' : '' }}>Doctoral</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Course Name</label>
                            <input type="text" class="form-control" name="course_name" value="{{ old('course_name', $degree->course_name) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Country</label>
                            <input type="text" class="form-control" name="country" value="{{ old('country', $degree->country) }}" readonly>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">City</label>
                            <select class="form-select" name="city">
                                <option value="">Select City</option>
                                @foreach($cities as $city)
                                <option value="{{ $city->name }}" {{ old('city', $degree->city) == $city->name ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Region</label>
                            <select class="form-select" name="region">
                                <option value="">Select Region</option>
                                <option value="Africa" {{ old('region', $degree->region) == 'Africa' ? 'selected' : '' }}>Africa</option>
                                <option value="Asia" {{ old('region', $degree->region) == 'Asia' ? 'selected' : '' }}>Asia</option>
                                <option value="Australia" {{ old('region', $degree->region) == 'Australia' ? 'selected' : '' }}>Australia</option>
                                <option value="Europe" {{ old('region', $degree->region) == 'Europe' ? 'selected' : '' }}>Europe</option>
                                <option value="North America" {{ old('region', $degree->region) == 'North America' ? 'selected' : '' }}>North America</option>
                                <option value="South Amercia" {{ old('region', $degree->region) == 'South Amercia' ? 'selected' : '' }}>South Amercia</option>
                                <option value="United Kingdom" {{ old('region', $degree->region) == 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">University Type</label>
                            @php $selectedType = old('type', $degree->type ?? []); @endphp
                            <select class="form-select select2-multiple" name="type[]" multiple>
                                <option value="Private" {{ is_array($selectedType) && in_array('Private', $selectedType) ? 'selected' : '' }}>Private</option>
                                <option value="Public" {{ is_array($selectedType) && in_array('Public', $selectedType) ? 'selected' : '' }}>Public</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <!-- 2. Course Overview -->
                    <h5 class="mb-3 text-primary">2. Course Overview</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Overview</label>
                            <textarea class="form-control" name="overview" rows="20">{{ old('overview', $degree->overview) }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Programme/Course Structure</label>
                            <textarea class="form-control" name="structure" rows="20">{{ old('structure', $degree->structure) }}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Degree Duration</label>
                            <select class="form-select" name="duration">
                                <option value="">Select Duration</option>
                                <option value="1 year" {{ old('duration', $degree->duration) == '1 year' ? 'selected' : '' }}>1 year</option>
                                <option value="1.6 year" {{ old('duration', $degree->duration) == '1.6 year' ? 'selected' : '' }}>1.6 year</option>
                                <option value="2 years" {{ old('duration', $degree->duration) == '2 years' ? 'selected' : '' }}>2 years</option>
                                <option value="2.6 years" {{ old('duration', $degree->duration) == '2.6 years' ? 'selected' : '' }}>2.6 years</option>
                                <option value="3 years" {{ old('duration', $degree->duration) == '3 years' ? 'selected' : '' }}>3 years</option>
                                <option value="3.6 years" {{ old('duration', $degree->duration) == '3.6 years' ? 'selected' : '' }}>3.6 years</option>
                                <option value="4 years" {{ old('duration', $degree->duration) == '4 years' ? 'selected' : '' }}>4 years</option>
                                <option value="4.6 years" {{ old('duration', $degree->duration) == '4.6 years' ? 'selected' : '' }}>4.6 years</option>
                                <option value="5 years" {{ old('duration', $degree->duration) == '5 years' ? 'selected' : '' }}>5 years</option>
                                <option value="5.6 years" {{ old('duration', $degree->duration) == '5.6 years' ? 'selected' : '' }}>5.6 years</option>
                                <option value="6 years" {{ old('duration', $degree->duration) == '6 years' ? 'selected' : '' }}>6 years</option>
                                <option value="6.6 years" {{ old('duration', $degree->duration) == '6.6 years' ? 'selected' : '' }}>6.6 years</option>
                                <option value="7 years" {{ old('duration', $degree->duration) == '7 years' ? 'selected' : '' }}>7 years</option>
                                <option value="7.6 years" {{ old('duration', $degree->duration) == '7.6 years' ? 'selected' : '' }}>7.6 years</option>
                                <option value="8+ years" {{ old('duration', $degree->duration) == '8+ years' ? 'selected' : '' }}>8+ years</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Beginning (Intake)</label>
                            @php $selectedIntake = old('intake_beginning', $degree->intake_beginning ?? []); @endphp
                            <select class="form-select select2-multiple" name="intake_beginning[]" multiple>
                                <option value="Fall Intake" {{ is_array($selectedIntake) && in_array('Fall Intake', $selectedIntake) ? 'selected' : '' }}>Fall Intake</option>
                                <option value="Spring Intake" {{ is_array($selectedIntake) && in_array('Spring Intake', $selectedIntake) ? 'selected' : '' }}>Spring Intake</option>
                                <option value="Other" {{ is_array($selectedIntake) && in_array('Other', $selectedIntake) ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Course Format</label>
                            @php $selectedFormat = old('format', $degree->format ?? []); @endphp
                            <select class="form-select select2-multiple" name="format[]" multiple>
                                <option value="Full Time" {{ is_array($selectedFormat) && in_array('Full Time', $selectedFormat) ? 'selected' : '' }}>Full Time</option>
                                <option value="Part Time" {{ is_array($selectedFormat) && in_array('Part Time', $selectedFormat) ? 'selected' : '' }}>Part Time</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Course Mode</label>
                            @php $selectedMode = old('course_mode', $degree->course_mode ?? []); @endphp
                            <select class="form-select select2-multiple" name="course_mode[]" multiple>
                                <option value="Online" {{ is_array($selectedMode) && in_array('Online', $selectedMode) ? 'selected' : '' }}>Online</option>
                                <option value="On Campus" {{ is_array($selectedMode) && in_array('On Campus', $selectedMode) ? 'selected' : '' }}>On Campus</option>
                                <option value="Hybrid" {{ is_array($selectedMode) && in_array('Hybrid', $selectedMode) ? 'selected' : '' }}>Hybrid</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Program Language</label>
                            @php $selectedLang = old('language', $degree->language ?? []); @endphp
                            <select class="form-select select2-multiple" name="language[]" multiple>
                                @foreach(config('degree.languages', ['English', 'French', 'German', 'Spanish']) as $lang)
                                <option value="{{ $lang }}" {{ is_array($selectedLang) && in_array($lang, $selectedLang) ? 'selected' : '' }}>{{ $lang }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>

                    <!-- 3. Fees & Financials -->
                    <h5 class="mb-3 text-primary">3. Fees & Financials</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">EU/Local Candidate Fee/Year</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" name="eu_fee" value="{{ old('eu_fee', $degree->eu_fee) }}">
                                <select class="form-select" style="max-width: 100px;">
                                    <option value="EUR">EUR</option>
                                    <option value="USD">USD</option>
                                    <option value="GBP">GBP</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Non EU/International Candidate Fee</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" name="non_eu_fee" value="{{ old('non_eu_fee', $degree->non_eu_fee) }}">
                                <select class="form-select" style="max-width: 100px;">
                                    <option value="EUR">EUR</option>
                                    <option value="USD">USD</option>
                                    <option value="GBP">GBP</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Full Fee</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" name="full_fee" value="{{ old('full_fee', $degree->full_fee) }}">
                                <select class="form-select" style="max-width: 100px;">
                                    <option value="EUR">EUR</option>
                                    <option value="USD">USD</option>
                                    <option value="GBP">GBP</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Application Fee</label>
                            <select class="form-select" name="application_fee_required">
                                <option value="No" {{ old('application_fee_required', $degree->application_fee_required) == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ old('application_fee_required', $degree->application_fee_required) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="Check" {{ old('application_fee_required', $degree->application_fee_required) == 'Check' ? 'selected' : '' }}>Check</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Application Fee Amount & Currency</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" name="application_fee_amount" value="{{ old('application_fee_amount', $degree->application_fee_amount) }}">
                                <select class="form-select" name="application_fee_currency" style="max-width: 100px;">
                                    <option value="EUR" {{ old('application_fee_currency', $degree->application_fee_currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="USD" {{ old('application_fee_currency', $degree->application_fee_currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Installment Plan</label>
                            <select class="form-select" name="installment_plan">
                                <option value="No" {{ old('installment_plan', $degree->installment_plan) == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ old('installment_plan', $degree->installment_plan) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="Check" {{ old('installment_plan', $degree->installment_plan) == 'Check' ? 'selected' : '' }}>Check</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <!-- 4. Scholarships -->
                    <h5 class="mb-3 text-primary">4. Scholarships</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Scholarship</label>
                            <select class="form-select" name="scholarship">
                                <option value="No" {{ old('scholarship', $degree->scholarship) == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ old('scholarship', $degree->scholarship) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="Check" {{ old('scholarship', $degree->scholarship) == 'Check' ? 'selected' : '' }}>Check</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Scholarship Link</label>
                            <input type="url" class="form-control" name="scholarship_link" value="{{ old('scholarship_link', $degree->scholarship_link) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Other Scholarship Link</label>
                            <input type="url" class="form-control" name="other_scholarship_link" value="{{ old('other_scholarship_link', $degree->other_scholarship_link) }}">
                        </div>
                    </div>

                    <hr>

                    <!-- 5. Admission Requirements -->
                    <h5 class="mb-3 text-primary">5. Admission Requirements</h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Admission Requirement</label>
                            <textarea class="form-control" name="admission_requirements" rows="20">{{ old('admission_requirements', $degree->admission_requirements) }}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">GPA</label>
                            <select class="form-select" name="gpa_required">
                                <option value="Any" {{ old('gpa_required', $degree->gpa_required) == 'Any' ? 'selected' : '' }}>Any</option>
                                <option value="Check" {{ old('gpa_required', $degree->gpa_required) == 'Check' ? 'selected' : '' }}>Check</option>
                                <option value="1.0" {{ old('gpa_required', $degree->gpa_required) == '1.0' ? 'selected' : '' }}>1.0</option>
                                <option value="2.0" {{ old('gpa_required', $degree->gpa_required) == '2.0' ? 'selected' : '' }}>2.0</option>
                                <option value="2.1" {{ old('gpa_required', $degree->gpa_required) == '2.1' ? 'selected' : '' }}>2.1</option>
                                <option value="2.2" {{ old('gpa_required', $degree->gpa_required) == '2.2' ? 'selected' : '' }}>2.2</option>
                                <option value="2.3" {{ old('gpa_required', $degree->gpa_required) == '2.3' ? 'selected' : '' }}>2.3</option>
                                <option value="2.4" {{ old('gpa_required', $degree->gpa_required) == '2.4' ? 'selected' : '' }}>2.4</option>
                                <option value="2.5" {{ old('gpa_required', $degree->gpa_required) == '2.5' ? 'selected' : '' }}>2.5</option>
                                <option value="2.6" {{ old('gpa_required', $degree->gpa_required) == '2.6' ? 'selected' : '' }}>2.6</option>
                                <option value="2.7" {{ old('gpa_required', $degree->gpa_required) == '2.7' ? 'selected' : '' }}>2.7</option>
                                <option value="2.8" {{ old('gpa_required', $degree->gpa_required) == '2.8' ? 'selected' : '' }}>2.8</option>
                                <option value="2.9" {{ old('gpa_required', $degree->gpa_required) == '2.9' ? 'selected' : '' }}>2.9</option>
                                <option value="3.0" {{ old('gpa_required', $degree->gpa_required) == '3.0' ? 'selected' : '' }}>3.0</option>
                                <option value="3+" {{ old('gpa_required', $degree->gpa_required) == '3+' ? 'selected' : '' }}>3+</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">GMAT/GRE Test</label>
                            <select class="form-select" name="gmat_gre_test">
                                <option value="No" {{ old('gmat_gre_test', $degree->gmat_gre_test) == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes/Check" {{ old('gmat_gre_test', $degree->gmat_gre_test) == 'Yes/Check' ? 'selected' : '' }}>Yes/Check</option>
                                <option value="Check" {{ old('gmat_gre_test', $degree->gmat_gre_test) == 'Check' ? 'selected' : '' }}>Check</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="mt-3 mb-2">🌍 English Language Test</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">IELTS Academic</label><input type="text" class="form-control" name="ielts_academic" value="{{ old('ielts_academic', $degree->ielts_academic) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">TOEFL</label><input type="text" class="form-control" name="toefl" value="{{ old('toefl', $degree->toefl) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Duolingo English</label><input type="text" class="form-control" name="duolingo_english" value="{{ old('duolingo_english', $degree->duolingo_english) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">TOEIC</label><input type="text" class="form-control" name="toeic" value="{{ old('toeic', $degree->toeic) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Cambridge English</label><input type="text" class="form-control" name="cambridge_english" value="{{ old('cambridge_english', $degree->cambridge_english) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Other</label><input type="text" class="form-control" name="other_english_test" value="{{ old('other_english_test', $degree->other_english_test) }}"></div>
                    </div>

                    <h6 class="mt-3 mb-2">🇫🇷 French Language Test</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">DELF French</label><input type="text" class="form-control" name="delf_french" value="{{ old('delf_french', $degree->delf_french) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">DALF French</label><input type="text" class="form-control" name="dalf_french" value="{{ old('dalf_french', $degree->dalf_french) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">TCF French</label><input type="text" class="form-control" name="tcf_french" value="{{ old('tcf_french', $degree->tcf_french) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">TEF French</label><input type="text" class="form-control" name="tef_french" value="{{ old('tef_french', $degree->tef_french) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Other</label><input type="text" class="form-control" name="other_french_test" value="{{ old('other_french_test', $degree->other_french_test) }}"></div>
                    </div>

                    <hr>

                    <!-- 6. Intake & Dates -->
                    <h5 class="mb-3 text-primary">6. Intake & Dates</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">Fall Admission Start Date</label><input type="text" class="form-control datepicker" name="fall_admission_start_date" value="{{ old('fall_admission_start_date', $degree->fall_admission_start_date) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Fall Admission Last Date</label><input type="text" class="form-control datepicker" name="fall_admission_last_date" value="{{ old('fall_admission_last_date', $degree->fall_admission_last_date) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Fall Course Start Date</label><input type="text" class="form-control datepicker" name="fall_course_start_date" value="{{ old('fall_course_start_date', $degree->fall_course_start_date) }}"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">Spring Admission Start Date</label><input type="text" class="form-control datepicker" name="spring_admission_start_date" value="{{ old('spring_admission_start_date', $degree->spring_admission_start_date) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Spring Admission Last Date</label><input type="text" class="form-control datepicker" name="spring_admission_last_date" value="{{ old('spring_admission_last_date', $degree->spring_admission_last_date) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Spring Course Start Date</label><input type="text" class="form-control datepicker" name="spring_course_start_date" value="{{ old('spring_course_start_date', $degree->spring_course_start_date) }}"></div>
                    </div>

                    <hr>

                    <!-- 7. Visa & Work Opportunities -->
                    <h5 class="mb-3 text-primary">7. Visa & Work Opportunities</h5>
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Post Study Work Visa</label>
                            <select class="form-select" name="post_study_work_visa">
                                <option value="No" {{ old('post_study_work_visa', $degree->post_study_work_visa) == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ old('post_study_work_visa', $degree->post_study_work_visa) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="Check" {{ old('post_study_work_visa', $degree->post_study_work_visa) == 'Check' ? 'selected' : '' }}>Check</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Post Study Visa Duration</label>
                            <select class="form-select" name="post_study_visa_duration">
                                <option value="">Select Duration</option>
                                <option value="1 year" {{ old('post_study_visa_duration', $degree->post_study_visa_duration) == '1 year' ? 'selected' : '' }}>1 year</option>
                                <option value="2 years" {{ old('post_study_visa_duration', $degree->post_study_visa_duration) == '2 years' ? 'selected' : '' }}>2 years</option>
                                <option value="3+ years" {{ old('post_study_visa_duration', $degree->post_study_visa_duration) == '3+ years' ? 'selected' : '' }}>3+ years</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">PR Path Available?</label>
                            <select class="form-select" name="pr_path_available">
                                <option value="No" {{ old('pr_path_available', $degree->pr_path_available) == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ old('pr_path_available', $degree->pr_path_available) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="Check" {{ old('pr_path_available', $degree->pr_path_available) == 'Check' ? 'selected' : '' }}>Check</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Part Time Job Allowed</label>
                            <select class="form-select" name="part_time_job_allowed">
                                <option value="No" {{ old('part_time_job_allowed', $degree->part_time_job_allowed) == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ old('part_time_job_allowed', $degree->part_time_job_allowed) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="Check" {{ old('part_time_job_allowed', $degree->part_time_job_allowed) == 'Check' ? 'selected' : '' }}>Check</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Part-time work allowed hours/week</label>
                            <input type="text" class="form-control" name="part_time_work_hours" value="{{ old('part_time_work_hours', $degree->part_time_work_hours) }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Part Time Per Hour Wages</label>
                            <input type="text" class="form-control" name="part_time_per_hour_wages" value="{{ old('part_time_per_hour_wages', $degree->part_time_per_hour_wages) }}">
                        </div>
                    </div>

                    <hr>

                    <!-- 8. Career Outcomes -->
                    <h5 class="mb-3 text-primary">8. Career Outcomes</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Salary After Graduation (Minimum-Maximum)/Year</label>
                            <input type="text" class="form-control" name="salary_after_graduation" value="{{ old('salary_after_graduation', $degree->salary_after_graduation) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dual Degree Option</label>
                            <select class="form-select" name="double_degree">
                                <option value="No" {{ old('double_degree', $degree->double_degree) == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ old('double_degree', $degree->double_degree) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="Any" {{ old('double_degree', $degree->double_degree) == 'Any' ? 'selected' : '' }}>Any</option>
                                <option value="Check" {{ old('double_degree', $degree->double_degree) == 'Check' ? 'selected' : '' }}>Check</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <!-- 9. Faculty -->
                    <h5 class="mb-3 text-primary">9. Faculty</h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Faculty Link</label>
                            <input type="url" class="form-control" name="faculty_link" value="{{ old('faculty_link', $degree->faculty_link) }}">
                        </div>
                    </div>

                    <hr>

                    <!-- 10. Admission Contact -->
                    <h5 class="mb-3 text-primary">10. Admission Contact</h5>
                    <!-- <h6 class="mt-3 mb-2">International Office</h6> -->
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">Department/Name</label><input type="text" class="form-control" name="international_office_name" placeholder="International Admission Office" value="{{ old('international_office_name', $degree->international_office_name) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="international_office_email" value="{{ old('international_office_email', $degree->international_office_email) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Contact</label><input type="text" class="form-control" name="international_office_contact" value="{{ old('international_office_contact', $degree->international_office_contact) }}"></div>
                    </div>
                    <!-- <h6 class="mt-3 mb-2">Local Admission Office</h6> -->
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">Department/Name</label><input type="text" class="form-control" name="local_office_name" placeholder="Local Admission Office" value="{{ old('local_office_name', $degree->local_office_name) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="local_office_email" value="{{ old('local_office_email', $degree->local_office_email) }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Contact</label><input type="text" class="form-control" name="local_office_contact" value="{{ old('local_office_contact', $degree->local_office_contact) }}"></div>
                    </div>

                    <hr>

                    <!-- 11. Additional Information -->
                    <h5 class="mb-3 text-primary">11. Additional Information</h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Additional Information</label>
                            <textarea class="form-control" name="degree_additional_info" rows="20">{{ old('degree_additional_info', $degree->degree_additional_info) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-md">Update Degree</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection