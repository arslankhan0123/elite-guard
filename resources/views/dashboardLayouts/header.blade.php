<style>
    /* Keep the full desktop navigation compact enough to fit on one line. */
    #page-topbar .topnav-menu {
        min-width: 0;
    }

    #page-topbar .topnav-menu .navbar-collapse {
        flex-basis: 100%;
    }

    #page-topbar .topnav-menu .navbar-nav {
        display: flex;
        flex-wrap: nowrap;
        justify-content: space-between;
        width: 100%;
        gap: 0;
    }

    #page-topbar .topnav-menu .nav-item {
        flex: 0 1 auto;
        min-width: 0;
    }

    #page-topbar .topnav-menu .nav-link {
        white-space: normal;
        padding: 0.4rem 0.25rem;
        font-size: 0.68rem;
    }

    #page-topbar .topnav-menu .nav-link .nav-icon {
        margin-right: 0.2rem;
    }

    @media (max-width: 991.98px) {
        #page-topbar .topnav-menu .navbar-collapse {
            max-height: calc(100vh - 150px);
            overflow-y: auto;
        }

        #page-topbar .topnav-menu .navbar-nav {
            flex-direction: column;
            align-items: stretch;
        }

        #page-topbar .topnav-menu .nav-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
    }

    @media (min-width: 992px) and (max-width: 1250px) {
        #page-topbar .topnav-menu .nav-link {
            padding-left: 0.15rem;
            padding-right: 0.15rem;
            font-size: 0.64rem;
        }
    }

    /* Refined top navigation dropdowns (Profile and grouped navigation items). */
    #page-topbar .topnav .navbar-nav > .nav-item > .dropdown-menu {
        display: none;
        min-width: 210px;
        max-height: min(62vh, 460px);
        overflow-y: auto;
        padding: 5px;
        margin-top: 2px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.14);
    }

    #page-topbar .topnav .navbar-nav > .nav-item > .dropdown-menu.show {
        display: block;
    }

    #page-topbar .topnav .navbar-nav > .nav-item > .dropdown-menu .dropdown-item {
        display: flex;
        align-items: center;
        gap: 8px;
        min-height: 32px;
        padding: 6px 9px;
        border-radius: 5px;
        color: #475569;
        font-size: 0.76rem;
        line-height: 1.2;
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    #page-topbar .topnav .navbar-nav > .nav-item > .dropdown-menu .dropdown-item svg {
        flex: 0 0 16px;
        width: 16px;
        height: 16px;
        margin: 0;
        color: #64748b;
    }

    #page-topbar .topnav .navbar-nav > .nav-item > .dropdown-menu .dropdown-item:hover,
    #page-topbar .topnav .navbar-nav > .nav-item > .dropdown-menu .dropdown-item:focus {
        color: #312e81;
        background: #f3f4ff;
    }

    #page-topbar .topnav .navbar-nav > .nav-item > .dropdown-menu .dropdown-item:hover svg,
    #page-topbar .topnav .navbar-nav > .nav-item > .dropdown-menu .dropdown-item:focus svg {
        color: #4f46e5;
    }

    #page-topbar .topnav .navbar-nav > .nav-item > .dropdown-menu .dropdown-item.active {
        color: #312e81;
        background: #eef2ff;
        font-weight: 600;
    }

    @media (min-width: 992px) {
        #page-topbar .topnav .navbar-nav > .nav-item.dropdown:hover > .dropdown-menu:not(.show) {
            display: none !important;
        }
    }

    @media (max-width: 991.98px) {
        #page-topbar .topnav .navbar-nav > .nav-item > .dropdown-menu {
            max-height: 45vh;
            min-width: 0;
        }
    }

    #notificationList {
        max-height: 230px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    /* Header options scrollable */
    /* .topnav-menu {
        overflow-x: auto;
        white-space: nowrap;
    }

    .topnav-menu::-webkit-scrollbar {
        height: 6px;
    }

    .topnav-menu::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }

    .topnav-menu::-webkit-scrollbar-thumb:hover {
        background: #999;
    }

    .navbar-nav {
        flex-wrap: nowrap;
    } */
