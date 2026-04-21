<style>
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
                <a href="{{route('dashboard')}}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{asset('logo.png')}}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('logo.png')}}" alt="" height="42">
                    </span>
                </a>

                <a href="{{route('dashboard')}}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{asset('logo.png')}}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('logo.png')}}" alt="" height="22">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 d-lg-none header-item" data-bs-toggle="collapse"
                data-bs-target="#topnav-menu-content">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <div class="d-flex">

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item user text-start d-flex align-items-center"
                    id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user"
                        src="{{ asset('adminDashboard/assets/images/avatar-1.jpg') }}" alt="Header Avatar">
                    <span class="ms-2 d-none d-sm-block user-item-desc">
                        <span class="user-name">{{Auth::user()->name}}</span>
                        <span class="user-sub-title"><b>Role: </b>Admin</span>
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
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('dashboard')}}"
                                id="topnav-dashboard" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="monitor"></i>
                                <span data-key="t-dashboards">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('companies.index')}}"
                                id="topnav-companies" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="briefcase"></i>
                                <span data-key="t-degrees">Company</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('sites.index')}}"
                                id="topnav-sites" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="grid"></i>
                                <span data-key="t-sites">Sites</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('nfc.index')}}" id="topnav-nfc"
                                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon nav-icon" data-feather="rss"></i>
                                <span data-key="t-nfc">NFC Tags</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('schedules.index')}}"
                                id="topnav-schedules" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="calendar"></i>
                                <span data-key="t-schedules">Schedule</span>
                            </a>
                        </li>
                        <!-- <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('employees.index')}}"
                                id="topnav-employees" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="users"></i>
                                <span data-key="t-employees">Employees</span>
                            </a>
                        </li> -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{route('time-clocks.index')}}"
                                id="topnav-time-clocks" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon nav-icon" data-feather="clock"></i>
                                <span data-key="t-time-clocks">Time Clock</span>
                            </a>
                        </li>
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
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon nav-icon" data-feather="monitor"></i>
                                <span data-key="t-components">Profile</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-components">
                                <a href="{{route('policies.index')}}" class="dropdown-item" data-key="t-widgets"> <i class="icon nav-icon" data-feather="file-text" style="width:16px; height:16px;"></i> Policies</a>
                                <a href="{{route('orientations.index')}}" class="dropdown-item" data-key="t-widgets"> <i class="icon nav-icon" data-feather="layers" style="width:16px; height:16px;"></i> Orientations</a>
                                <a href="{{route('employees.index')}}" class="dropdown-item" data-key="t-widgets"><i class="icon nav-icon" data-feather="users" style="width:16px; height:16px;"></i> Employees</a>
                                <a href="{{route('pay-slips.index')}}" class="dropdown-item" data-key="t-widgets"><i class="icon nav-icon" data-feather="file-text" style="width:16px; height:16px;"></i> Pay Slips</a>
                                <a href="{{route('tax-docs.index')}}" class="dropdown-item" data-key="t-widgets"><i class="icon nav-icon" data-feather="file-text" style="width:16px; height:16px;"></i> Tax Docs</a>
                                <a href="{{route('reports.index')}}" class="dropdown-item" data-key="t-widgets"><i class="icon nav-icon" data-feather="bar-chart" style="width:16px; height:16px;"></i> Reports</a>
                                <a href="{{route('numbers.index')}}" class="dropdown-item" data-key="t-widgets"><i class="icon nav-icon" data-feather="phone" style="width:16px; height:16px;"></i> Numbers</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</header>