@extends('layouts.frontend.main')
@section('title', 'About Us - Elite Guard')

@section('content')
<style>
    .about-container {
        padding: 120px 0 80px;
    }

    .vision-card {
        background: var(--bg-card);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 40px;
        padding: 60px;
        margin-bottom: 80px;
        position: relative;
        overflow: hidden;
    }

    .vision-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle at center, var(--primary-glow) 0%, transparent 70%);
        opacity: 0.1;
    }

    .value-item {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--glass-border);
        padding: 40px;
        border-radius: 30px;
        height: 100%;
        transition: all 0.3s ease;
    }

    .value-item:hover {
        border-color: var(--primary);
        transform: translateY(-10px);
    }

    .value-icon {
        font-size: 2.5rem;
        color: var(--primary);
        margin-bottom: 25px;
        display: block;
    }

    .value-item h3 {
        color: var(--text-main);
        font-weight: 800;
        margin-bottom: 15px;
    }

    .value-item p {
        color: var(--text-dim);
        font-size: 0.95rem;
        line-height: 1.7;
    }

    .mission-text {
        font-size: 1.5rem;
        color: var(--text-main);
        font-weight: 600;
        line-height: 1.5;
        margin-bottom: 30px;
    }
</style>

<div class="about-container">
    <div class="container">
        <div class="vision-card" data-aos="zoom-in">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="badge-os">Our Mission</span>
                    <h1 class="title-hq text-glow">Securing the Future.</h1>
                    <p class="mission-text">At Elite Guard, we don't just provide security; we architect comprehensive protection ecosystems for high-stakes environments.</p>
                    <p class="desc-hq mb-0">Founded on the principles of tactical precision and digital innovation, Elite Guard has evolved from a local patrol service into a multi-tier security infrastructure provider based in Calgary, Alberta.</p>
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <i class="fas fa-shield-halved text-primary" style="font-size: 15rem; opacity: 0.2;"></i>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="value-item" data-aos="fade-up" data-aos-delay="100">
                    <i class="fas fa-handshake-angle value-icon"></i>
                    <h3>Integrity</h3>
                    <p>Unwavering commitment to ethical conduct and transparency in every operation we undertake.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-item" data-aos="fade-up" data-aos-delay="200">
                    <i class="fas fa-eye value-icon"></i>
                    <h3>Vigilance</h3>
                    <p>Constant, proactive monitoring to neutralize threats before they materialize into risks.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-item" data-aos="fade-up" data-aos-delay="300">
                    <i class="fas fa-microchip value-icon"></i>
                    <h3>Innovation</h3>
                    <p>Leveraging cutting-edge OS and NFC technology to provide data-driven security solutions.</p>
                </div>
            </div>
        </div>

        <div class="mt-5 pt-5 text-center" data-aos="fade-up">
            <h2 class="title-hq" style="font-size: 2.5rem;">The Headquarters</h2>
            <p class="desc-hq mx-auto">Operating from our central hub in Calgary, we manage a diverse portfolio of industrial, commercial, and residential assets across the province.</p>
        </div>
    </div>
</div>
@endsection
