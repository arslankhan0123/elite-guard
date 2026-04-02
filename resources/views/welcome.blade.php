<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Degree Town - Coming Soon</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

        <style>
            :root {
                --primary: #6366f1;
                --primary-hover: #4f46e5;
                --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
                --glass: rgba(255, 255, 255, 0.03);
                --glass-border: rgba(255, 255, 255, 0.1);
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Outfit', sans-serif;
                background: var(--bg-gradient);
                background-size: 400% 400%;
                animation: gradientBG 15s ease infinite;
                color: #fff;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                position: relative;
            }

            @keyframes gradientBG {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            /* Animated background circles */
            .circle {
                position: absolute;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
                z-index: 0;
                filter: blur(40px);
                animation: float 20s infinite ease-in-out;
            }

            .circle-1 { width: 400px; height: 400px; top: -100px; left: -100px; }
            .circle-2 { width: 400px; height: 400px; bottom: -100px; right: -100px; animation-delay: -5s; }

            @keyframes float {
                0%, 100% { transform: translateY(0) scale(1); }
                50% { transform: translateY(-30px) scale(1.1); }
            }

            .container {
                z-index: 10;
                text-align: center;
                padding: 2rem;
                max-width: 800px;
                width: 90%;
            }

            .logo {
                font-weight: 700;
                font-size: 3.5rem;
                letter-spacing: -1px;
                margin-bottom: 1rem;
                background: linear-gradient(to right, #fff, #a5b4fc);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                display: inline-block;
            }

            .coming-soon {
                font-size: 1.25rem;
                color: #94a3b8;
                font-weight: 300;
                text-transform: uppercase;
                letter-spacing: 4px;
                margin-bottom: 3rem;
                display: block;
            }

            .card {
                background: var(--glass);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid var(--glass-border);
                padding: 3rem;
                border-radius: 2rem;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                margin-bottom: 3rem;
            }

            h1 {
                font-size: 2.5rem;
                font-weight: 600;
                margin-bottom: 1rem;
            }

            p {
                color: #94a3b8;
                font-size: 1.1rem;
                line-height: 1.6;
                margin-bottom: 2rem;
            }

            .btn-group {
                display: flex;
                gap: 1.5rem;
                justify-content: center;
                flex-wrap: wrap;
            }

            .btn {
                padding: 0.875rem 2.5rem;
                border-radius: 12px;
                font-weight: 600;
                font-size: 1rem;
                text-decoration: none;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                cursor: pointer;
            }

            .btn-primary {
                background: var(--primary);
                color: #fff;
                box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
            }

            .btn-primary:hover {
                background: var(--primary-hover);
                transform: translateY(-2px);
                box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.4);
            }

            .btn-outline {
                background: transparent;
                border: 1px solid var(--glass-border);
                color: #fff;
            }

            .btn-outline:hover {
                background: rgba(255, 255, 255, 0.05);
                border-color: rgba(255, 255, 255, 0.3);
                transform: translateY(-2px);
            }

            footer {
                position: absolute;
                bottom: 2rem;
                color: #64748b;
                font-size: 0.875rem;
            }

            @media (max-width: 640px) {
                .logo { font-size: 2.5rem; }
                .card { padding: 2rem; }
                h1 { font-size: 2rem; }
            }
        </style>
    </head>
    <body>
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>

        <div class="container">
            <div class="logo">Degree Town</div>
            <span class="coming-soon">Evolution in Education</span>

            <div class="card">
                <h1>We're launching soon</h1>
                <p>The ultimate destination for your academic journey is under construction. Join our community and stay updated as we prepare to redefine degree management.</p>
                
                @if (Route::has('login'))
                    <div class="btn-group">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                                Go to Dashboard
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                Log in
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-outline">
                                    Register
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" y1="8" x2="19" y2="14"></line><line x1="22" y1="11" x2="16" y2="11"></line></svg>
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>

        <footer>
            &copy; {{ date('Y') }} Degree Town. All rights reserved.
        </footer>
    </body>
</html>