</style>
<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                @php
                    $__companyLogoPath = \App\Models\Setting::get('company_logo_path', '');
                    $__logoUrl = $__companyLogoPath
                        ? \Illuminate\Support\Facades\Storage::url($__companyLogoPath)
                        : asset('logo.png');
                @endphp
                <a href="{{route('dashboard')}}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ $__logoUrl }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ $__logoUrl }}" alt="" height="42">
                    </span>
                </a>

                <a href="{{route('dashboard')}}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ $__logoUrl }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ $__logoUrl }}" alt="" height="22">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 d-lg-none header-item" data-bs-toggle="collapse"
                data-bs-target="#topnav-menu-content">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <div class="d-flex align-items-center">

            {{-- ─── Notification Bell ─────────────────────────── --}}
            <div class="d-inline-block me-2 position-relative" id="notif-bell-wrapper">
                <button type="button" class="btn header-item position-relative px-3"
                    id="notif-bell-btn"
                    style="background:transparent;border:none;color:inherit;">
                    <i class="fa fa-bell fs-5" style="opacity:0.75;"></i>
                    <span id="notif-badge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size:0.6rem;min-width:18px;display:none;">0</span>
                </button>

                <div class="shadow-lg border-0 p-0"
                    id="notif-dropdown"
                    style="display:none;position:fixed;z-index:9999;border-radius:12px;overflow:hidden;background:#fff;">
                    {{-- Header --}}
                    <div class="d-flex align-items-center justify-content-between px-3 py-2"
                        style="background:#4f46e5;color:#fff;">
                        <span class="fw-semibold" style="font-size:0.9rem;">
                            <i class="fa fa-bell me-1"></i> Notifications
                            <span id="notif-header-count" class="badge bg-white text-primary ms-1" style="font-size:0.65rem;">0</span>
                        </span>
                        <button class="btn btn-sm px-2 py-0 text-white" id="notif-mark-all-read"
                            style="background:rgba(255,255,255,0.15);border:none;font-size:0.75rem;border-radius:6px;">
                            Mark all read
                        </button>
                    </div>

                    {{-- List --}}
                    <ul class="list-unstyled mb-0" id="notificationList"
                        style="max-height:320px;overflow-y:auto;overflow-x:hidden;">
                        <li class="text-center text-muted py-4" id="notif-empty" style="font-size:0.85rem;">
                            <i class="fa fa-check-circle me-1 text-success"></i> All caught up!
                        </li>
                    </ul>

                    {{-- Footer --}}
                    <div class="border-top text-center py-2" style="background:#f8fafc;">
                        <span style="font-size:0.75rem;color:#9ca3af;">Showing latest 50 notifications</span>
                    </div>
                </div>
            </div>
            {{-- ─────────────────────────────────────────────────── --}}

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item user text-start d-flex align-items-center"
                    id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user"
                        src="{{ asset('adminDashboard/assets/images/avatar-1.jpg') }}" alt="Header Avatar">
                    <span class="ms-2 d-none d-sm-block user-item-desc">
                        <span class="user-name">{{Auth::user()->name}}</span>
                        <span class="user-sub-title"><b>Role: </b>{{ Auth::user()->role }}</span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end pt-0">
                    <div class="p-3 bg-primary border-bottom">
                        <h6 class="mb-0 text-white">{{Auth::user()->name}}</h6>
                        <p class="mb-0 font-size-11 text-white-50 fw-semibold">{{Auth::user()->email}}</p>
                    </div>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                            class="mdi mdi-account-circle text-muted font-size-16 align-middle me-1"></i> <span
                            class="align-middle">Profile</span></a>
                    <!-- <a class="dropdown-item" href="apps-chat.html"><i
                            class="mdi mdi-message-text-outline text-muted font-size-16 align-middle me-1"></i>
                        <span class="align-middle">Messages</span></a> -->
                    <!-- <a class="dropdown-item" href="apps-kanban-board.html"><i
                            class="mdi mdi-calendar-check-outline text-muted font-size-16 align-middle me-1"></i>
                        <span class="align-middle">Taskboard</span></a> -->
                    <!-- <a class="dropdown-item" href="pages-faqs.html"><i
                            class="mdi mdi-lifebuoy text-muted font-size-16 align-middle me-1"></i> <span
                            class="align-middle">Help</span></a> -->
                    <div class="dropdown-divider"></div>
                    <!-- <a class="dropdown-item" href="pages-profile.html"><i
                            class="mdi mdi-wallet text-muted font-size-16 align-middle me-1"></i> <span
                            class="align-middle">Balance : <b>$6951.02</b></span></a> -->
                    <!-- <a class="dropdown-item d-flex align-items-center" href="user-settings.html"><i
                            class="mdi mdi-cog-outline text-muted font-size-16 align-middle me-1"></i> <span
                            class="align-middle">Settings</span><span
                            class="badge badge-success-subtle ms-auto">New</span></a> -->
                    <!-- <a class="dropdown-item" href="auth-lockscreen-basic.html"><i
                            class="mdi mdi-lock text-muted font-size-16 align-middle me-1"></i> <span
                            class="align-middle">Lock screen</span></a> -->
                    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                        @csrf
                    </form>

                    <a class="dropdown-item" href="javascript:void(0);"
                        onclick="document.getElementById('logout-form').submit();"><i
                            class="mdi mdi-logout text-muted font-size-16 align-middle me-1"></i><span
                            class="align-middle">Logout</span></a>

                    <!-- <a class="dropdown-item" href="auth-signout-basic.html"><i
                            class="mdi mdi-logout text-muted font-size-16 align-middle me-1"></i> <span
                            class="align-middle">Logout</span></a> -->
                </div>
            </div>
        </div>
    </div>
    <div class="topnav">
        <div class="container-fluid">
            <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

                <div class="collapse navbar-collapse" id="topnav-menu-content">
                    <ul class="navbar-nav">
                        @if(Auth::user()->status)
                        @if(Auth::user()->hasAdminPermission('dashboard', 'list'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('dashboard')}}"
                                id="topnav-dashboard" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="monitor"></i>
                                <span data-key="t-dashboards">Dashboard</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasAdminPermission('companies', 'list'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('companies.index')}}"
                                id="topnav-companies" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="briefcase"></i>
                                <span data-key="t-degrees">Company</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasAdminPermission('sites', 'list'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('sites.index')}}"
                                id="topnav-sites" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="grid"></i>
                                <span data-key="t-sites">Sites</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->role === 'SuperAdmin' || Auth::user()->role === 'Admin')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{ route('chronological-reports.index') }}">
                                <i class="icon nav-icon" data-feather="bar-chart-2"></i>
                                <span>System Reports</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasAdminPermission('reports-forms', 'list'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('reports.all')}}"
                                id="topnav-reports" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="file-text"></i>
                                <span data-key="t-reports">Reports & Forms</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasAdminPermission('shifts-reports', 'list'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('reports.index', ['type' => 'shifts'])}}"
                                id="topnav-run-sheets" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="icon nav-icon" data-feather="clock" style="width:16px; height:16px;"></i>
                                <span data-key="t-run-sheets">Shifts reports</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasAdminPermission('employees'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('employees.index')}}"
                                id="topnav-run-sheets" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="icon nav-icon" data-feather="users" style="width:16px; height:16px;"></i>
                                <span data-key="t-run-sheets">Employees</span>
                            </a>
                        </li>
                        @endif












                        @if(Auth::user()->hasAdminPermission('schedules', 'list'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{ route('schedules.index') }}">
                                <i class="icon nav-icon" data-feather="calendar"></i>
                                <span>Schedules</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasAdminPermission('attendance', 'list'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('attendance.index')}}"
                                id="topnav-attendance" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="check-circle"></i>
                                <span data-key="t-attendance">Attendance</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasAdminPermission('dispatches', 'list'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{ route('dispatches.index') }}">
                                <i class="icon nav-icon" data-feather="send"></i>
                                <span>Dispatch</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasAdminPermission('invoices', 'list') || Auth::user()->hasAdminPermission('products', 'list') || Auth::user()->hasAdminPermission('taxes', 'list') || Auth::user()->hasAdminPermission('customers', 'list'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-billing" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon nav-icon" data-feather="file-text"></i>
                                <span data-key="t-billing">Invoices</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-billing">
                                @if(Auth::user()->hasAdminPermission('invoices', 'list'))
                                <a href="{{ route('invoices.index') }}" class="dropdown-item" data-key="t-invoices-list">
                                    <i class="icon nav-icon" data-feather="file-text" style="width:16px; height:16px;"></i> Invoices
                                </a>
                                @endif
                                @if(Auth::user()->hasAdminPermission('customers', 'list'))
                                <a href="{{ route('customers.index') }}" class="dropdown-item" data-key="t-customers">
                                    <i class="icon nav-icon" data-feather="users" style="width:16px; height:16px;"></i> Customers
                                </a>
                                @endif
                                @if(Auth::user()->hasAdminPermission('products', 'list'))
                                <a href="{{ route('products.index') }}" class="dropdown-item" data-key="t-products">
                                    <i class="icon nav-icon" data-feather="box" style="width:16px; height:16px;"></i> Products
                                </a>
                                @endif
                                @if(Auth::user()->hasAdminPermission('taxes', 'list'))
                                <a href="{{ route('taxes.index') }}" class="dropdown-item" data-key="t-taxes">
                                    <i class="icon nav-icon" data-feather="percent" style="width:16px; height:16px;"></i> Tax
                                </a>
                                @endif
                            </div>
                        </li>
                        @endif




                        @if(Auth::user()->hasAdminPermission('site-tours', 'list'))
                        <!-- <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('sites.tours.all')}}"
                                id="topnav-tours" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="map"></i>
                                <span data-key="t-tours">Site Tours</span>
                            </a>
                        </li> -->
                        @endif
                        <!-- @if(Auth::user()->hasAdminPermission('nfc', 'list'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{ route('nfc.index') }}">
                                <i class="icon nav-icon" data-feather="rss"></i>
                                <span>NFC Tags</span>
                            </a>
                        </li>
                        @endif -->
                        
                        
                        <!-- <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('nfc.index')}}" id="topnav-nfc"
                                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon nav-icon" data-feather="rss"></i>
                                <span data-key="t-nfc">NFC Tags</span>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('schedules.index')}}"
                                id="topnav-schedules" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="calendar"></i>
                                <span data-key="t-schedules">Schedule</span>
                            </a>
                        </li> -->
                        {{-- Moved to dropdown --}}
                        <!-- <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('employees.index')}}"
                                id="topnav-employees" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="users"></i>
                                <span data-key="t-employees">Employees</span>
                            </a>
                        </li> -->
                        @if(Auth::user()->hasAdminPermission('time-clocks', 'list'))
                        <!-- <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('time-clocks.index')}}"
                                id="topnav-time-clocks" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="clock"></i>
                                <span data-key="t-time-clocks">Time Clock</span>
                            </a>
                        </li> -->
                        @endif
                        <!-- <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-forms" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon nav-icon" data-feather="file-text"></i>
                                <span data-key="t-forms">Forms</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-forms">
                                <a href="{{route('forms.assessments')}}" class="dropdown-item" data-key="t-assessments">
                                    <i class="icon nav-icon" data-feather="clipboard"
                                        style="width:16px; height:16px;"></i> Assessment
                                </a>
                                <a href="{{route('forms.daily-vehicle-checklist')}}" class="dropdown-item"
                                    data-key="t-daily-vehicle-checklist">
                                    <i class="icon nav-icon" data-feather="truck" style="width:16px; height:16px;"></i>
                                    Daily Vehicle Checklist
                                </a>
                            </div>
                        </li> -->
                        <!-- <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-security-reports" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon nav-icon" data-feather="file-text"></i>
                                <span data-key="t-reports">Security Reports</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-security-reports">
                                <a href="{{ route('security-reports.disciplinary') }}" class="dropdown-item" data-key="t-disciplinary">
                                    <i class="icon nav-icon" data-feather="user-x" style="width:16px; height:16px;"></i> Disciplinary Form
                                </a>
                                <a href="{{ route('security-reports.incident') }}" class="dropdown-item" data-key="t-incident">
                                    <i class="icon nav-icon" data-feather="alert-triangle" style="width:16px; height:16px;"></i> Incident Report
                                </a>
                                <a href="{{ route('security-reports.general') }}" class="dropdown-item" data-key="t-general">
                                    <i class="icon nav-icon" data-feather="file-text" style="width:16px; height:16px;"></i> General Report
                                </a>
                                <a href="{{ route('security-reports.daily-shift') }}" class="dropdown-item" data-key="t-daily-shift">
                                    <i class="icon nav-icon" data-feather="clock" style="width:16px; height:16px;"></i> Daily Shift Report
                                </a>
                            </div>
                        </li> -->
                        
                        <!-- <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('reports.index')}}"
                                id="topnav-reports" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="bar-chart"></i>
                                <span data-key="t-reports">Reports</span>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('numbers.index')}}"
                                id="topnav-numbers" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="phone"></i>
                                <span data-key="t-numbers">Numbers</span>
                            </a>
                        </li> -->
                        @endif
                        @if(collect(['sites', 'policies', 'orientations', 'pay-slips', 'tax-docs', 'management-reports', 'numbers', 'notice-board', 'post-esc', 'open-shifts', 'availabilities'])->contains(fn($module) => Auth::user()->hasAdminPermission($module, 'list')))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon nav-icon" data-feather="monitor"></i>
                                <span data-key="t-components">Profile</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-components">


                                @if(Auth::user()->hasAdminPermission('sites', 'list'))
                                <a href="{{route('weekly-run-sheets.index')}}" class="dropdown-item" data-key="t-widgets"> 
                                    <i class="icon nav-icon" data-feather="clipboard" style="width:16px; height:16px;"></i>
                                    Runsheets</a>
                                @endif

                                @if(Auth::user()->hasAdminPermission('policies'))
                                <a href="{{route('policies.index')}}" class="dropdown-item" data-key="t-widgets"> <i
                                        class="icon nav-icon" data-feather="file-text"
                                        style="width:16px; height:16px;"></i> Policies</a>
                                @endif
                                @if(Auth::user()->hasAdminPermission('orientations'))
                                <a href="{{route('orientations.index')}}" class="dropdown-item" data-key="t-widgets"> <i
                                        class="icon nav-icon" data-feather="layers"
                                        style="width:16px; height:16px;"></i> Orientations</a>
                                @endif
                                @if(Auth::user()->hasAdminPermission('pay-slips'))
                                <a href="{{route('pay-slips.index')}}" class="dropdown-item" data-key="t-widgets"><i
                                        class="icon nav-icon" data-feather="file-text"
                                        style="width:16px; height:16px;"></i> Pay Slips</a>
                                @endif
                                @if(Auth::user()->hasAdminPermission('tax-docs'))
                                <a href="{{route('tax-docs.index')}}" class="dropdown-item" data-key="t-widgets"><i
                                        class="icon nav-icon" data-feather="file-text"
                                        style="width:16px; height:16px;"></i> Tax Docs</a>
                                @endif
                                @if(Auth::user()->hasAdminPermission('management-reports', 'list'))
                                <a href="{{route('reports.index')}}" class="dropdown-item" data-key="t-widgets"><i
                                        class="icon nav-icon" data-feather="bar-chart"
                                        style="width:16px; height:16px;"></i> Reports</a>
                                @endif
                                @if(Auth::user()->hasAdminPermission('numbers'))
                                <a href="{{route('numbers.index')}}" class="dropdown-item" data-key="t-widgets"><i
                                        class="icon nav-icon" data-feather="phone" style="width:16px; height:16px;"></i>
                                    Numbers</a>
                                @endif
                                @if(Auth::user()->hasAdminPermission('notice-board'))
                                <a href="{{route('notice-board.index')}}" class="dropdown-item" data-key="t-widgets"><i
                                        class="icon nav-icon" data-feather="file-text" style="width:16px; height:16px;"></i>
                                    Notice Board</a>
                                @endif
                                @if(Auth::user()->hasAdminPermission('post-esc'))
                                <a href="{{route('post-esc.index')}}" class="dropdown-item" data-key="t-widgets"><i
                                        class="icon nav-icon" data-feather="file" style="width:16px; height:16px;"></i>
                                    Post &amp; ESC</a>
                                @endif

                                @if(Auth::user()->hasAdminPermission('open-shifts', 'list'))
                                <a href="{{route('open-shifts.index')}}" class="dropdown-item" data-key="t-widgets">
                                    <i class="icon nav-icon" data-feather="layers" style="width:16px; height:16px;"></i>
                                    Open Shifts
                                    @php $pendingOpenShiftClaims = \App\Models\OpenShiftClaim::where('status', 'pending')->count(); @endphp
                                    @if($pendingOpenShiftClaims > 0)
                                    <span class="badge bg-danger rounded-pill ms-1" style="font-size:.65rem;">{{ $pendingOpenShiftClaims }}</span>
                                    @endif
                                </a>
                                @endif

                                @if(Auth::user()->hasAdminPermission('availabilities', 'list'))
                                <a href="{{route('availabilities.index')}}" class="dropdown-item" data-key="t-widgets">
                                    <i class="icon nav-icon" data-feather="calendar" style="width:16px; height:16px;"></i>
                                    Availability
                                    @php $pendingAvailCount = \App\Models\Availability::where('status', 'pending')->count(); @endphp
                                    @if($pendingAvailCount > 0)
                                    <span class="badge bg-warning text-dark rounded-pill ms-1" style="font-size:.65rem;">{{ $pendingAvailCount }}</span>
                                    @endif
                                </a>
                                @endif
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </nav>
        </div>
    </div>

