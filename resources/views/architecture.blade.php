<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Elite Guard - System Architecture</title>

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
                --bg-dark: #020617;
                --bg-card: rgba(15, 23, 42, 0.6);
                --glass-border: rgba(255, 255, 255, 0.08);
                --accent: #10b981;
                --text-main: #f8fafc;
                --text-dim: #94a3b8;
            }

            body {
                font-family: 'Outfit', sans-serif;
                background-color: var(--bg-dark);
                color: var(--text-main);
                overflow-x: hidden;
                line-height: 1.6;
            }

            .main-scene {
                position: fixed;
                top: 0; left: 0; width: 100%; height: 100vh; z-index: -2;
                background: radial-gradient(circle at 50% 50%, #1e1b4b 0%, #020617 100%);
            }

            .nav-elite {
                padding: 40px 0;
                background: transparent;
                width: 100%;
                z-index: 1000;
            }

            .brand-name {
                font-family: 'Inter', sans-serif;
                font-weight: 850;
                font-size: 1.8rem;
                letter-spacing: -1.5px;
                background: linear-gradient(to right, #fff, var(--primary));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                text-decoration: none;
            }

            .header-section {
                padding: 100px 0 60px;
                text-align: center;
            }

            .badge-tactical {
                background: rgba(139, 92, 246, 0.1);
                border: 1px solid var(--glass-border);
                color: var(--primary);
                padding: 8px 18px;
                border-radius: 50px;
                font-weight: 800;
                font-size: 0.75rem;
                letter-spacing: 3px;
                text-transform: uppercase;
                margin-bottom: 2rem;
                display: inline-block;
            }

            .page-title {
                font-size: clamp(2.5rem, 6vw, 4.5rem);
                font-weight: 950;
                letter-spacing: -3px;
                margin-bottom: 20px;
                background: linear-gradient(to bottom, #fff, #94a3b8);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            /* Architecture Timeline */
            .arch-timeline {
                position: relative;
                max-width: 1000px;
                margin: 50px auto;
                padding: 40px 0;
            }

            .arch-timeline::before {
                content: '';
                position: absolute;
                left: 50%;
                top: 0;
                bottom: 0;
                width: 2px;
                background: linear-gradient(to bottom, transparent, var(--primary), transparent);
                transform: translateX(-50%);
            }

            .timeline-item {
                position: relative;
                margin-bottom: 120px;
                width: 100%;
                display: flex;
            }

            .timeline-content {
                width: 45%;
                background: var(--bg-card);
                backdrop-filter: blur(20px);
                border: 1px solid var(--glass-border);
                padding: 40px;
                border-radius: 30px;
                position: relative;
                transition: all 0.4s ease;
            }

            .timeline-content:hover {
                border-color: var(--primary);
                box-shadow: 0 0 30px var(--primary-glow);
                transform: translateY(-5px);
            }

            .timeline-item:nth-child(odd) { justify-content: flex-start; }
            .timeline-item:nth-child(even) { justify-content: flex-end; }

            .timeline-dot {
                position: absolute;
                left: 50%;
                top: 20px;
                width: 24px;
                height: 24px;
                background: var(--bg-dark);
                border: 4px solid var(--primary);
                border-radius: 50%;
                transform: translateX(-50%);
                z-index: 5;
                box-shadow: 0 0 15px var(--primary);
            }

            .step-number {
                font-weight: 900;
                font-size: 3rem;
                color: rgba(255, 255, 255, 0.05);
                position: absolute;
                top: 20px;
                right: 30px;
            }

            .icon-wrapper {
                width: 60px;
                height: 60px;
                background: rgba(139, 92, 246, 0.1);
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 25px;
                color: var(--primary);
                font-size: 1.5rem;
            }

            .cta-box {
                margin: 100px 0;
                padding: 80px;
                background: linear-gradient(135deg, var(--bg-card), #1e1b4b);
                border-radius: 50px;
                text-align: center;
                border: 1px solid var(--glass-border);
            }

            .btn-elite {
                padding: 18px 40px;
                border-radius: 18px;
                font-weight: 800;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 12px;
                transition: 0.3s;
            }

            .btn-glow {
                background: var(--primary);
                color: white;
                box-shadow: 0 10px 30px var(--primary-glow);
            }

            .btn-glow:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 40px var(--primary-glow);
                color: white;
            }

            @media (max-width: 768px) {
                .arch-timeline::before { left: 20px; }
                .timeline-dot { left: 20px; }
                .timeline-content { width: 85%; margin-left: 50px; }
                .timeline-item { width: 100% !important; justify-content: flex-start !important; }
            }
        </style>
    </head>
    <body>
        <div class="main-scene"></div>

        <nav class="nav-elite">
            <div class="container d-flex justify-content-between align-items-center">
                <a href="{{ url('/') }}" class="brand-name">ELITE GUARD</a>
                <a href="{{ url('/') }}" class="text-white-50 text-decoration-none small fw-bold">
                    <i class="fas fa-arrow-left me-2"></i> BACK TO HQ
                </a>
            </div>
        </nav>

        <header class="header-section container">
            <div class="badge-tactical" data-aos="fade-down">Infrastructure Protocol</div>
            <h1 class="page-title" data-aos="zoom-in">The Ecosystem of <br>Supreme Control.</h1>
            <p class="text-dim mx-auto" style="max-width: 600px;" data-aos="fade-up">Elite Guard bridges the gap between physical patrol and digital intelligence through a multi-tier encrypted architecture.</p>
        </header>

        <main class="container">
            <div class="arch-timeline">
                <!-- Step 1 -->
                <div class="timeline-item" data-aos="fade-right">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="step-number">01</span>
                        <div class="icon-wrapper"><i class="fas fa-microchip"></i></div>
                        <h4 class="fw-bold">NFC Deployment</h4>
                        <p class="text-dim small">Physical encrypted NFC tags are deployed at tactical sites. Each tag is cryptographically paired with its specific geographical zone.</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="timeline-item" data-aos="fade-left">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="step-number">02</span>
                        <div class="icon-wrapper"><i class="fas fa-mobile-screen-button"></i></div>
                        <h4 class="fw-bold">Mobile Interaction</h4>
                        <p class="text-dim small">Patrol guards utilize the Elite Guard Mobile App to perform high-frequency scans. The app captures NFC telemetry and GPS coordinates.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="timeline-item" data-aos="fade-right">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="step-number">03</span>
                        <div class="icon-wrapper"><i class="fas fa-cloud-bolt"></i></div>
                        <h4 class="fw-bold">Cloud Verification</h4>
                        <p class="text-dim small">Payloads are transmitted to the Laravel API. The system verifies the UID integrity, guard credentials, and time-sync parameters.</p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="timeline-item" data-aos="fade-left">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="step-number">04</span>
                        <div class="icon-wrapper"><i class="fas fa-chart-line"></i></div>
                        <h4 class="fw-bold">Live intelligence</h4>
                        <p class="text-dim small">Verified data is instantly broadcast to the Administrative Dashboard. Controllers receive real-time updates on site safety and personnel status.</p>
                    </div>
                </div>
            </div>

            <section class="cta-box" data-aos="zoom-out">
                <h2 class="fw-bold mb-4">Ready to Secure Your Assets?</h2>
                <p class="text-dim mb-5">Experience the architecture of the future. Professional security management, simplified.</p>
                <a href="{{ route('login') }}" class="btn-elite btn-glow">
                    ACCESS SYSTEM <i class="fas fa-shield-halved"></i>
                </a>
            </section>
        </main>

        <footer class="py-5 text-center text-white-50 small border-top border-secondary border-opacity-10">
            <p>&copy; {{ date('Y') }} Elite Guard Operational Systems.</p>
        </footer>

        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init({ once: true });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
