<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Admin Login — Elite Guard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background:#0f1117; min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .login-card { background:#1a1d27; border:1px solid #2a2d3e; border-radius:12px; width:100%; max-width:420px; padding:2.5rem; }
        .form-control { background:#0f1117; border-color:#2a2d3e; color:#c5c8d9; }
        .form-control:focus { background:#0f1117; border-color:#ffc107; color:#c5c8d9; box-shadow:0 0 0 .2rem rgba(255,193,7,.15); }
        .form-control::placeholder { color:#4a4d5e; }
        .form-label { color:#8b8fa8; font-size:.875rem; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="text-center mb-4">
        <i class="bi bi-shield-fill-check text-warning" style="font-size:2.5rem;"></i>
        <h5 class="mt-2 mb-0 fw-bold">Master Admin Portal</h5>
        <p class="text-muted small mt-1">Elite Guard Platform</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger py-2 small">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('master.login.submit') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="admin@eliteguard.com" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="••••••••" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-4 form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label text-muted small" for="remember">Remember me</label>
        </div>
        <button type="submit" class="btn btn-warning w-100 fw-semibold">
            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
        </button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
