@extends('layouts.frontend.main')
@section('title', 'Architecture - Elite Guard')

@section('content')
<style>
    .arch-container {
        padding: 120px 0 80px;
    }

    .header-section {
        padding: 60px 0;
        text-align: center;
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

    @media (max-width: 768px) {
        .arch-timeline::before { left: 20px; }
        .timeline-dot { left: 20px; }
        .timeline-content { width: 85%; margin-left: 50px; }
        .timeline-item { width: 100% !important; justify-content: flex-start !important; }
        .cta-box { padding: 40px 20px; }
    }
</style>

<div class="arch-container py-hq">
    <header class="header-section container">
        <div class="badge-os" data-aos="fade-down">Infrastructure Protocol</div>
        <h1 class="title-hq text-glow" data-aos="zoom-in">The Ecosystem of <br>Supreme Control.</h1>
        <p class="desc-hq mx-auto" data-aos="fade-up">Elite Guard bridges the gap between physical patrol and digital intelligence through a multi-tier encrypted architecture.</p>
    </header>

    <main class="container">
        <div class="arch-timeline">
            <!-- Step 1 -->
            <div class="timeline-item" data-aos="fade-right">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <span class="step-number">01</span>
                    <div class="icon-wrapper"><i class="fas fa-microchip"></i></div>
                    <h4 class="fw-bold text-white">NFC Deployment</h4>
                    <p class="text-dim small mb-0">Physical encrypted NFC tags are deployed at tactical sites. Each tag is cryptographically paired with its specific geographical zone.</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="timeline-item" data-aos="fade-left">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <span class="step-number">02</span>
                    <div class="icon-wrapper"><i class="fas fa-mobile-screen-button"></i></div>
                    <h4 class="fw-bold text-white">Mobile Interaction</h4>
                    <p class="text-dim small mb-0">Patrol guards utilize the Elite Guard Mobile App to perform high-frequency scans. The app captures NFC telemetry and GPS coordinates.</p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="timeline-item" data-aos="fade-right">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <span class="step-number">03</span>
                    <div class="icon-wrapper"><i class="fas fa-cloud-bolt"></i></div>
                    <h4 class="fw-bold text-white">Cloud Verification</h4>
                    <p class="text-dim small mb-0">Payloads are transmitted to the Laravel API. The system verifies the UID integrity, guard credentials, and time-sync parameters.</p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="timeline-item" data-aos="fade-left">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <span class="step-number">04</span>
                    <div class="icon-wrapper"><i class="fas fa-chart-line"></i></div>
                    <h4 class="fw-bold text-white">Live intelligence</h4>
                    <p class="text-dim small mb-0">Verified data is instantly broadcast to the Administrative Dashboard. Controllers receive real-time updates on site safety and personnel status.</p>
                </div>
            </div>
        </div>

        <section class="cta-box" data-aos="zoom-out">
            <h2 class="title-hq mb-4" style="font-size: 2.5rem;">Ready to Secure Your Assets?</h2>
            <p class="desc-hq mx-auto mb-5">Experience the architecture of the future. Professional security management, simplified.</p>
            <a href="{{ route('login') }}" class="btn-action-hub btn-primary-hub">
                INITIALIZE PROTOCOL <i class="fas fa-shield-halved"></i>
            </a>
        </section>
    </main>
</div>
@endsection
