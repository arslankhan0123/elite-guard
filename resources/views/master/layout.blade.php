<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Admin — @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background:#0f1117; }
        .sidebar { width:240px; min-height:100vh; background:#1a1d27; border-right:1px solid #2a2d3e; }
        .sidebar .brand { padding:1.25rem 1rem; border-bottom:1px solid #2a2d3e; }
        .sidebar .nav-link { color:#8b8fa8; padding:.6rem 1rem; border-radius:.4rem; margin:.1rem .5rem; transition:all .2s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background:#2a2d3e; color:#ffc107; }
        .sidebar .nav-link i { width:20px; }
        .main-content { flex:1; min-height:100vh; }
        .topbar { background:#1a1d27; border-bottom:1px solid #2a2d3e; padding:.75rem 1.5rem; }
        .card { background:#1a1d27; border:1px solid #2a2d3e; }
        .card-header { background:#1e2130; border-bottom:1px solid #2a2d3e; }
        .card-footer { background:#1e2130; border-top:1px solid #2a2d3e; }
        .table { color:#c5c8d9; }
        .table th { color:#8b8fa8; font-size:.8rem; text-transform:uppercase; letter-spacing:.05em; border-color:#2a2d3e; }
        .table td { border-color:#2a2d3e; }
        .form-control, .form-select { background:#0f1117; border-color:#2a2d3e; color:#c5c8d9; }
        .form-control:focus, .form-select:focus { background:#0f1117; border-color:#ffc107; color:#c5c8d9; box-shadow:0 0 0 .2rem rgba(255,193,7,.15); }
        .form-control::placeholder { color:#4a4d5e; }
        .form-text { color:#8b8fa8 !important; }
        .badge.bg-success { background:#0d7055 !important; }
        dl.row dt { font-weight:500; }
    </style>
</head>
<body>
<div class="d-flex">
    {{-- Sidebar --}}
    <div class="sidebar d-flex flex-column">
        <div class="brand">
            <div class="text-warning fw-bold fs-6"><i class="bi bi-shield-fill-check me-2"></i>Master Admin</div>
            <div class="text-muted small mt-1">Elite Guard Platform</div>
        </div>
        <nav class="flex-grow-1 py-2">
            <a href="{{ route('master.dashboard') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('master.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('master.tenants.index') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('master.tenants.*') ? 'active' : '' }}">
                <i class="bi bi-buildings"></i> Tenants
            </a>
        </nav>
        <div class="p-3 border-top" style="border-color:#2a2d3e!important;">
            <div class="text-muted small mb-2">{{ Auth::guard('master')->user()?->name }}</div>
            <form method="POST" action="{{ route('master.logout') }}">
                @csrf
                <button class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </button>
            </form>
        </div>
    </div>

    {{-- Main content --}}
    <div class="main-content">
        <div class="topbar d-flex align-items-center justify-content-between">
            <h6 class="mb-0 text-muted">@yield('title', 'Dashboard')</h6>
            <span class="badge bg-warning text-dark"><i class="bi bi-shield-lock me-1"></i>Master Admin Portal</span>
        </div>
        <div class="p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
