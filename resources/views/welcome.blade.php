@extends('layouts.frontend.main')
@section('title', 'Home')

@section('content')
<main class="hero-section">
    <div class="container mt-5">
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
@endsection