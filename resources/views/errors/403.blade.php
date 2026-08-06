<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied | Elite Security</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            color: #1f2552;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at 15% 20%, rgba(124, 58, 237, .12), transparent 28%),
                radial-gradient(circle at 85% 80%, rgba(32, 201, 151, .10), transparent 25%),
                linear-gradient(135deg, #f8f7ff 0%, #f4f8ff 100%);
        }
        .card {
            width: min(560px, 100%);
            padding: 46px 42px;
            text-align: center;
            border: 1px solid rgba(124, 58, 237, .12);
            border-radius: 26px;
            background: rgba(255, 255, 255, .94);
            box-shadow: 0 24px 70px rgba(31, 37, 82, .13);
        }
        .logo { width: 68px; height: 68px; object-fit: contain; margin-bottom: 20px; }
        .icon {
            width: 84px;
            height: 84px;
            display: grid;
            place-items: center;
            margin: 0 auto 22px;
            border-radius: 50%;
            color: #fff;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            box-shadow: 0 12px 30px rgba(124, 58, 237, .3);
        }
        .icon svg { width: 40px; height: 40px; }
        .code {
            display: inline-block;
            margin-bottom: 10px;
            padding: 6px 13px;
            border-radius: 999px;
            color: #6d28d9;
            background: #f1eafe;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: .08em;
        }
        h1 { margin: 0 0 12px; color: #181d45; font-size: clamp(28px, 5vw, 38px); }
        p { margin: 0 auto 28px; max-width: 410px; color: #69708f; line-height: 1.65; }
        .actions { display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 145px;
            padding: 12px 20px;
            border: 0;
            border-radius: 11px;
            cursor: pointer;
            text-decoration: none;
            font: inherit;
            font-weight: 700;
            transition: transform .18s ease, box-shadow .18s ease;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn-primary { color: #fff; background: linear-gradient(135deg, #7c3aed, #6d28d9); box-shadow: 0 9px 20px rgba(109, 40, 217, .23); }
        .btn-light { color: #4b5273; background: #eef0f7; }
        .btn svg { width: 18px; height: 18px; }
        @media (max-width: 520px) { .card { padding: 36px 22px; } .btn { width: 100%; } }
    </style>
</head>
<body>
    @php
        $landingRoute = auth()->check() ? auth()->user()->adminLandingRoute() : 'login';
        $landingUrl = route($landingRoute);
    @endphp
    <main class="card">
        <img src="{{ asset('logo.png') }}" alt="Elite Security" class="logo">
        <div class="icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 3 4.5 6v5.2c0 4.8 3.1 8.2 7.5 9.8 4.4-1.6 7.5-5 7.5-9.8V6L12 3Z"/>
                <path d="m9.2 14.8 5.6-5.6M9.2 9.2l5.6 5.6"/>
            </svg>
        </div>
        <span class="code">ERROR 403</span>
        <h1>Access Denied</h1>
        <p>You do not have permission to open this page. Please go back or continue to an area available to your account.</p>
        <div class="actions">
            <button type="button" class="btn btn-primary" onclick="goBack()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                Go Back
            </button>
            <a href="{{ $landingUrl }}" class="btn btn-light">My Portal</a>
        </div>
    </main>
    <script>
        function goBack() {
            if (window.history.length > 1) window.history.back();
            else window.location.href = @json($landingUrl);
        }
    </script>
</body>
</html>
