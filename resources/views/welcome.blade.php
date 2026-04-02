<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Elite Guard - Enterprise Security Management</title>

        <!-- Google Fonts: Inter & Outfit -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- AOS Animation Library -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        
        <!-- Bootstrap 5 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <style>
            :root {
                --primary: #8b5cf6;
                --primary-glow: rgba(139, 92, 246, 0.4);
                --secondary: #3b82f6;
                --accent: #10b981;
                --bg-dark: #020617;
                --bg-card: rgba(15, 23, 42, 0.6);
                --glass-border: rgba(255, 255, 255, 0.08);
                --text-main: #f8fafc;
                --text-dim: #94a3b8;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                cursor: default;
            }
            
            a, button { cursor: pointer; }

            body {
                font-family: 'Outfit', sans-serif;
                background-color: var(--bg-dark);
                color: var(--text-main);
                overflow-x: hidden;
                line-height: 1.6;
            }

            /* --- Background Architecture --- */
            .main-scene {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100vh;
                z-index: -2;
                background: radial-gradient(circle at 20% 30%, #1e1b4b 0%, transparent 40%),
                            radial-gradient(circle at 80% 70%, #0f172a 0%, transparent 40%);
            }

            .hero-wallpaper {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100vh;
                background-image: linear-gradient(to bottom, rgba(2, 6, 23, 0.8), rgba(2, 6, 23, 0.98)), 
                                  url('{{ asset('brain/0aa9d240-740f-43ee-8a8c-d4ab45a72724/elite_guard_hero_bg_1775160362111.png') }}');
                background-size: cover;
                background-position: center;
                z-index: -1;
                opacity: 0.7;
            }

            /* --- Navigation --- */
            .nav-elite {
                padding: 60px 0;
                background: transparent;
                position: relative;
                width: 100%;
                z-index: 1000;
            }

            .brand-name {
                font-family: 'Inter', sans-serif;
                font-weight: 850;
                font-size: 2.2rem;
                letter-spacing: -2px;
                background: linear-gradient(to right, #fff, var(--primary));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                text-decoration: none;
            }

            /* --- Hero Section --- */
            .hero-section {
                min-height: 100vh;
                display: flex;
                align-items: center;
                position: relative;
            }

            .badge-os {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid var(--glass-border);
                backdrop-filter: blur(5px);
                color: var(--primary);
                padding: 10px 20px;
                border-radius: 50px;
                font-weight: 800;
                font-size: 0.75rem;
                letter-spacing: 3px;
                text-transform: uppercase;
                margin-bottom: 2rem;
                display: inline-block;
            }

            .title-hq {
                font-size: clamp(3rem, 10vw, 6.5rem);
                font-weight: 950;
                line-height: 0.9;
                margin-bottom: 25px;
                letter-spacing: -5px;
            }

            .text-glow {
                text-shadow: 0 0 50px var(--primary-glow);
                background: linear-gradient(135deg, #fff 0%, #cbd5e1 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .desc-hq {
                font-size: 1.3rem;
                color: var(--text-dim);
                max-width: 650px;
                margin-bottom: 4rem;
                font-weight: 400;
                line-height: 1.7;
            }

            /* --- Buttons --- */
            .btn-action-hub {
                padding: 20px 45px;
                border-radius: 20px;
                font-weight: 800;
                font-size: 1.2rem;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 15px;
                transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
                border: none;
                position: relative;
                overflow: hidden;
            }

            .btn-primary-hub {
                background: var(--primary);
                color: white;
                box-shadow: 0 15px 40px -10px var(--primary-glow);
            }

            .btn-primary-hub:hover {
                background: var(--primary-dark);
                transform: scale(1.05) translateY(-5px);
                box-shadow: 0 25px 50px -12px var(--primary-glow);
                color: white;
            }

            .btn-secondary-hub {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid var(--glass-border);
                color: white;
                backdrop-filter: blur(10px);
            }

            .btn-secondary-hub:hover {
                background: rgba(255, 255, 255, 0.1);
                border-color: white;
                color: white;
            }

            /* --- Intelligence Cards --- */
            .intel-card {
                background: var(--bg-card);
                backdrop-filter: blur(20px);
                border: 1px solid var(--glass-border);
                padding: 45px;
                border-radius: 35px;
                transition: all 0.5s ease;
                height: 100%;
                position: relative;
            }

            .intel-card:hover {
                border-color: var(--primary);
                transform: translateY(-15px);
                background: rgba(15, 23, 42, 0.8);
            }

            .intel-icon {
                width: 70px;
                height: 70px;
                background: linear-gradient(135deg, var(--primary), var(--secondary));
                border-radius: 22px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 30px;
                color: white;
                font-size: 1.8rem;
                box-shadow: 0 10px 20px -5px var(--primary-glow);
            }

            .stat-badge {
                position: absolute;
                top: 45px;
                right: 45px;
                font-size: 0.7rem;
                font-weight: 800;
                color: var(--accent);
                background: rgba(16, 185, 129, 0.1);
                padding: 5px 12px;
                border-radius: 10px;
                border: 1px solid rgba(16, 185, 129, 0.2);
            }

            /* --- Alerts --- */
            .security-alert-box {
                position: fixed;
                top: 30px;
                right: 30px;
                z-index: 10000;
                min-width: 400px;
            }

            .glass-alert {
                background: rgba(239, 68, 68, 0.1);
                backdrop-filter: blur(30px);
                border: 1px solid rgba(239, 68, 68, 0.2);
                border-left: 6px solid #ef4444;
                padding: 30px;
                border-radius: 20px;
                display: flex;
                align-items: center;
                gap: 20px;
                animation: slideInShake 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            }

            @keyframes slideInShake {
                0% { transform: translateX(100%); }
                70% { transform: translateX(-10%); }
                100% { transform: translateX(0); }
            }

            footer {
                padding: 80px 0;
                text-align: center;
                color: var(--text-dim);
                background: rgba(0,0,0,0.3);
                margin-top: 150px;
            }

            .tag-line {
                font-weight: 800;
                color: var(--primary);
                display: block;
                margin-bottom: 10px;
            }
        </style>
    </head>
    <body>
        @if(session('error'))
            <div class="security-alert-box">
                <div class="glass-alert shadow-2xl">
                    <div class="alert-icon-circle">
                        <i class="fas fa-lock fa-2x text-danger animate-pulse"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-danger">SECURITY EXCEPTION</h5>
                        <p class="mb-0 small text-white-50 opacity-100">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="main-scene"></div>
        <div class="hero-wallpaper"></div>

        <nav class="nav-elite">
            <div class="container d-flex justify-content-between align-items-center">
                <a href="#" class="brand-name">ELITE GUARD</a>
                <div class="d-none d-md-flex align-items-center gap-5">
                    @auth
                        @if(Auth::user()->role === 'SuperAdmin')
                            <a href="{{ url('/dashboard') }}" class="btn-action-hub btn-primary-hub py-2 px-4 fs-6">
                                DASHBOARD <i class="fas fa-microchip"></i>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn-action-hub btn-secondary-hub py-2 px-4 fs-6">
                            SYSTEM ACCESS <i class="fas fa-shield-halved"></i>
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-8" data-aos="fade-up" data-aos-duration="1000">
                        <div class="badge-os">TACTICAL OPERATIONS OS v2.4</div>
                        <h1 class="title-hq text-glow">The Hub of <br>Elite Security.</h1>
                        <p class="desc-hq">A comprehensive infrastructure for high-stakes patrol management. Synchronize NFC checkpoints, personnel strength, and tactical sites with military-grade intelligence in real-time.</p>
                        
                        <div class="d-flex flex-wrap gap-4">
                            <a href="{{ route('login') }}" class="btn-action-hub btn-primary-hub">
                                INITIALIZE PROTOCOL <i class="fas fa-bolt-lightning"></i>
                            </a>
                            <a href="{{ route('architecture') }}" class="btn-action-hub btn-secondary-hub">
                                LEARN ARCHITECTURE <i class="fas fa-info-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-5 pt-5">
                    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="intel-card">
                            <span class="stat-badge">ENCRYPTED</span>
                            <div class="intel-icon"><i class="fas fa-rss"></i></div>
                            <h3 class="fw-bold mb-3">NFC Integration</h3>
                            <p class="text-dim">hardware-level scan verification with real-time payload decryption and site linking.</p>
                        </div>
                    </div>
                    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="intel-card">
                            <span class="stat-badge">REAL-TIME</span>
                            <div class="intel-icon" style="background: linear-gradient(135deg, #3b82f6, #0ea5e9);"><i class="fas fa-map-location-dot"></i></div>
                            <h3 class="fw-bold mb-3">Site Intelligence</h3>
                            <p class="text-dim">Centralized command over diverse operational zones, patrol routes, and tactical sites.</p>
                        </div>
                    </div>
                    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="intel-card">
                            <span class="stat-badge">SECURE</span>
                            <div class="intel-icon" style="background: linear-gradient(135deg, #10b981, #34d399);"><i class="fas fa-user-gear"></i></div>
                            <h3 class="fw-bold mb-3">Personnel OS</h3>
                            <p class="text-dim">Complete administration of elite personnel, roles, and administrative controllers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            <div class="container">
                <span class="tag-line">DESIGNED FOR OPERATION EXCELLENCE</span>
                <p class="small">&copy; {{ date('Y') }} Elite Guard Operational Systems. All critical data is encrypted under AES-256 standard.</p>
            </div>
        </footer>

        <!-- Scripts -->
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init({
                once: true,
                mirror: false
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