<script>
(function () {
    'use strict';

    // ── Notification Bell JS ─────────────────────────────────────────────
    const POLL_INTERVAL = 60000; // poll every 60s

    function timeAgo(dateStr) {
        const d = new Date(dateStr);
        const diff = Math.floor((Date.now() - d) / 1000);
        if (diff < 60)  return diff + 's ago';
        if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
        return Math.floor(diff / 86400) + 'd ago';
    }

    function renderNotifications(data) {
        const list      = document.getElementById('notificationList');
        const badge     = document.getElementById('notif-badge');
        const hdrCount  = document.getElementById('notif-header-count');
        const emptyEl   = document.getElementById('notif-empty');

        if (!list) return;

        const count = data.unread_count || 0;
        // Badge
        if (badge) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        }
        if (hdrCount) {
            hdrCount.textContent = count + ' unread';
            hdrCount.style.display = count > 0 ? 'inline-block' : 'none';
        }

        const notifications = data.notifications || [];
        if (notifications.length === 0) {
            if (emptyEl) emptyEl.style.display = 'block';
            list.querySelectorAll('.notif-item').forEach(el => el.remove());
            return;
        }
        if (emptyEl) emptyEl.style.display = 'none';

        // Rebuild list
        list.querySelectorAll('.notif-item').forEach(el => el.remove());
        notifications.forEach(function (n) {
            const isRead = !!n.read_at;
            const li = document.createElement('li');
            li.className = 'notif-item border-bottom';
            li.dataset.id = n.id;
            li.style.cssText = 'cursor:pointer;transition:background 0.15s;';
            if (!isRead) li.style.background = '#f0f4ff';
            li.innerHTML = `
                <div class="d-flex align-items-start px-3 py-2 gap-2">
                    <div style="flex-shrink:0;width:34px;height:34px;border-radius:50%;
                        background:${isRead ? '#e5e7eb' : '#4f46e5'};
                        display:flex;align-items:center;justify-content:center;margin-top:2px;">
                        <i class="fa fa-bell" style="font-size:14px;color:${isRead ? '#9ca3af' : '#fff'};"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:0.82rem;font-weight:${isRead ? '400' : '600'};color:#111827;
                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            ${n.title}
                        </div>
                        <div style="font-size:0.75rem;color:#6b7280;margin-top:2px;line-height:1.4;">
                            ${n.message}
                        </div>
                        <div style="font-size:0.68rem;color:#9ca3af;margin-top:4px;">
                            ${timeAgo(n.created_at)}
                            ${!isRead ? '<span class="badge bg-primary ms-1" style="font-size:0.55rem;">New</span>' : ''}
                        </div>
                    </div>
                </div>`;

            li.addEventListener('click', function () {
                if (!n.read_at) {
                    fetch('/notifications/' + n.id + '/read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Content-Type': 'application/json'
                        }
                    }).then(() => {
                        n.read_at = new Date().toISOString();
                        li.style.background = '';
                        li.querySelector('[style*="font-weight"]').style.fontWeight = '400';
                        fetchNotifications();
                    });
                }
            });

            list.appendChild(li);
        });
    }

    function fetchNotifications() {
        fetch('/notifications')
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) { if (data) renderNotifications(data); })
            .catch(function () {});
    }

    document.addEventListener('DOMContentLoaded', function () {
        fetchNotifications();
        setInterval(fetchNotifications, POLL_INTERVAL);

        // ── Manual bell toggle (fully responsive) ──────────────────────
        const bellBtn      = document.getElementById('notif-bell-btn');
        const notifDropdown = document.getElementById('notif-dropdown');

        function positionDropdown() {
            const vw = window.innerWidth;
            const dropW = Math.min(360, vw - 20);
            notifDropdown.style.width = dropW + 'px';
            notifDropdown.style.right = '10px';
            notifDropdown.style.left  = 'auto';
            // position just below the header bar
            const btnRect = bellBtn.getBoundingClientRect();
            notifDropdown.style.top = (btnRect.bottom + 6) + 'px';
        }

        if (bellBtn && notifDropdown) {
            bellBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                const isVisible = notifDropdown.style.display === 'block';
                if (isVisible) {
                    notifDropdown.style.display = 'none';
                } else {
                    positionDropdown();
                    notifDropdown.style.display = 'block';
                }
            });

            // close when clicking outside
            document.addEventListener('click', function (e) {
                if (!notifDropdown.contains(e.target) && e.target !== bellBtn) {
                    notifDropdown.style.display = 'none';
                }
            });

            // reposition on resize
            window.addEventListener('resize', function () {
                if (notifDropdown.style.display === 'block') positionDropdown();
            });
        }

        // ── Mark all read ──────────────────────────────────────────────
        const markAllBtn = document.getElementById('notif-mark-all-read');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Content-Type': 'application/json'
                    }
                }).then(() => fetchNotifications());
            });
        }
    });
    // ────────────────────────────────────────────────────────────────────
})();
</script>

</header>
