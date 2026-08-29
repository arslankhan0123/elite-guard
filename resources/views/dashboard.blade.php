@extends('dashboardLayouts.main')
@section('title', 'Admin Dashboard')

@section('breadcrumbTitle', 'Dashboard Overview')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Operational Hub</li>
@endsection

@section('content')
<style>
    /* Premium Colorful Design System */
    :root {
        --dash-purple: #8b5cf6;
        --dash-blue: #3b82f6;
        --dash-emerald: #10b981;
        --dash-rose: #f43f5e;
        --dash-amber: #f59e0b;
        --dash-cyan: #06b6d4;
    }

    /* Circular Progress Bar */
    .radial-progress {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: conic-gradient(var(--dash-emerald) calc(var(--progress-percent) * 1%), #e2e8f0 0);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .radial-progress-inner {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 750;
        color: #1e293b;
    }

    /* High-contrast readable badges */
    .badge.bg-success-subtle {
        background-color: rgba(16, 185, 129, 0.12) !important;
        color: #065f46 !important;
        border: 1px solid rgba(16, 185, 129, 0.3) !important;
    }
    .badge.bg-danger-subtle {
        background-color: rgba(244, 63, 94, 0.12) !important;
        color: #991b1b !important;
        border: 1px solid rgba(244, 63, 94, 0.3) !important;
    }
    .badge.bg-warning-subtle {
        background-color: rgba(245, 158, 11, 0.12) !important;
        color: #92400e !important;
        border: 1px solid rgba(245, 158, 11, 0.3) !important;
    }

    .p-wrapper {
        padding: 5px 0px;
    }

    /* Vibrant Gradient Cards */
    .vibrant-card {
        border: none;
        border-radius: 24px;
        color: white;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        position: relative;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        min-height: 180px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .vibrant-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.2);
    }

    .vibrant-card::before {
        content: '';
        position: absolute;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        top: -50px;
        right: -50px;
        transition: all 0.4s;
    }

    .vibrant-card:hover::before {
        transform: scale(1.5);
        background: rgba(255, 255, 255, 0.15);
    }

    .card-company { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); }
    .card-site { background: linear-gradient(135deg, #059669 0%, #10b981 100%); }
    .card-nfc { background: linear-gradient(135deg, #0284c7 0%, #06b6d4 100%); }
    .card-employee { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }

    .stat-icon {
        font-size: 3rem;
        position: absolute;
        bottom: 10px;
        right: 15px;
        opacity: 0.2;
        transition: all 0.4s;
        transform: rotate(-15deg);
    }

    .vibrant-card:hover .stat-icon {
        transform: rotate(0deg) scale(1.2);
        opacity: 0.4;
    }

    .stat-value {
        font-size: 3rem;
        font-weight: 800;
        margin: 0;
        line-height: 1;
    }

    .stat-label {
        font-size: 1.1rem;
        font-weight: 600;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Welcome Hero section */
    .hero-section {
        background: linear-gradient(-45deg, #0b0f19, #1e1b4b, #2e1065, #0b0f19);
        background-size: 400% 400%;
        animation: gradientBG 12s ease infinite;
        border-radius: 30px;
        padding: 60px 50px;
        color: white;
        margin-bottom: 40px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        position: relative;
        overflow: hidden;
    }

    @keyframes gradientBG {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Floating Logo Animation */
    .hero-logo-img {
        max-height: 180px;
        filter: drop-shadow(0 0 20px rgba(139, 92, 246, 0.4));
        animation: floatLogo 5s ease-in-out infinite;
    }

    @keyframes floatLogo {
        0% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(1.5deg); }
        100% { transform: translateY(0px) rotate(0deg); }
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 850;
        letter-spacing: -2px;
        background: linear-gradient(to right, #fff, #94a3b8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .action-btn {
        padding: 12px 30px;
        border-radius: 15px;
        font-weight: 700;
        transition: all 0.3s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-create-company {
        background: var(--dash-purple);
        color: white;
    }

    .btn-create-nfc {
        background: rgba(255,255,255,0.1);
        color: white;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.1);
    }

    .btn-create-nfc:hover {
        background: white;
        color: #0f172a;
    }

    /* Info Cards */
    .info-card {
        background: white;
        border-radius: 24px;
        border: 1px solid #f1f5f9;
        transition: all 0.3s;
    }

    .info-card:hover {
        border-color: var(--dash-purple);
        box-shadow: 0 10px 30px -10px rgba(139, 92, 246, 0.1);
    }

    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }

</style>

<div class="p-wrapper container-fluid" style="max-width: 100%;">
    <!-- Hero Section -->
    <div class="hero-section animate__animated animate__fadeIn">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-soft-info text-info rounded-pill px-3 py-2 mb-3 fw-bold">ELITE GUARD OS</span>
                <h1 class="hero-title">Elite <span style="background: linear-gradient(to right, #8b5cf6, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Security</span> Dashboard</h1>
                <p class="text-white-50 fs-5 mb-4 mt-2">Manage your global security network, patrol sites, and NFC infrastructure with real-time precision.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('companies.create') }}" class="action-btn btn-create-company shadow-lg">
                        <i data-feather="plus-circle"></i> Add New Company
                    </a>
                    <a href="{{ route('nfc.create') }}" class="action-btn btn-create-nfc">
                        <i data-feather="rss"></i> Generate NFC Tag
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block">
                <img src="{{ asset('logo.png') }}" alt="Elite Guard Logo" class="hero-logo-img img-fluid">
            </div>
        </div>
    </div>

    {{-- Commented out Stats Grid
    <!-- Stats Grid -->
    <div class="row g-4 mt-2">
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('companies.index') }}" class="text-decoration-none">
                <div class="vibrant-card card-company p-4">
                    <div class="stat-label">Total Organizations</div>
                    <div class="stat-value">{{ $companyCount }}</div>
                    <div class="stat-icon"><i data-feather="briefcase"></i></div>
                    <div class="mt-3 fs-6 d-flex align-items-center gap-1 opacity-75">
                        Manage Companies <i data-feather="arrow-right" style="width: 16px;"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('sites.index') }}" class="text-decoration-none">
                <div class="vibrant-card card-site p-4">
                    <div class="stat-label">Tactical Print Sites</div>
                    <div class="stat-value">{{ $siteCount }}</div>
                    <div class="stat-icon"><i data-feather="map-pin"></i></div>
                    <div class="mt-3 fs-6 d-flex align-items-center gap-1 opacity-75">
                        View Patrol Sites <i data-feather="arrow-right" style="width: 16px;"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('nfc.index') }}" class="text-decoration-none">
                <div class="vibrant-card card-nfc p-4">
                    <div class="stat-label">NFC Device Tags</div>
                    <div class="stat-value">{{ $nfcCount }}</div>
                    <div class="stat-icon"><i data-feather="rss"></i></div>
                    <div class="mt-3 fs-6 d-flex align-items-center gap-1 opacity-75">
                        Manage Checkpoints <i data-feather="arrow-right" style="width: 16px;"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('employees.index') }}" class="text-decoration-none">
                <div class="vibrant-card card-employee p-4">
                    <div class="stat-label">Team Employees</div>
                    <div class="stat-value">{{ $employeeCount }}</div>
                    <div class="stat-icon"><i data-feather="users"></i></div>
                    <div class="mt-3 fs-6 d-flex align-items-center gap-1 opacity-75">
                        Directory <i data-feather="arrow-right" style="width: 16px;"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>
    --}}

    <!-- Real-time Activity Logs Section -->
    <div class="row g-4 mt-2">
        <!-- Live Attendance Tracker Card -->
        <div class="col-xl-6 col-12">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold text-dark mb-0">
                        <i data-feather="activity" class="text-primary me-2"></i> Live Shift Attendance
                    </h5>
                    <span class="badge bg-soft-success text-success rounded-pill px-3 py-1 fw-bold align-items-center gap-1 d-flex" style="font-size: 0.75rem;">
                        <span class="spinner-grow spinner-grow-sm text-success" role="status" aria-hidden="true"></span> Realtime
                    </span>
                </div>
                <div class="card-body px-4 pb-4">
                    <div id="attendance-toast" class="alert alert-success py-2 px-3 mb-3 small d-none align-items-center justify-content-between rounded-3 border-0 shadow-sm" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <span><i data-feather="check-circle" class="me-1" style="width: 14px; height: 14px;"></i> Attendance fetched successfully</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="live-attendance-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Guard</th>
                                    <th>Site</th>
                                    <th>Action</th>
                                    <th>Time</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Loading active sessions...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Tours Tracker Card -->
        <div class="col-xl-6 col-12">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold text-dark mb-0">
                        <i data-feather="navigation" class="text-info me-2"></i> Recent Site Tour Scans
                    </h5>
                    <span class="badge bg-soft-success text-success rounded-pill px-3 py-1 fw-bold align-items-center gap-1 d-flex" style="font-size: 0.75rem;">
                        <span class="spinner-grow spinner-grow-sm text-success" role="status" aria-hidden="true"></span> Realtime
                    </span>
                </div>
                <div class="card-body px-4 pb-4">
                    <div id="tours-toast" class="alert alert-success py-2 px-3 mb-3 small d-none align-items-center justify-content-between rounded-3 border-0 shadow-sm" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <span><i data-feather="check-circle" class="me-1" style="width: 14px; height: 14px;"></i> Tours fetched successfully</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="live-tours-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Tour Name</th>
                                    <th>Site</th>
                                    <th>Guard</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Loading tour logs...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Commented out extra widget cards
    <!-- Attendance Section -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <a href="{{ route('attendance.index') }}" class="text-decoration-none">
                <div class="vibrant-card p-4" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); min-height: 120px;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label text-white">Live Operations</div>
                            <h2 class="fw-bold text-white mb-1">Shift Attendance</h2>
                            <p class="text-white-50 mb-0">Monitor real-time clock-in/out activity across all sites.</p>
                        </div>
                        <div class="text-center">
                            <div class="stat-value text-white">{{ $todayAttendanceCount }}</div>
                            <div class="small fw-bold text-white opacity-75">TODAY</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Open Shift Management Section -->
    <div class="row g-4 mt-2">
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('open-shifts.claims') }}" class="text-decoration-none">
                <div class="vibrant-card p-4" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="stat-label">Pending Claims</div>
                    <div class="stat-value">{{ $pendingOpenShiftClaimsCount }}</div>
                    <div class="stat-icon"><i data-feather="bell"></i></div>
                    <div class="mt-3 fs-6 d-flex align-items-center gap-1 opacity-75">
                        Review Claim Requests <i data-feather="arrow-right" style="width: 16px;"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('open-shifts.index') }}" class="text-decoration-none">
                <div class="vibrant-card p-4" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
                    <div class="stat-label">Active Open Shifts</div>
                    <div class="stat-value">{{ \App\Models\OpenShift::where('status', 'open')->count() }}</div>
                    <div class="stat-icon"><i data-feather="layers"></i></div>
                    <div class="mt-3 fs-6 d-flex align-items-center gap-1 opacity-75">
                        Manage Open Shifts <i data-feather="arrow-right" style="width: 16px;"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-4 col-md-12">
            <a href="{{ route('open-shifts.create') }}" class="text-decoration-none">
                <div class="vibrant-card p-4" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); min-height: 100px;">
                    <div class="d-flex align-items-center justify-content-between h-100">
                        <div>
                            <div class="stat-label mb-1">Quick Action</div>
                            <h3 class="fw-bold text-white mb-0">Post New Shift</h3>
                        </div>
                        <div class="bg-white bg-opacity-20 p-3 rounded-circle">
                            <i data-feather="plus-circle" style="width: 40px; height: 40px;"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Availability Section -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <a href="{{ route('availabilities.index') }}" class="text-decoration-none">
                <div class="vibrant-card p-4" style="background: linear-gradient(135deg, #0f172a 0%, #334155 100%); min-height: 120px;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label text-info">Resource Planning</div>
                            <h2 class="fw-bold text-white mb-1">Employee Availability</h2>
                            <p class="text-white-50 mb-0">Review {{ $pendingAvailCount }} pending availability submissions from your team.</p>
                        </div>
                        <div class="text-center">
                            <div class="stat-value text-info">{{ $pendingAvailCount }}</div>
                            <div class="small fw-bold text-info opacity-75">PENDING</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    --}}

    <div class="row g-3 mt-3 mb-4">
        <div class="col-lg-12">
            <div class="info-card p-4 h-100 shadow-sm" style="background: linear-gradient(to bottom right, #ffffff, #f8fafc);">
                <h5 class="fw-bold text-dark d-flex align-items-center gap-2 mb-3">
                    <i data-feather="activity" class="text-success" style="width: 20px; height: 20px;"></i>
                    System Health
                </h5>
                <div class="row g-3 mt-1">
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="p-2 bg-light rounded-3 text-center">
                            <div class="text-muted small fw-bold mb-1">COMPANIES</div>
                            <div class="h5 fw-bold text-primary mb-0">{{ $companyCount }}</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="p-2 bg-light rounded-3 text-center">
                            <div class="text-muted small fw-bold mb-1">SITES</div>
                            <div class="h5 fw-bold text-success mb-0">{{ $siteCount }}</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="p-2 bg-light rounded-3 text-center">
                            <div class="text-muted small fw-bold mb-1">CHECKPOINTS</div>
                            <div class="h5 fw-bold text-info mb-0">{{ $nfcCount }}</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="p-2 bg-light rounded-3 text-center">
                            <div class="text-muted small fw-bold mb-1">EMPLOYEES</div>
                            <div class="h5 fw-bold text-warning mb-0">{{ $employeeCount }}</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="p-2 bg-light rounded-3 text-center">
                            <div class="text-muted small fw-bold mb-1">DISPATCH</div>
                            <div class="h5 fw-bold text-danger mb-0">{{ $dispatchCount }}</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="p-2 bg-light rounded-3 text-center">
                            <div class="text-muted small fw-bold mb-1">OPEN SHIFTS</div>
                            <div class="h5 fw-bold text-dark mb-0">{{ $openShiftCount }}</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="p-2 bg-light rounded-3 text-center">
                            <div class="text-muted small fw-bold mb-1">AVAILABILITY</div>
                            <div class="h5 fw-bold text-primary mb-0">{{ $availabilityCount }}</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="p-2 bg-light rounded-3 text-center">
                            <div class="text-muted small fw-bold mb-1">RUNSHEETS</div>
                            <div class="h5 fw-bold text-success mb-0">{{ $runsheetCount }}</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="p-2 bg-light rounded-3 text-center">
                            <div class="text-muted small fw-bold mb-1">POLICIES</div>
                            <div class="h5 fw-bold text-info mb-0">{{ $policyCount }}</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="p-2 bg-light rounded-3 text-center">
                            <div class="text-muted small fw-bold mb-1">ORIENTATIONS</div>
                            <div class="h5 fw-bold text-warning mb-0">{{ $orientationCount }}</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="p-2 bg-light rounded-3 text-center">
                            <div class="text-muted small fw-bold mb-1">NOTICE BOARD</div>
                            <div class="h5 fw-bold text-danger mb-0">{{ $noticeBoardCount }}</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="p-2 bg-light rounded-3 text-center">
                            <div class="text-muted small fw-bold mb-1">POST & ESC</div>
                            <div class="h5 fw-bold text-dark mb-0">{{ $postEscCount }}</div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 p-3 rounded-4 bg-primary-subtle border border-primary-subtle text-primary small d-flex align-items-center gap-2">
                    <i data-feather="check-circle" style="width: 16px;"></i> All systems are operational
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let isFirstLoad = true;

        function fetchLiveDashboardData() {
            fetch("{{ route('dashboard.live-data') }}")
                .then(response => response.json())
                .then(data => {
                    // 1. Render Attendances
                    const attBody = document.querySelector('#live-attendance-table tbody');
                    if (data.attendances && data.attendances.length > 0) {
                        let attHtml = '';
                        data.attendances.forEach(att => {
                            const badgeClass = att.type === 'Checked Out' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle';
                            attHtml += `
                                <tr>
                                    <td class="fw-semibold text-dark">${att.user_name}</td>
                                    <td class="text-secondary">${att.site_name}</td>
                                    <td><span class="badge ${badgeClass} rounded-pill px-2 py-1 fw-semibold">${att.type}</span></td>
                                    <td class="fw-bold text-dark">${att.time}</td>
                                    <td class="text-muted small">${att.date}</td>
                                </tr>
                            `;
                        });
                        attBody.innerHTML = attHtml;
                    } else {
                        attBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">No attendance logs found today.</td></tr>`;
                    }

                    // 2. Render Tours
                    const tourBody = document.querySelector('#live-tours-table tbody');
                    if (data.tours && data.tours.length > 0) {
                        let tourHtml = '';
                        data.tours.forEach(tour => {
                            let statusBadge = 'bg-warning-subtle text-warning border border-warning-subtle';
                            if (tour.status === 'Completed') {
                                statusBadge = 'bg-success-subtle text-success border border-success-subtle';
                            } else if (tour.status === 'Missed') {
                                statusBadge = 'bg-danger-subtle text-danger border border-danger-subtle';
                            }

                            const percent = tour.required_count > 0 ? ((tour.scanned_count / tour.required_count) * 100) : 0;
                            tourHtml += `
                                <tr>
                                    <td class="fw-semibold text-dark">${tour.tour_name}</td>
                                    <td class="text-secondary">${tour.site_name}</td>
                                    <td class="text-secondary">${tour.user_name}</td>
                                    <td>
                                        <div class="radial-progress" style="--progress-percent: ${percent};">
                                            <div class="radial-progress-inner">
                                                ${tour.progress}
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge ${statusBadge} rounded-pill px-2 py-1 fw-semibold">${tour.status}</span></td>
                                </tr>
                            `;
                        });
                        tourBody.innerHTML = tourHtml;
                    } else {
                        tourBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">No recent site tours found.</td></tr>`;
                    }
                    
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }

                    // Show temporary toast alerts on data refresh (skip first load)
                    if (!isFirstLoad) {
                        const attToast = document.getElementById('attendance-toast');
                        const toursToast = document.getElementById('tours-toast');

                        attToast.classList.remove('d-none');
                        attToast.classList.add('d-flex');
                        toursToast.classList.remove('d-none');
                        toursToast.classList.add('d-flex');

                        if (typeof feather !== 'undefined') {
                            feather.replace();
                        }

                        setTimeout(() => {
                            attToast.classList.remove('d-flex');
                            attToast.classList.add('d-none');
                            toursToast.classList.remove('d-flex');
                            toursToast.classList.add('d-none');
                        }, 5000);
                    }

                    isFirstLoad = false;
                })
                .catch(error => console.error("Error fetching live dashboard data:", error));
        }

        // Fetch immediately and poll every 30 seconds (30000ms)
        fetchLiveDashboardData();
        setInterval(fetchLiveDashboardData, 30000);
    });
</script>
@endsection