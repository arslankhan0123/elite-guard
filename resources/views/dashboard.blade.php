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
        background: #0f172a;
        background-image: radial-gradient(at 0% 0%, rgba(139, 92, 246, 0.15) 0, transparent 50%), 
                          radial-gradient(at 50% 0%, rgba(59, 130, 246, 0.1) 0, transparent 50%);
        border-radius: 30px;
        padding: 50px;
        color: white;
        margin-bottom: 40px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
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
                <i data-feather="shield" style="width: 250px; height: 250px; stroke: #8b5cf6; stroke-width: 1; opacity: 0.5;"></i>
            </div>
        </div>
    </div>

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

    <!-- Secondary Info Grid -->
    <div class="row g-4 mt-4 mb-5">
        <div class="col-lg-6">
            <div class="info-card p-5 h-100 shadow-sm">
                <div class="icon-circle bg-soft-primary text-primary">
                    <i data-feather="file-text"></i>
                </div>
                <h4 class="fw-bold text-dark">Developer documentation</h4>
                <p class="text-muted fs-5">Get started with our NFC hardware integration guide. Perfect for setting up your Flutter mobile application and writing tags.</p>
                <a href="{{ asset('docs/NFC_INTEGRATION.md') }}" class="btn btn-primary rounded-pill px-4 fw-bold mt-2">Open Guide</a>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="info-card p-5 h-100 shadow-sm" style="background: linear-gradient(to bottom right, #ffffff, #f8fafc);">
                <div class="icon-circle bg-soft-success text-success">
                    <i data-feather="activity"></i>
                </div>
                <h4 class="fw-bold text-dark">System Health</h4>
                <div class="d-flex align-items-center gap-3 mt-4">
                    <div class="p-3 bg-light rounded-4 text-center flex-grow-1">
                        <div class="text-muted small fw-bold mb-1">COMPANIES</div>
                        <div class="h5 fw-bold text-primary mb-0">{{ $companyCount }}</div>
                    </div>
                    <div class="p-3 bg-light rounded-4 text-center flex-grow-1">
                        <div class="text-muted small fw-bold mb-1">SITES</div>
                        <div class="h5 fw-bold text-success mb-0">{{ $siteCount }}</div>
                    </div>
                    <div class="p-3 bg-light rounded-4 text-center flex-grow-1">
                        <div class="text-muted small fw-bold mb-1">CHECKPOINTS</div>
                        <div class="h5 fw-bold text-info mb-0">{{ $nfcCount }}</div>
                    </div>
                    <div class="p-3 bg-light rounded-4 text-center flex-grow-1">
                        <div class="text-muted small fw-bold mb-1">TEAM</div>
                        <div class="h5 fw-bold text-warning mb-0">{{ $employeeCount }}</div>
                    </div>
                </div>
                <div class="mt-4 p-3 rounded-4 bg-primary-subtle border border-primary-subtle text-primary small d-flex align-items-center gap-2">
                    <i data-feather="check-circle" style="width: 16px;"></i> All systems are operational
                </div>
            </div>
        </div>
    </div>
</div>
@endsection