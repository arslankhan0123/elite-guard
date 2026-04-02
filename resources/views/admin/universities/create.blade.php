@extends('dashboardLayouts.main')
@section('title', 'University Create')

@section('breadcrumbTitle', 'University Create')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{route('catalogs.index', ['country' => $countryName])}}">Catalogs</a></li>
<li class="breadcrumb-item active">Create</li>
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
                <h4 class="card-title">University Create</h4>
                <a href="{{route('catalogs.index', ['country' => $countryName])}}" class="btn btn-sm btn-secondary-subtle"><i class="mdi mdi-arrow-right align-middle"></i> Back</a>
            </div>
            <div class="card-body">
                <form action="{{ route('universities.store', ['countryName' => $countryName]) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- ================= BASIC ================= -->
                    <h5 class="text-primary">🏫 Basic Information</h5>

                    <div class="row">
                        <div class="col-md-4">
                            <label>University Name</label>
                            <input type="text" name="name" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Website</label>
                            <input type="text" name="website" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Foundation Year</label>
                            <input type="number" name="foundation_year" class="form-control">
                        </div>
                    </div>


                    <!-- ================= LOCATION ================= -->
                    <h5 class="text-primary mt-4">📍 Location</h5>

                    <div class="row">
                        <div class="col-md-4">
                            <label>Country</label>
                            <input type="text" class="form-control" name="country" value="{{ old('country', $countryName) }}" readonly>
                        </div>

                        <div class="col-md-4">
                            <label>City</label>
                            <select class="form-select" name="city">
                                <option value="">Select City</option>
                                @foreach($cities as $city)
                                <option value="{{ $city->name }}" {{ old('city') == $city->name ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Campus Locations</label>
                            <input type="text" name="campus_locations[]" class="form-control">
                        </div>
                    </div>


                    <!-- ================= MEDIA ================= -->
                    <h5 class="text-primary mt-4">🖼️ Media</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Background Uni Photo</label>
                            <input type="file" name="background_photo" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>University Photos (4-6)</label>
                            <input type="file" name="photos[]" multiple class="form-control">
                        </div>

                        <div class="col-md-12 mt-3">
                            <label>Youtube Video Links</label>
                            <input type="text" name="youtube_links[]" class="form-control mb-2">
                            <input type="text" name="youtube_links[]" class="form-control mb-2">
                            <input type="text" name="youtube_links[]" class="form-control">
                        </div>
                    </div>


                    <!-- ================= ACADEMIC ================= -->
                    <h5 class="text-primary mt-4">🎓 Academic Information</h5>

                    <div class="row">

                        <div class="col-md-6">
                            <label>Programs</label>
                            <select name="programs[]" multiple class="form-control select2-multiple">
                                <option value="Foundation">Foundation</option>
                                <option value="Bachelor">Bachelor</option>
                                <option value="Masters">Masters</option>
                                <option value="PHD">PHD</option>
                                <option value="Doctoral">Doctoral</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Double Degree</label>
                            <select name="double_degree" class="form-control">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                                <option value="Check">Check</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Exchange Program</label>
                            <select name="exchange_program" class="form-control">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                                <option value="Check">Check</option>
                            </select>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label>University Faculty Links</label>
                            <input type="text" name="faculty_links[]" class="form-control mb-2">
                            <input type="text" name="faculty_links[]" class="form-control">
                        </div>

                    </div>



                    <!-- ================= RANKING ================= -->
                    <h5 class="text-primary mt-4">🏆 Rankings</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <label>World Ranking</label>
                            <input type="text" name="world_ranking" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>Country Ranking</label>
                            <input type="text" name="country_ranking" class="form-control">
                        </div>
                    </div>



                    <!-- ================= PARTNERS ================= -->
                    <h5 class="text-primary mt-4">🌍 Partnerships</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Partner Universities</label>
                            <input type="text" name="partner_universities[]" class="form-control mb-2">
                            <input type="text" name="partner_universities[]" class="form-control mb-2">
                            <input type="text" name="partner_universities[]" class="form-control mb-2">
                            <input type="text" name="partner_universities[]" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>Partner Companies Links</label>
                            <input type="text" name="partner_companies_links[]" class="form-control mb-2">
                            <input type="text" name="partner_companies_links[]" class="form-control mb-2">
                            <input type="text" name="partner_companies_links[]" class="form-control mb-2">
                            <input type="text" name="partner_companies_links[]" class="form-control">
                        </div>
                    </div>



                    <!-- ================= ACCREDITATION ================= -->
                    <h5 class="text-primary mt-4">🎓 Accreditation</h5>

                    <input type="text" name="accreditations[]" class="form-control mb-2">
                    <input type="text" name="accreditations[]" class="form-control">


                    <!-- ================= FACILITIES ================= -->
                    <h5 class="text-primary mt-4">🏫 Facilities</h5>

                    <div class="row">
                        <div class="col-md-2">
                            <label>Gym</label>
                            <select name="gym" class="form-control">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                                <option value="Check">Check</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label>Library</label>
                            <select name="library" class="form-control">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                                <option value="Check">Check</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label>Career Fairs</label>
                            <select name="career_fairs" class="form-control">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                                <option value="Check">Check</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label>Hostel</label>
                            <select name="hostel" class="form-control">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                                <option value="Check">Check</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Sports Teams</label>
                            <input type="text" name="sports_teams" class="form-control">
                        </div>
                    </div>



                    <!-- ================= ALUMNI ================= -->
                    <h5 class="text-primary mt-4">🤝 Alumni</h5>

                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="alumni_linkedin" placeholder="Linkedin" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="alumni_facebook" placeholder="Facebook" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="alumni_instagram" placeholder="Instagram" class="form-control">
                        </div>
                    </div>



                    <!-- ================= COMMUNITY ================= -->
                    <h5 class="text-primary mt-4">👥 Community</h5>

                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="community_linkedin" placeholder="Linkedin" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="community_facebook" placeholder="Facebook" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="community_instagram" placeholder="Instagram" class="form-control">
                        </div>
                    </div>



                    <!-- ================= SOCIAL ================= -->
                    <h5 class="text-primary mt-4">📱 Social Media</h5>

                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="linkedin" placeholder="Linkedin" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <input type="text" name="facebook" placeholder="Facebook" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <input type="text" name="instagram" placeholder="Instagram" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <input type="text" name="twitter" placeholder="Twitter" class="form-control">
                        </div>
                    </div>



                    <!-- ================= CONTACT ================= -->
                    <h5 class="text-primary mt-4">📞 Local Office</h5>

                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="local_office_name" placeholder="Name" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="local_office_email" placeholder="Email" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="local_office_contact" placeholder="Contact" class="form-control">
                        </div>
                    </div>


                    <h5 class="text-primary mt-4">📞 International Office</h5>

                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="international_office_name" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="international_office_email" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="international_office_contact" class="form-control">
                        </div>
                    </div>

                    <!-- ================= PROS CONS ================= -->
                    <h5 class="text-primary mt-4">👍 Pros / Cons</h5>

                    <textarea name="pros" class="form-control mb-2" placeholder="Pros"></textarea>
                    <textarea name="cons" class="form-control" placeholder="Cons"></textarea>


                    <button class="btn btn-primary mt-4">Create University</button>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection