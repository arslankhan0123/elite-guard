<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-user-id" content="{{ auth()->id() }}">
    <title>@yield('title', 'Quick News')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Pichforest" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('logo.png')}}">


    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Font Awesome for Icons 6.0.0 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- plugin css -->
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.9.96/css/materialdesignicons.min.css" rel="stylesheet">
    <!-- <link href="{{ asset('adminDashboard') }}/assets/css/jsvectormap.min.css" rel="stylesheet" type="text/css" /> -->

    <!-- Bootstrap Css -->
    <link href="{{ asset('adminDashboard/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"> -->
    <!-- <link href="{{ asset('https://preview.pichforest.com/dashonic/layouts/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" /> -->


    <!-- Icons Css -->
    <link href="{{ asset('adminDashboard/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- <link href="{{ asset('https://preview.pichforest.com/dashonic/layouts/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" /> -->

    <!-- App Css-->
    <link href="{{ asset('adminDashboard/assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <!-- <link href="{{ asset('https://preview.pichforest.com/dashonic/layouts/assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" /> -->

    <!-- Toaster Link -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">


    <!-- Header dropdown css  -->
    <!-- <style>
        .dropdown-menu {
            width: 100%;
            margin: 0;
            overflow-y: auto;
            height: 250px;
            scrollbar-width: thin;
            scrollbar-color: #dcdcdc #f1f1f1;
        }

        .dropdown-menu::-webkit-scrollbar {
            width: 6px;
        }

        .dropdown-menu::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .dropdown-menu::-webkit-scrollbar-thumb {
            background-color: #dcdcdc;
            border-radius: 10px;
        }

        .dropdown-menu::-webkit-scrollbar-thumb:hover {
            background-color: #b0b0b0;
        }
    </style> -->


    <!-- Action Buttons -->
    <style>
        /* View Button */
        .view_btn {
            --fs: 1.25em;
            --col1: honeydew;
            --col3: #17a2b8;
            --col4: honeydew;
            /* --pd: .5em .65em; */
            width: 1.875rem;
            height: 1.875rem;
            display: grid;
            align-content: baseline;
            appearance: none;
            border: 0;
            grid-template-columns: min-content 1fr;
            padding: var(--pd);
            font-size: var(--fs);
            color: var(--col1);
            background-color: var(--col3);
            border-radius: 6px;
            position: relative;
            transition: all 0.75s ease-out;
            transform-origin: center;
        }

        .view_btn:hover {
            color: var(--col4);
        }

        .view_btn:active {
            animation: offset 1s ease-in-out infinite;
            outline-offset: 0;
        }

        .view_btn::after,
        .view_btn::before {
            content: "";
            align-self: center;
            justify-self: center;
            height: 0.5em;
            margin: 0 0.5em;
            grid-column: 1;
            grid-row: 1;
            opacity: 1;
        }

        .view_btn::after {
            position: relative;
            border: 2px solid var(--col4);
            border-radius: 50%;
            transition: all 0.5s ease-out;
            height: 0.1em;
            width: 0.1em;
            top: 0.438rem;
            left: -0.188rem;
        }

        .view_btn:hover::after {
            border: 2px solid var(--col3);
            transform: rotate(-120deg) translate(10%, 164%);
        }

        .view_btn::before {
            border-radius: 50% 0%;
            border: 4px solid var(--col4);
            transition: all 1s ease-out;
            transform: rotate(45deg);
            height: 1rem;
            width: 1rem;
            position: relative;
            top: 0.438rem;
            left: -0.188rem;
        }

        .view_btn:hover::before {
            border-radius: 50%;
            border: 4px solid var(--col1);
            transform: scale(1.25) rotate(0deg);
            animation: blink 1.5s ease-out 1s infinite alternate;
        }

        @keyframes blink {
            0% {
                transform: scale(1, 1) skewX(0deg);
                opacity: 1;
            }

            5% {
                transform: scale(1.5, 0.1) skewX(10deg);
                opacity: 0.5;
            }

            10%,
            35% {
                transform: scale(1, 1) skewX(0deg);
                opacity: 1;
            }

            40% {
                transform: scale(1.5, 0.1) skewX(10deg);
                opacity: 0.25;
            }

            45%,
            100% {
                transform: scale(1, 1) skewX(0deg);
                opacity: 1;
            }
        }

        @keyframes offset {
            50% {
                outline-offset: 0.15em;
                outline-color: var(--col1);
            }

            55% {
                outline-offset: 0.1em;
                transform: translateY(1px);
            }

            80%,
            100% {
                outline-offset: 0;
            }
        }


        /* Edit Button */
        .editBtn {
            width: 1.875rem;
            height: 1.875rem;
            border-radius: 7px;
            border: none;
            background-color: rgb(93, 93, 116);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.123);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }

        .editBtn::before {
            content: "";
            width: 200%;
            height: 200%;
            background-color: rgb(102, 102, 141);
            position: absolute;
            z-index: 1;
            transform: scale(0);
            transition: all 0.3s;
            border-radius: 50%;
            filter: blur(10px);
        }

        .editBtn:hover::before {
            transform: scale(1);
        }

        .editBtn:hover {
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.336);
        }

        .editBtn svg {
            height: 0.813rem;
            fill: white;
            z-index: 3;
            transition: all 0.2s;
            transform-origin: bottom;
        }

        .editBtn:hover svg {
            transform: rotate(-15deg) translateX(5px);
        }

        .editBtn::after {
            content: "";
            width: 1.563rem;
            height: 0.094rem;
            position: absolute;
            bottom: 9px;
            left: -11px !important;
            background-color: white;
            border-radius: 2px;
            z-index: 2;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.5s ease-out;
        }

        .editBtn:hover::after {
            transform: scaleX(1);
            left: 0px;
            transform-origin: right;
        }

        /* Delete Button */
        .bin-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 1.875rem;
            height: 1.875rem;
            border-radius: 7px;
            background-color: rgb(255, 95, 95);
            cursor: pointer;
            border: none;
            transition-duration: 0.3s;
        }

        .bin-bottom {
            width: 0.625rem;
        }

        .bin-top {
            width: 0.875rem;
            transform-origin: right;
            transition-duration: 0.3s;
        }

        .bin-button:hover .bin-top {
            transform: rotate(45deg);
        }

        .bin-button:hover {
            background-color: rgb(255, 0, 0);
        }

        .bin-button:active {
            transform: scale(0.9);
        }

        .upload-button {
            width: 32px;
            height: 30px;
            background: #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: 0.25s;
        }

        .upload-icon {
            width: 14px;
            height: 14px;
        }

        .upload-button:hover {
            background: #084298;
            transform: translateY(-2px);
        }

        .upload-button:hover .upload-icon {
            animation: uploadMove 0.5s ease-in-out;
        }

        @keyframes uploadMove {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-3px);
            }

            100% {
                transform: translateY(0);
            }
        }


        /* AddButton */

        .plusButton {
            /* Config start */

            --plus_sideLength: 1.875rem;
            --plus_topRightTriangleSideLength: 0.9rem;
            /* Config end */
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid white;
            width: var(--plus_sideLength);
            height: var(--plus_sideLength);
            background-color: #084298;
            overflow: hidden;
            border-radius: 6px;
        }

        .plusButton::before {
            position: absolute;
            content: "";
            top: 0;
            right: 0;
            width: 0;
            height: 0;
            border-width: 0 var(--plus_topRightTriangleSideLength) var(--plus_topRightTriangleSideLength) 0;
            border-style: solid;
            border-color: transparent #6ea8fe transparent transparent;
            transition-timing-function: ease-in-out;
            transition-duration: 0.2s;
        }

        .plusButton:hover {
            cursor: pointer;
        }

        .plusButton:hover::before {
            --plus_topRightTriangleSideLength: calc(var(--plus_sideLength) * 2);
        }

        .plusButton:focus-visible::before {
            --plus_topRightTriangleSideLength: calc(var(--plus_sideLength) * 2);
        }

        .plusButton>.plusIcon,
        .plusButton>.minusIcon {
            fill: white;
            width: calc(var(--plus_sideLength) * 0.7);
            height: calc(var(--plus_sideLength) * 0.7);
            z-index: 1;
            transition-timing-function: ease-in-out;
            transition-duration: 0.2s;
        }

        .plusButton:hover>.plusIcon,
        .plusButton:hover>.minusIcon {
            fill: black;
            transform: rotate(180deg);
        }

        .plusButton:focus-visible>.plusIcon,
        .plusButton:focus-visible>.minusIcon {
            fill: black;
            transform: rotate(180deg);
        }


        /* Shine Heading CSS */
        .shine {
            font-size: 1.5em !important;
            font-weight: 900 !important;
            color: rgba(255, 255, 255, 0.3) !important;
            background: #222 -webkit-gradient(linear,
                    left top,
                    right top,
                    from(#222),
                    to(#222),
                    color-stop(0.5, #fff)) 0 0 no-repeat;
            background-image: -webkit-linear-gradient(-40deg,
                    transparent 0%,
                    transparent 40%,
                    #fff 50%,
                    transparent 60%,
                    transparent 100%);
            -webkit-background-clip: text;
            -webkit-background-size: 50px;
            -webkit-animation: zezzz;
            -webkit-animation-duration: 5s;
            -webkit-animation-iteration-count: infinite;
        }

        /* Shine Heading CSS */
        @-webkit-keyframes zezzz {

            0%,
            10% {
                background-position: -200px;
            }

            20% {
                background-position: top left;
            }

            100% {
                background-position: 200px;
            }
        }



        /* Customizing the tooltip style */
        .tooltip {
            position: relative;
            display: inline-block;
            border-bottom: 1px dotted black;
            /* If you want dots under the hoverable text */
        }

        /* Tooltip text */
        .tooltip .tooltiptext {
            visibility: hidden;
            width: 120px;
            background-color: black;
            color: #fff;
            text-align: center;
            padding: 5px 0;
            border-radius: 6px;

            /* Position the tooltip text - see examples below! */
            position: absolute;
            z-index: 1;
        }

        /* Show the tooltip text when you mouse over the tooltip container */
        .tooltip:hover .tooltiptext {
            visibility: visible;
        }

        /* select2 selected option CSS */
        .select2-selection__choice {
            background-color: #00249c !important;
            color: white !important;
            border: 1px solid #00249c !important;
        }

        .select2-selection {
            background-color: #f8f9fa !important;
            /* height: 38px; */
        }
    </style>

    <!-- DataTables CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-dt/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#custom-table').DataTable({
                "pagingType": "simple_numbers",
                "lengthChange": false,
                "searching": true,
                "ordering": false,
                "info": false,
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search records..."
                }
            });
        });
    </script>

    <!-- Pagination Custom Styles -->
    <style>
        .dataTables_paginate .paginate_button {
            border-radius: 5px;
            margin: 0 5px;
            padding: 8px 12px;
            font-size: 14px;
            color: #6c757d;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            transition: background-color 0.3s, color 0.3s;
        }

        .dataTables_paginate .paginate_button:hover {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        .dataTables_paginate .paginate_button:active {
            background-color: #0056b3;
        }

        .dataTables_paginate .paginate_button.current {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border: 1px solid #0056b3;
        }

        .dataTables_paginate .paginate_button.disabled {
            color: #ced4da;
            cursor: not-allowed;
            background-color: #f8f9fa;
        }

        /* --- Flagship Admin Tables --- */
        #custom-table {
            border-collapse: separate !important;
            border-spacing: 0 8px !important;
            width: 100% !important;
            border: none !important;
            margin-top: 15px !important;
        }

        #custom-table thead th {
            background-color: #1e1b4b !important;
            /* Deep Navy */
            color: #ffffff !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 0.8rem !important;
            letter-spacing: 0.05em !important;
            padding: 16px 20px !important;
            border: none !important;
        }

        #custom-table thead th:first-child {
            border-radius: 12px 0 0 12px !important;
        }

        #custom-table thead th:last-child {
            border-radius: 0 12px 12px 0 !important;
        }

        #custom-table tbody tr {
            background-color: #ffffff !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border-radius: 12px !important;
        }

        #custom-table tbody td {
            padding: 18px 20px !important;
            border: none !important;
            color: #475569 !important;
            font-size: 0.95rem !important;
            font-weight: 500 !important;
            vertical-align: middle !important;
        }

        /* Zebra Striping Alternative */
        #custom-table tbody tr:nth-child(even) {
            background-color: #f8fafc !important;
        }

        #custom-table tbody tr:hover {
            transform: translateY(-2px) scale(1.002) !important;
            background-color: #f5f3ff !important;
            /* Extremely soft violet */
            box-shadow: 0 10px 15px -3px rgba(124, 58, 237, 0.1) !important;
        }

        #custom-table tbody tr:hover td {
            color: #1e1b4b !important;
        }

        #custom-table tbody td:first-child {
            border-radius: 12px 0 0 12px !important;
        }

        #custom-table tbody td:last-child {
            border-radius: 0 12px 12px 0 !important;
        }

        /* --- Premium Pagination Refinement --- */
        .dataTables_paginate {
            margin-top: 30px !important;
            padding-top: 20px !important;
            border-top: 1px solid #e2e8f0 !important;
        }

        .dataTables_paginate .paginate_button {
            border-radius: 10px !important;
            margin: 0 4px !important;
            padding: 10px 18px !important;
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            color: #64748b !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02) !important;
        }

        .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background-color: #7c3aed !important;
            color: white !important;
            border-color: #7c3aed !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3) !important;
        }

        .dataTables_paginate .paginate_button.current {
            background-color: #7c3aed !important;
            color: white !important;
            border-color: #7c3aed !important;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2) !important;
        }

        .dataTables_paginate .paginate_button.previous,
        .dataTables_paginate .paginate_button.next {
            background: #f1f5f9 !important;
            border: none !important;
        }

        /* Professional DataTable Search */
        .dataTables_filter {
            position: relative;
            float: right !important;
            margin-bottom: 25px !important;
        }

        .dataTables_filter input {
            background: #ffffff !important;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
            border-radius: 12px !important;
            padding: 12px 15px 12px 45px !important;
            color: #1e1b4b !important;
            font-size: 0.9rem !important;
            width: 300px !important;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
            outline: none !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
            font-weight: 500 !important;
        }

        .dataTables_filter input:focus {
            border-color: #7c3aed !important;
            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.15), 0 10px 15px -3px rgba(124, 58, 237, 0.1) !important;
            width: 380px !important;
        }

        .dataTables_filter::before {
            content: "\f002";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #7c3aed;
            font-size: 0.9rem;
            pointer-events: none;
            z-index: 1;
            opacity: 0.7;
        }

        .dataTables_filter label {
            margin-bottom: 0 !important;
            color: transparent !important;
            font-size: 0 !important;
            display: block !important;
        }


        /* Logo Style */
        .custom-logo-style {
            width: 60px;
            height: 60px;
            border: 3px solid #00249c;
            padding: 5px;
            box-sizing: border-box;
        }

        .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #eff0f2 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 33px !important;
        }

        .btn .badge {
            top: -10px !important;
            right: 12px !important;
        }
    </style>
</head>

<body data-layout="horizontal">
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('dashboardLayouts.header')
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <!-- start Breadcrumb -->
                    <!-- Dynamic Breadcrumb -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-flex align-items-center justify-content-between">
                                <h4 class="mb-0 shine">
                                    @yield('breadcrumbTitle', '')
                                </h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        @yield('breadcrumbs', '<li class="breadcrumb-item active">Dashboard</li>')
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end Breadcrumb -->

                    <!-- Global Alerts -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-check-circle me-2 font-size-20"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('status') === 'profile-updated')
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-account-check me-2 font-size-20"></i>
                                <div>Profile information updated successfully!</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('status') === 'password-updated')
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-lock-check me-2 font-size-20"></i>
                                <div>Password has been changed successfully!</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-alert-circle me-2 font-size-20"></i>
                                <div>{{ session('error') }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any() && !session('status'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-alert-circle me-2 font-size-20"></i>
                                <div>Please check the form for errors.</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')

                </div>
            </div>
            <!-- End Page-content -->


            @include('dashboardLayouts.footer')
        </div>
        <!-- end main content-->

    </div>
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('adminDashboard/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminDashboard/assets/js/metismenujs.min.js') }}"></script>
    <script src="{{ asset('adminDashboard/assets/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('adminDashboard/assets/js/feather.min.js') }}"></script>

    <!-- apexcharts -->
    <script src="{{ asset('adminDashboard/assets/js/apexcharts.min.js') }}"></script>

    <!-- Vector map-->
    <script src="{{ asset('adminDashboard/assets/js/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('adminDashboard/assets/js/world-merc.js') }}"></script>

    <script src="{{ asset('adminDashboard/assets/js/dashboard-sales.init.js') }}"></script>

    <!-- <script src="https://preview.pichforest.com/dashonic/layouts/assets/js/app.js"></script> -->
    <!-- <script src="{{ asset('adminDashboard/assets/js/app.js') }}"></script> -->

    <!-- Toaster script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <!-- Select2 CDN and Links -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    @if (Session::has('success'))
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true
        }
        toastr.success("{{ session('success') }}")
    </script>
    @endif

    @if (Session::has('error'))
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true
        }
        toastr.error("{{ session('error') }}")
    </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipElements.forEach((el) => {
                new bootstrap.Tooltip(el, {
                    animation: true,
                    placement: 'top', // Adjust placement as needed
                });
            });
        });
    </script>
    <script>
        $('#topic-Select').select2({
            placeholder: "Select Topic",
            allowClear: true,
            tags: true
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#topicSelect').select2({
                placeholder: "Open to Select Topic",
                allowClear: true,
            });
        });
    </script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        feather.replace();
    </script>

    @yield('scripts')

</body>

</html>