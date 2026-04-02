@extends('dashboardLayouts.main')
@section('title', 'Dashboard')

@section('breadcrumbTitle', 'Dashboard')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<!-- <li class="breadcrumb-item"><a href="">Home</a></li> -->
<li class="breadcrumb-item active">Home</li>
@endsection

@section('content')
<!-- Google Fonts: Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    /* Premium Design System - Refined */
    :root {
        --p-primary: #7c3aed; /* Brand Violet */
        --p-primary-soft: #a78bfa;
        --p-primary-light: rgba(124, 58, 237, 0.08);
        --p-navy: #1e1b4b; /* Brand Navy */
        --p-slate-700: #334155;
        --p-slate-600: #475569;
        --p-slate-500: #64748b;
        --p-slate-400: #94a3b8;
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-border: rgba(226, 232, 240, 0.8);
        --p-shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
        --p-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        --p-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -4px rgba(0, 0, 0, 0.04);
        --p-glow-violet: rgba(124, 58, 237, 0.5);
    }

    /* Professional Noise Texture Overlay */
    .p-dashboard-wrapper::before {
        content: "";
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        opacity: 0.015;
        z-index: 0;
        pointer-events: none;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
    }

    .p-dashboard-wrapper {
        padding: 30px;
        background-color: #f8fafc;
        min-height: 100vh;
        font-family: 'Inter', -apple-system, sans-serif;
        color: var(--p-slate-700);
    }

    /* Hero Banner */
    .p-hero-banner {
        background: linear-gradient(135deg, var(--p-navy) 0%, #4338ca 100%);
        border-radius: 24px;
        padding: 48px;
        color: white;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--p-shadow-lg);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .p-hero-banner::before {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background: radial-gradient(circle at top right, rgba(124, 58, 237, 0.2), transparent 60%);
        pointer-events: none;
    }

    .p-hero-banner h1 {
        font-weight: 600;
        letter-spacing: -0.02em;
        margin-bottom: 12px;
        font-size: 2.75rem;
    }

    .p-hero-banner h1 span {
        color: #ddd6fe; /* Soft violet/lavender */
    }

    .p-hero-banner p {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1.15rem;
        max-width: 550px;
        font-weight: 400;
        line-height: 1.6;
    }

    /* Stat Cards - Dark Premium Theme */
    .p-stat-card {
        background: linear-gradient(145deg, #111827, #1f2937); /* Refined gradient */
        border: 1px solid rgba(124, 58, 237, 0.15);
        border-radius: 20px;
        padding: 32px 24px;
        height: 100%;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), 
                    box-shadow 0.4s ease, border-color 0.4s ease, background 0.4s ease;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        text-decoration: none !important;
        position: relative;
        overflow: hidden;
        animation: p-float-complex 3s ease-in-out infinite, p-glow-pulse 3s ease-in-out infinite;
        z-index: 1;
        perspective: 1000px;
    }

    /* Futuristic Light Sweep Effect */
    .p-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.05),
            rgba(255, 255, 255, 0.1),
            rgba(255, 255, 255, 0.05),
            transparent
        );
        transform: skewX(-25deg);
        z-index: 1;
        animation: light-sweep 4s infinite linear;
    }

    .p-stat-card::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background: radial-gradient(circle at top right, rgba(124, 58, 237, 0.1), transparent 70%);
        pointer-events: none;
    }

    .p-stat-card:hover {
        transform: translateY(-8px) scale(1.02) rotateX(4deg) rotateY(4deg); /* Ultimate 3D Tilt */
        box-shadow: 0 25px 50px -12px rgba(124, 58, 237, 0.35);
        border-color: var(--p-primary-soft);
        background: #1e1b4b;
    }

    .p-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 24px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
        animation: icon-heartbeat 4s ease-in-out infinite alternate;
    }

    @keyframes icon-heartbeat {
        0%, 100% { transform: scale(1); opacity: 0.9; }
        50% { transform: scale(1.1); opacity: 1; filter: brightness(1.2); }
    }

    .p-stat-value {
        font-size: 2.25rem;
        font-weight: 700;
        color: #ffffff; /* High contrast white */
        margin-bottom: 6px;
        display: block;
        letter-spacing: -0.02em;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .p-stat-label {
        font-size: 0.95rem;
        font-weight: 500;
        color: #94a3b8; /* Slate 400 */
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }

    /* Advanced Animation Keyframes */
    @keyframes p-float-complex {
        0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
        33% { transform: translateY(-8px) rotate(0.5deg) scale(1.01); }
        66% { transform: translateY(-4px) rotate(-0.5deg) scale(1); }
    }

    @keyframes p-glow-pulse {
        0%, 100% { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2); }
        50% { box-shadow: 0 15px 30px -5px rgba(124, 58, 237, 0.4), 0 8px 12px -3px rgba(124, 58, 237, 0.2); border-color: rgba(124, 58, 237, 0.5); }
    }

    @keyframes light-sweep {
        0% { left: -100%; opacity: 0; }
        10% { opacity: 1; }
        30% { left: 150%; opacity: 0; }
        100% { left: 150%; opacity: 0; }
    }

    /* Staggered Floating Delays - Dynamic Rhythm */
    .p-animate:nth-child(1) .p-stat-card { animation-delay: 0s, 0s; }
    .p-animate:nth-child(1) .p-stat-card::before { animation-delay: 0s; }
    
    .p-animate:nth-child(2) .p-stat-card { animation-delay: 0.5s, 0.5s; }
    .p-animate:nth-child(2) .p-stat-card::before { animation-delay: 1s; }
    
    .p-animate:nth-child(3) .p-stat-card { animation-delay: 1s, 1s; }
    .p-animate:nth-child(3) .p-stat-card::before { animation-delay: 2s; }
    
    .p-animate:nth-child(4) .p-stat-card { animation-delay: 1.5s, 1.5s; }
    .p-animate:nth-child(4) .p-stat-card::before { animation-delay: 3s; }

    /* Refined soft colors for dark theme */
    .bg-soft-indigo { background: rgba(79, 70, 229, 0.2); color: #818cf8; }
    .bg-soft-emerald { background: rgba(16, 185, 129, 0.2); color: #34d399; }
    .bg-soft-rose { background: rgba(244, 63, 94, 0.2); color: #fb7185; }
    .bg-soft-amber { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
    .bg-soft-cyan { background: rgba(6, 182, 212, 0.2); color: #22d3ee; }
    .bg-soft-violet { background: rgba(124, 58, 237, 0.2); color: #a78bfa; }
    .bg-soft-slate { background: rgba(71, 85, 105, 0.2); color: #94a3b8; }

    /* Section Headers */
    .p-section-header {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--p-navy);
        margin: 48px 0 24px 0;
        display: flex;
        align-items: center;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .p-section-header::after {
        content: '';
        flex: 1;
        height: 5px;
        background: linear-gradient(90deg, #475569, transparent);
        margin-left: 20px;
        opacity: 0.4;
        border-radius: 2.5px;
    }

    .p-section-header i {
        margin-right: 12px;
        font-size: 0.9em;
        color: var(--p-primary);
        opacity: 0.7;
    }

    /* Filters Section - Refined */
    .p-filter-panel {
        background: white;
        border-radius: 24px;
        padding: 32px;
        margin-bottom: 40px;
        border: 1px solid var(--glass-border);
        box-shadow: var(--p-shadow-lg);
        display: none;
    }

    .p-btn-filter {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.3s;
    }

    .p-btn-filter:hover {
        background: white;
        color: var(--p-navy);
        transform: translateY(-2px);
    }

    /* Animations */
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes fadeInUp { 
        from { opacity: 0; transform: translateY(20px); } 
        to { opacity: 1; transform: translateY(0); } 
    }
    @keyframes slideDown { 
        from { opacity: 0; transform: translateY(-20px); } 
        to { opacity: 1; transform: translateY(0); } 
    }

    .p-animate {
        animation: fadeInUp 0.6s ease forwards;
        opacity: 0;
    }

    /* Category Badges for Filter */
    .p-cat-badge {
        cursor: pointer;
        padding: 10px 20px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        font-weight: 500;
        font-size: 0.85rem;
        transition: all 0.2s;
        user-select: none;
        color: var(--p-slate-600);
    }

    .p-cat-badge:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .p-btn-check:checked + .p-cat-badge {
        background: var(--p-primary);
        color: white;
        border-color: var(--p-primary);
        box-shadow: 0 4px 6px -1px rgba(124, 58, 237, 0.2);
    }

    .btn-primary {
        background-color: var(--p-primary);
        border-color: var(--p-primary);
        font-weight: 600;
        letter-spacing: 0.02em;
        padding-top: 10px;
        padding-bottom: 10px;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background-color: #6d28d9;
        border-color: #6d28d9;
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(109, 40, 217, 0.2);
    }
</style>

<div class="p-dashboard-wrapper">
    <!-- Hero Banner -->
    <div class="p-hero-banner d-flex flex-column flex-md-row justify-content-between align-items-center">
        <div class="text-center text-md-start">
            <span class="badge bg-primary px-3 py-2 rounded-pill mb-3" style="font-size: 0.75rem;">ADMIN PANEL</span>
            <h1>Operations <span class="text-primary opacity-75">Intelligence</span></h1>
            <p>Real-time analytics and content management orchestration. Monitor your system performance at a glance.</p>
        </div>
        <div class="mt-4 mt-md-0">
            <button id="filterToggleBtn" class="p-btn-filter" type="button">
                <i class="fas fa-sliders-h me-2"></i> Customize View
            </button>
        </div>
    </div>

    <!-- Filter Panel -->
    <div id="filterSection" class="p-filter-panel">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0 fw-bold">🔎 Dynamic Data Filters</h5>
            <button type="button" class="btn-close" id="closeFilterBtn" aria-label="Close"></button>
        </div>
        
        <form id="dashboardFilterForm">
            <div class="row g-4">
                <div class="col-12">
                    <label class="form-label fw-bold text-slate small mb-3">SELECT CONTENT STREAMS</label>
                </div>
                <div class="col-md-6 mt-4">
                    <label class="form-label fw-bold small">FROM DATE</label>
                    <input type="date" id="start_date" name="start_date" class="form-control rounded-3 py-2">
                </div>
                <div class="col-md-6 mt-4">
                    <label class="form-label fw-bold small">TO DATE</label>
                    <input type="date" id="end_date" name="end_date" class="form-control rounded-3 py-2">
                </div>
                <div class="col-12 text-end mt-4">
                    <button type="button" id="resetFilterBtn" class="btn btn-link text-decoration-none text-muted px-4">Reset Default</button>
                    <button type="button" id="applyFilterBtn" class="btn btn-primary px-5 rounded-3 fw-bold">Update Analytics</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Main Entities -->
    <h3 class="p-section-header"><i class="fas fa-database"></i> CORE ENTITIES</h3>
    <div class="row g-4">
        <div class="col-xl-3 col-lg-6 col-md-6 p-animate" style="animation-delay: 0.1s">
            <a href="#" class="p-stat-card">
                <div class="p-icon-box bg-soft-rose"><i class="fas fa-users"></i></div>
                <div class="p-stat-value" id="cardUsersCount">1</div>
                <div class="p-stat-label">Active Users</div>
            </a>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 p-animate" style="animation-delay: 0.2s">
            <a href="#" class="p-stat-card">
                <div class="p-icon-box bg-soft-indigo"><i class="fas fa-user-shield"></i></div>
                <div class="p-stat-value" id="cardRolesCount">1</div>
                <div class="p-stat-label">System Roles</div>
            </a>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 p-animate" style="animation-delay: 0.3s">
            <a href="#" class="p-stat-card">
                <div class="p-icon-box bg-soft-emerald"><i class="fas fa-book-open"></i></div>
                <div class="p-stat-value" id="cardMagazinesCount">1</div>
                <div class="p-stat-label">Digital Magazines</div>
            </a>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 p-animate" style="animation-delay: 0.4s">
            <a href="#" class="p-stat-card">
                <div class="p-icon-box bg-soft-amber"><i class="fab fa-youtube"></i></div>
                <div class="p-stat-value" id="cardYoutubesCount">1</div>
                <div class="p-stat-label">YouTube Resources</div>
            </a>
        </div>
    </div>

    <!-- Content Performance -->
    <h3 class="p-section-header"><i class="fas fa-layer-group"></i> NEWS SYNDICATION</h3>
    <div class="row g-4">
        <div class="col-xl-3 col-lg-6 col-md-6 p-animate" style="animation-delay: 0.5s">
            <a href="#" class="p-stat-card">
                <div class="p-icon-box bg-soft-slate"><i class="fas fa-globe"></i></div>
                <div class="p-stat-value" id="cardBreakingNewsGlobalsCount">1</div>
                <div class="p-stat-label">Global Breaking</div>
            </a>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 p-animate" style="animation-delay: 0.6s">
            <a href="#" class="p-stat-card">
                <div class="p-icon-box bg-soft-emerald"><i class="fas fa-map-marker-alt"></i></div>
                <div class="p-stat-value" id="cardBreakingNewsLocalsCount">1</div>
                <div class="p-stat-label">Local Breaking</div>
            </a>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 p-animate" style="animation-delay: 0.7s">
            <a href="#" class="p-stat-card">
                <div class="p-icon-box bg-soft-amber"><i class="fas fa-fire"></i></div>
                <div class="p-stat-value" id="cardTrendingNewsGlobalsCount">1</div>
                <div class="p-stat-label">Global Trending</div>
            </a>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 p-animate" style="animation-delay: 0.8s">
            <a href="#" class="p-stat-card">
                <div class="p-icon-box bg-soft-rose"><i class="fas fa-rss"></i></div>
                <div class="p-stat-value" id="cardTrendingNewsLocalsCount">1</div>
                <div class="p-stat-label">Local Trending</div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6 p-animate" style="animation-delay: 0.9s">
            <a href="#" class="p-stat-card">
                <div class="p-icon-box bg-soft-violet"><i class="fas fa-archive"></i></div>
                <div class="p-stat-value" id="cardGlobalNewsCount">1</div>
                <div class="p-stat-label">Global News Archive</div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6 p-animate" style="animation-delay: 1.0s">
            <a href="#" class="p-stat-card">
                <div class="p-icon-box bg-soft-cyan"><i class="fas fa-robot"></i></div>
                <div class="p-stat-value" id="cardBreakingAiNewsGlobalCount">1</div>
                <div class="p-stat-label">AI Intelligence Breaking</div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6 p-animate" style="animation-delay: 1.1s">
            <a href="#" class="p-stat-card">
                <div class="p-icon-box bg-soft-indigo"><i class="fas fa-play-circle"></i></div>
                <div class="p-stat-value" id="cardVideoShortsCount">1</div>
                <div class="p-stat-label">Short Video Snippets</div>
            </a>
        </div>
    </div>

    <!-- Media Assets -->
    <h3 class="p-section-header"><i class="fas fa-photo-video"></i> REPOSITORY METRICS</h3>
    <div class="row g-4 mb-5">
        <div class="col-xl-4 col-md-6 p-animate" style="animation-delay: 1.2s">
            <div class="p-stat-card">
                <div class="p-icon-box bg-soft-indigo"><i class="fas fa-images"></i></div>
                <div class="p-stat-value" id="cardNewsImagesCount">1</div>
                <div class="p-stat-label">Public News Media</div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 p-animate" style="animation-delay: 1.3s">
            <div class="p-stat-card">
                <div class="p-icon-box bg-soft-rose"><i class="fas fa-film"></i></div>
                <div class="p-stat-value" id="cardVideoCount">1</div>
                <div class="p-stat-label">Syndicated Video Files</div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 p-animate" style="animation-delay: 1.4s">
            <div class="p-stat-card">
                <div class="p-icon-box bg-soft-emerald"><i class="fas fa-camera"></i></div>
                <div class="p-stat-value" id="cardVideoImagesCount">1</div>
                <div class="p-stat-label">Media Thumbnails</div>
            </div>
        </div>
    </div>
</div>


<!-- <div class="row">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-warning border-0 d-flex align-items-center" role="alert">
                    <i class="uil uil-exclamation-triangle font-size-16 text-warning me-2"></i>
                    <div class="flex-grow-1 text-truncate">
                        Your free trial expired in <b>21</b> days.
                    </div>
                    <div class="flex-shrink-0">
                        <a href="pricing-basic.html"
                            class="text-reset text-decoration-underline"><b>Upgrade</b></a>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-sm-7">
                        <p class="font-size-18">Upgrade your plan from a <span
                                class="fw-semibold">Free
                                trial</span>, to ‘Premium Plan’ <i class="mdi mdi-arrow-right"></i>
                        </p>
                        <div class="mt-4">
                            <a href="pricing-basic.html" class="btn btn-primary">Upgrade
                                Account!</a>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <img src="assets/images/illustrator/2.png" class="img-fluid" alt="">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="float-end">
                    <div class="dropdown">
                        <a class="dropdown-toggle text-reset" href="#" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="fw-semibold">Report By:</span> <span
                                class="text-muted">Monthly<i
                                    class="mdi mdi-chevron-down ms-1"></i></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Yearly</a>
                            <a class="dropdown-item" href="#">Monthly</a>
                            <a class="dropdown-item" href="#">Weekly</a>
                            <a class="dropdown-item" href="#">Today</a>
                        </div>
                    </div>
                </div>

                <h4 class="card-title mb-4">Earning Reports</h4>

                <div class="row align-items-center">
                    <div class="col-sm-7">
                        <div class="row mb-3">
                            <div class="col-6">
                                <p class="text-muted mb-2">This Month</p>
                                <h5>$12,582<small
                                        class="badge badge-success-subtle font-13 ms-2">+15%</small>
                                </h5>
                            </div>

                            <div class="col-6">
                                <p class="text-muted mb-2">Last Month</p>
                                <h5>$98,741</h5>
                            </div>
                        </div>
                        <p class="text-muted"><span class="text-success me-1"> 25.2%<i
                                    class="mdi mdi-arrow-up"></i></span>From previous period</p>

                        <div class="mt-4">
                            <a href="" class="btn btn-secondary-subtlebtn-sm">Generate Reports <i
                                    class="mdi mdi-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="mt-4 mt-0">
                            <div id="donut_chart" class="apex-charts" dir="ltr"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="float-end">
                    <div class="dropdown">
                        <a class="dropdown-toggle text-reset" href="#" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="fw-semibold">Sort By:</span> <span
                                class="text-muted">Yearly<i
                                    class="mdi mdi-chevron-down ms-1"></i></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Yearly</a>
                            <a class="dropdown-item" href="#">Monthly</a>
                            <a class="dropdown-item" href="#">Weekly</a>
                            <a class="dropdown-item" href="#">Today</a>
                        </div>
                    </div>
                </div>
                <h4 class="card-title mb-4">Sales Analytics</h4>

                <div class="mt-1">
                    <ul class="list-inline main-chart mb-0 text-center">
                        <li class="list-inline-item chart-border-left me-0 border-0">
                            <h3 class="text-primary">$<span
                                    data-plugin="counterup">3.85k</span><span
                                    class="text-muted d-inline-block fw-normal font-size-15 ms-2">Income</span>
                            </h3>
                        </li>
                        <li class="list-inline-item chart-border-left me-0">
                            <h3><span data-plugin="counterup">258</span><span
                                    class="text-muted d-inline-block fw-normal font-size-15 ms-2">Sales</span>
                            </h3>
                        </li>
                        <li class="list-inline-item chart-border-left me-0">
                            <h3><span data-plugin="counterup">52</span>k<span
                                    class="text-muted d-inline-block fw-normal font-size-15 ms-2">Users</span>
                            </h3>
                        </li>
                    </ul>
                </div>

                <div class="mt-3">
                    <div id="sales-analytics-chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>
</div> -->

<!-- <div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title mb-4">Orders</h4>

                    <div>
                        <div class="dropdown d-inline">
                            <a class="dropdown-toggle text-muted me-3 mb-3 align-middle" href="#"
                                data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class='bx bx-search font-size-16'></i>
                            </a>

                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0">
                                <form class="p-2">
                                    <div class="search-box">
                                        <div class="position-relative">
                                            <input type="text"
                                                class="form-control rounded bg-light border-0"
                                                placeholder="Search...">
                                            <i class="bx bx-search font-size-16 search-icon"></i>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="dropdown d-inline">
                            <a class="dropdown-toggle text-reset mb-3" href="#"
                                data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <span class="fw-semibold">Report By:</span> <span
                                    class="text-muted">Monthly<i
                                        class="mdi mdi-chevron-down ms-1"></i></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">Yearly</a>
                                <a class="dropdown-item" href="#">Monthly</a>
                                <a class="dropdown-item" href="#">Weekly</a>
                                <a class="dropdown-item" href="#">Today</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-nowrap mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="rounded-start" style="width: 15px;">
                                    <div class="form-check">
                                        <input class="form-check-input font-size-16" type="checkbox"
                                            value="" id="flexCheckDefault">
                                        <label class="form-check-label" for="flexCheckDefault">
                                        </label>
                                    </div>
                                </th>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Customer</th>
                                <th>Purchased</th>
                                <th colspan="2" class="rounded-end">Revenue</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input font-size-16" type="checkbox"
                                            value="" id="flexCheckexampleone">
                                        <label class="form-check-label" for="flexCheckexampleone">
                                        </label>
                                    </div>
                                </td>
                                <td class="fw-medium">
                                    #DK1018
                                </td>
                                <td>
                                    1 Jun, 11:21
                                </td>

                                <td>
                                    <div class="d-flex">
                                        <div class="me-2">
                                            <i
                                                class="mdi mdi-check-circle-outline text-success"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0">Paid</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <img src="{{ asset('adminDashboard/assets/images/avatar-1.jpg') }}"
                                                class="avatar-sm img-thumbnail h-auto rounded-circle"
                                                alt="Error">
                                        </div>
                                        <div>
                                            <h5 class="font-size-14 text-truncate mb-0"><a href="#"
                                                    class="text-reset">Alex Fox</a>
                                            </h5>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    Wireframing Kit for Figma
                                </td>

                                <td>
                                    $129.99
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle card-drop"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i
                                                class="mdi mdi-dots-horizontal font-size-18 text-muted"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a href="#" class="dropdown-item"><i
                                                        class="mdi mdi-pencil font-size-16 text-success me-1"></i>
                                                    Edit</a></li>
                                            <li><a href="#" class="dropdown-item"><i
                                                        class="mdi mdi-trash-can font-size-16 text-danger me-1"></i>
                                                    Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input font-size-16" type="checkbox"
                                            value="" id="flexCheckexamplethree">
                                        <label class="form-check-label" for="flexCheckexamplethree">
                                        </label>
                                    </div>
                                </td>
                                <td class="fw-medium">
                                    #DK1017
                                </td>
                                <td>
                                    29 May, 18:36
                                </td>

                                <td>
                                    <div class="d-flex">
                                        <div class="me-2">
                                            <i
                                                class="mdi mdi-check-circle-outline text-success"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0">Paid</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <img src="{{ asset('adminDashboard/assets/images/avatar-1.jpg') }}"
                                                class="avatar-sm img-thumbnail h-auto rounded-circle"
                                                alt="Error">
                                        </div>
                                        <div>
                                            <h5 class="font-size-14 text-truncate mb-0"><a href="#"
                                                    class="text-reset">Joya Calvert</a>
                                            </h5>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    Mastering the Grid <span class="text-muted">+2 more</span>
                                </td>

                                <td>
                                    $228.88
                                </td>

                                <td>
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle card-drop"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i
                                                class="mdi mdi-dots-horizontal font-size-18 text-muted"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a href="#" class="dropdown-item"><i
                                                        class="mdi mdi-pencil font-size-16 text-success me-1"></i>
                                                    Edit</a></li>
                                            <li><a href="#" class="dropdown-item"><i
                                                        class="mdi mdi-trash-can font-size-16 text-danger me-1"></i>
                                                    Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input font-size-16" type="checkbox"
                                            value="" id="flexCheckexamplefour">
                                        <label class="form-check-label" for="flexCheckexamplefour">
                                        </label>
                                    </div>
                                </td>
                                <td class="fw-medium">
                                    #DK1016
                                </td>
                                <td>
                                    25 May , 08:09
                                </td>

                                <td>
                                    <div class="d-flex">
                                        <div class="me-2">
                                            <i
                                                class="mdi mdi-arrow-left-thin-circle-outline text-warning"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0">Refunded</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <img src="{{ asset('adminDashboard/assets/images/avatar-1.jpg') }}"
                                                class="avatar-sm img-thumbnail h-auto rounded-circle"
                                                alt="Error">
                                        </div>
                                        <div>
                                            <h5 class="font-size-14 text-truncate mb-0"><a href="#"
                                                    class="text-reset">Gracyn Make</a>
                                            </h5>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    Wireframing Kit for Figma
                                </td>

                                <td>
                                    $9.99
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle card-drop"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i
                                                class="mdi mdi-dots-horizontal font-size-18 text-muted"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a href="#" class="dropdown-item"><i
                                                        class="mdi mdi-pencil font-size-16 text-success me-1"></i>
                                                    Edit</a></li>
                                            <li><a href="#" class="dropdown-item"><i
                                                        class="mdi mdi-trash-can font-size-16 text-danger me-1"></i>
                                                    Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input font-size-16" type="checkbox"
                                            value="" id="flexCheckexamplefive">
                                        <label class="form-check-label" for="flexCheckexamplefive">
                                        </label>
                                    </div>
                                </td>
                                <td class="fw-medium">
                                    #DK1015
                                </td>
                                <td>
                                    19 May , 14:09
                                </td>

                                <td>
                                    <div class="d-flex">
                                        <div class="me-2">
                                            <i
                                                class="mdi mdi-check-circle-outline text-success"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0">Paid</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <img src="{{ asset('adminDashboard/assets/images/avatar-1.jpg') }}"
                                                class="avatar-sm img-thumbnail h-auto rounded-circle"
                                                alt="Error">
                                        </div>
                                        <div>
                                            <h5 class="font-size-14 text-truncate mb-0"><a href="#"
                                                    class="text-reset">Monroe Mock</a>
                                            </h5>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    Spiashify 2.0
                                </td>

                                <td>
                                    $44.00
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle card-drop"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i
                                                class="mdi mdi-dots-horizontal font-size-18 text-muted"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a href="#" class="dropdown-item"><i
                                                        class="mdi mdi-pencil font-size-16 text-success me-1"></i>
                                                    Edit</a></li>
                                            <li><a href="#" class="dropdown-item"><i
                                                        class="mdi mdi-trash-can font-size-16 text-danger me-1"></i>
                                                    Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input font-size-16" type="checkbox"
                                            value="" id="flexCheckexamplesix">
                                        <label class="form-check-label" for="flexCheckexamplesix">
                                        </label>
                                    </div>
                                </td>
                                <td class="fw-medium">
                                    #DK1014
                                </td>
                                <td>
                                    10 May , 10:00
                                </td>

                                <td>
                                    <div class="d-flex">
                                        <div class="me-2">
                                            <i
                                                class="mdi mdi-check-circle-outline text-success"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0">Paid</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <img src="{{ asset('adminDashboard/assets/images/avatar-1.jpg') }}"
                                                class="avatar-sm img-thumbnail h-auto rounded-circle"
                                                alt="Error">
                                        </div>
                                        <div>
                                            <h5 class="font-size-14 text-truncate mb-0"><a href="#"
                                                    class="text-reset">Lauren Bond</a>
                                            </h5>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    Mastering the Grid
                                </td>

                                <td>
                                    $75.87
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle card-drop"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i
                                                class="mdi mdi-dots-horizontal font-size-18 text-muted"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a href="#" class="dropdown-item"><i
                                                        class="mdi mdi-pencil font-size-16 text-success me-1"></i>
                                                    Edit</a></li>
                                            <li><a href="#" class="dropdown-item"><i
                                                        class="mdi mdi-trash-can font-size-16 text-danger me-1"></i>
                                                    Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input font-size-16" type="checkbox"
                                            value="" id="flexCheckexamplenine">
                                        <label class="form-check-label" for="flexCheckexamplenine">
                                        </label>
                                    </div>
                                </td>
                                <td class="fw-medium">
                                    #DK1011
                                </td>
                                <td>
                                    29 Apr , 12:46
                                </td>

                                <td>
                                    <div class="d-flex">
                                        <div class="me-2">
                                            <i class="mdi mdi-close-circle-outline text-danger"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0">Changeback</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <img src="{{ asset('adminDashboard/assets/images/avatar-1.jpg') }}"
                                                class="avatar-sm img-thumbnail h-auto rounded-circle"
                                                alt="Error">
                                        </div>
                                        <div>
                                            <h5 class="font-size-14 text-truncate mb-0"><a href="#"
                                                    class="text-reset">Ricardo Schaefer</a> </h5>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    Spiashify 2.0
                                </td>

                                <td>
                                    $63.99
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle card-drop"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i
                                                class="mdi mdi-dots-horizontal font-size-18 text-muted"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a href="#" class="dropdown-item"><i
                                                        class="mdi mdi-pencil font-size-16 text-success me-1"></i>
                                                    Edit</a></li>
                                            <li><a href="#" class="dropdown-item"><i
                                                        class="mdi mdi-trash-can font-size-16 text-danger me-1"></i>
                                                    Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input font-size-16" type="checkbox"
                                            value="" id="flexCheckDefaultexample">
                                        <label class="form-check-label"
                                            for="flexCheckDefaultexample">
                                        </label>
                                    </div>
                                </td>
                                <td class="fw-medium">
                                    #DK1010
                                </td>
                                <td>
                                    27 Apr , 10:53
                                </td>

                                <td>
                                    <div class="d-flex">
                                        <div class="me-2">
                                            <i
                                                class="mdi mdi-check-circle-outline text-success"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0">Paid</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <img src="{{ asset('adminDashboard/assets/images/avatar-1.jpg') }}"
                                                class="avatar-sm img-thumbnail h-auto rounded-circle"
                                                alt="Error">
                                        </div>
                                        <div>
                                            <h5 class="font-size-14 text-truncate mb-0"><a href="#"
                                                    class="text-reset">Arvi Hasan</a> </h5>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    Wireframing Kit for Figma
                                </td>

                                <td>
                                    $51.00
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle card-drop"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i
                                                class="mdi mdi-dots-horizontal font-size-18 text-muted"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a href="#" class="dropdown-item"><i
                                                        class="mdi mdi-pencil font-size-16 text-success me-1"></i>
                                                    Edit</a></li>
                                            <li><a href="#" class="dropdown-item"><i
                                                        class="mdi mdi-trash-can font-size-16 text-danger me-1"></i>
                                                    Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title mb-4">Sales by County</h4>
                    <div class="dropdown">
                        <a class="dropdown-toggle text-reset" href="#" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="fw-semibold">Report By:</span> <span
                                class="text-muted">Monthly<i
                                    class="mdi mdi-chevron-down ms-1"></i></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Yearly</a>
                            <a class="dropdown-item" href="#">Monthly</a>
                            <a class="dropdown-item" href="#">Weekly</a>
                            <a class="dropdown-item" href="#">Today</a>
                        </div>
                    </div>
                </div>

                <div id="world-map-markers" style="height: 242px;"></div>

                <div class="pt-3 px-2 pb-2">
                    <p class="mb-1 fw-medium">USA <span class="float-end">75%</span></p>
                    <div class="progress animated-progess custom-progress mt-2">
                        <div class="progress-bar" role="progressbar" style="width: 75%"
                            aria-valuenow="75" aria-valuemin="0" aria-valuemax="75">
                        </div>
                    </div>

                    <p class="mt-4 mb-1 fw-medium">Russia <span class="float-end">55%</span></p>
                    <div class="progress animated-progess custom-progress mt-2">
                        <div class="progress-bar" role="progressbar" style="width: 55%"
                            aria-valuenow="55" aria-valuemin="0" aria-valuemax="55">
                        </div>
                    </div>

                    <p class="mt-4 mb-1 fw-medium">Australia <span class="float-end">85%</span></p>
                    <div class="progress animated-progess custom-progress mt-2">
                        <div class="progress-bar" role="progressbar" style="width: 85%"
                            aria-valuenow="85" aria-valuemin="0" aria-valuemax="85">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div> -->
@endsection