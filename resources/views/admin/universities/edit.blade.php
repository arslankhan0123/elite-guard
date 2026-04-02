@extends('dashboardLayouts.main')
@section('title', 'University Update')
@section('breadcrumbTitle', 'University Update')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{route('catalogs.index', ['country' => $countryName])}}">Catalogs</a></li>
<li class="breadcrumb-item active">Update</li>
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
                <h4 class="card-title">University Update</h4>
                <a href="{{route('catalogs.index', ['country' => $countryName])}}" class="btn btn-sm btn-secondary-subtle"><i class="mdi mdi-arrow-right align-middle"></i> Back</a>
            </div>
            <div class="card-body">
                <form action="{{ route('universities.update', ['university_id' => $university->id, 'country' => $countryName]) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- ================= BASIC ================= -->
                    <h5 class="text-primary">🏫 Basic Information</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <label>University Name</label>
                            <input type="text" value="{{$university->name}}" name="name" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Website</label>
                            <input type="text" value="{{$university->website}}" name="website" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Foundation Year</label>
                            <input type="number" value="{{$university->foundation_year}}" name="foundation_year" class="form-control">
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
                                <option value="{{ $city->name }}" {{ old('city', $university->city) == $city->name ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Campus Locations</label>
                            <input type="text" value="{{ implode(', ', $university->campus_locations ?? []) }}" name="campus_locations[]" class="form-control">
                        </div>
                    </div>

                    <!-- ================= MEDIA ================= -->
                    <h5 class="text-primary mt-4">🖼️ Media</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Background Uni Photo</label>
                            @if($university->background_photo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/'.$university->background_photo) }}" class="bg-uni-img">
                            </div>
                            @endif
                            <input type="file" name="background_photo" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>University Photos (4-6)</label>
                            <div class="mb-2 d-flex flex-wrap gap-2">
                                @if($university->photos)
                                @foreach($university->photos as $photo)
                                <div class="uni-photo-box">
                                    <img src="{{ asset('storage/'.$photo) }}" class="uni-photo">
                                </div>
                                @endforeach
                                @endif
                            </div>
                            <input type="file" name="photos[]" multiple class="form-control">
                        </div>

                        <div class="col-md-12 mt-3">
                            <label>Youtube Video Links</label>
                            @php
                            $youtube_links = $university->youtube_links ?? ['','',''];
                            @endphp
                            @foreach($youtube_links as $link)
                            <input type="text" name="youtube_links[]" class="form-control mb-2" value="{{ $link }}">
                            @endforeach
                        </div>
                    </div>

                    <!-- ================= ACADEMIC ================= -->
                    <h5 class="text-primary mt-4">🎓 Academic Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Programs</label>
                            <select name="programs[]" multiple class="form-control select2-multiple">
                                @php $selectedPrograms = $university->programs ?? []; @endphp
                                @foreach(['Foundation','Bachelor','Masters','PHD','Doctoral','Others'] as $program)
                                <option value="{{ $program }}" {{ in_array($program,$selectedPrograms) ? 'selected' : '' }}>{{ $program }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Double Degree</label>
                            <select name="double_degree" class="form-control">
                                @foreach(['No','Yes','Check'] as $val)
                                <option value="{{ $val }}" {{ $university->double_degree==$val ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Exchange Program</label>
                            <select name="exchange_program" class="form-control">
                                @foreach(['No','Yes','Check'] as $val)
                                <option value="{{ $val }}" {{ $university->exchange_program==$val ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label>University Faculty Links</label>
                            @php $faculty_links = $university->faculty_links ?? ['','']; @endphp
                            @foreach($faculty_links as $link)
                            <input type="text" name="faculty_links[]" class="form-control mb-2" value="{{ $link }}">
                            @endforeach
                        </div>
                    </div>

                    <!-- ================= RANKING ================= -->
                    <h5 class="text-primary mt-4">🏆 Rankings</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <label>World Ranking</label>
                            <input type="text" name="world_ranking" class="form-control" value="{{ $university->world_ranking }}">
                        </div>
                        <div class="col-md-6">
                            <label>Country Ranking</label>
                            <input type="text" name="country_ranking" class="form-control" value="{{ $university->country_ranking }}">
                        </div>
                    </div>

                    <!-- ================= PARTNERS ================= -->
                    <h5 class="text-primary mt-4">🌍 Partnerships</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Partner Universities</label>
                            @php $partners = $university->partner_universities ?? ['','','','']; @endphp
                            @foreach($partners as $p)
                            <input type="text" name="partner_universities[]" class="form-control mb-2" value="{{ $p }}">
                            @endforeach
                        </div>
                        <div class="col-md-6">
                            <label>Partner Companies Links</label>
                            @php $company_links = $university->partner_companies_links ?? ['','','','']; @endphp
                            @foreach($company_links as $p)
                            <input type="text" name="partner_companies_links[]" class="form-control mb-2" value="{{ $p }}">
                            @endforeach
                        </div>
                    </div>

                    <!-- ================= ACCREDITATION ================= -->
                    <h5 class="text-primary mt-4">🎓 Accreditation</h5>
                    @php $accreditations = $university->accreditations ?? ['','']; @endphp
                    @foreach($accreditations as $a)
                    <input type="text" name="accreditations[]" class="form-control mb-2" value="{{ $a }}">
                    @endforeach

                    <!-- ================= FACILITIES ================= -->
                    <h5 class="text-primary mt-4">🏫 Facilities</h5>
                    <div class="row">
                        @foreach(['gym','library','career_fairs','hostel'] as $facility)
                        <div class="col-md-2">
                            <label>{{ ucfirst(str_replace('_',' ',$facility)) }}</label>
                            <select name="{{ $facility }}" class="form-control">
                                @foreach(['No','Yes','Check'] as $val)
                                <option value="{{ $val }}" {{ $university->$facility==$val ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endforeach
                        <div class="col-md-4">
                            <label>Sports Teams</label>
                            <input type="text" name="sports_teams" class="form-control" value="{{ $university->sports_teams }}">
                        </div>
                    </div>

                    <!-- ================= ALUMNI ================= -->
                    <h5 class="text-primary mt-4">🤝 Alumni</h5>
                    <div class="row">
                        <div class="col-md-4"><input type="text" name="alumni_linkedin" placeholder="Linkedin" class="form-control" value="{{ $university->alumni_linkedin }}"></div>
                        <div class="col-md-4"><input type="text" name="alumni_facebook" placeholder="Facebook" class="form-control" value="{{ $university->alumni_facebook }}"></div>
                        <div class="col-md-4"><input type="text" name="alumni_instagram" placeholder="Instagram" class="form-control" value="{{ $university->alumni_instagram }}"></div>
                    </div>

                    <!-- ================= COMMUNITY ================= -->
                    <h5 class="text-primary mt-4">👥 Community</h5>
                    <div class="row">
                        <div class="col-md-4"><input type="text" name="community_linkedin" placeholder="Linkedin" class="form-control" value="{{ $university->community_linkedin }}"></div>
                        <div class="col-md-4"><input type="text" name="community_facebook" placeholder="Facebook" class="form-control" value="{{ $university->community_facebook }}"></div>
                        <div class="col-md-4"><input type="text" name="community_instagram" placeholder="Instagram" class="form-control" value="{{ $university->community_instagram }}"></div>
                    </div>

                    <!-- ================= SOCIAL ================= -->
                    <h5 class="text-primary mt-4">📱 Social Media</h5>
                    <div class="row">
                        @foreach(['linkedin','facebook','instagram','twitter'] as $social)
                        <div class="col-md-3">
                            <input type="text" name="{{ $social }}" placeholder="{{ ucfirst($social) }}" class="form-control" value="{{ $university->$social }}">
                        </div>
                        @endforeach
                    </div>

                    <!-- ================= CONTACT ================= -->
                    <h5 class="text-primary mt-4">📞 Local Office</h5>
                    <div class="row">
                        <div class="col-md-4"><input type="text" name="local_office_name" placeholder="Name" class="form-control" value="{{ $university->local_office_name }}"></div>
                        <div class="col-md-4"><input type="text" name="local_office_email" placeholder="Email" class="form-control" value="{{ $university->local_office_email }}"></div>
                        <div class="col-md-4"><input type="text" name="local_office_contact" placeholder="Contact" class="form-control" value="{{ $university->local_office_contact }}"></div>
                    </div>

                    <h5 class="text-primary mt-4">📞 International Office</h5>
                    <div class="row">
                        <div class="col-md-4"><input type="text" name="international_office_name" class="form-control" value="{{ $university->international_office_name }}"></div>
                        <div class="col-md-4"><input type="text" name="international_office_email" class="form-control" value="{{ $university->international_office_email }}"></div>
                        <div class="col-md-4"><input type="text" name="international_office_contact" class="form-control" value="{{ $university->international_office_contact }}"></div>
                    </div>

                    <!-- ================= PROS CONS ================= -->
                    <h5 class="text-primary mt-4">👍 Pros / Cons</h5>
                    <textarea name="pros" class="form-control mb-2" placeholder="Pros">{{ $university->pros }}</textarea>
                    <textarea name="cons" class="form-control" placeholder="Cons">{{ $university->cons }}</textarea>

                    <button class="btn btn-primary mt-4">Update University</button>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
    .bg-uni-img {
        width: 100%;
        max-width: 250px;
        height: 140px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    .uni-photo-box {
        width: 90px;
        height: 90px;
        overflow: hidden;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    .uni-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
@endsection