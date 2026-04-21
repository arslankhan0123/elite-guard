@extends('layouts.frontend.main')
@section('title', 'Security Protocol - Elite Guard')

@section('content')
<style>
    .protocol-container {
        padding: 120px 0 80px;
    }

    .protocol-card {
        background: var(--bg-card);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 35px;
        padding: 50px;
        margin-bottom: 40px;
        scroll-margin-top: 120px;
    }

    .protocol-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .protocol-title {
        font-size: 3.5rem;
        font-weight: 950;
        letter-spacing: -2px;
        background: linear-gradient(to right, #fff, var(--primary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 20px;
    }

    .protocol-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }

    .protocol-item {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--glass-border);
        padding: 40px;
        border-radius: 25px;
        transition: all 0.3s ease;
    }

    .protocol-item:hover {
        border-color: var(--primary);
        transform: translateY(-10px);
        background: rgba(139, 92, 246, 0.05);
    }

    .protocol-icon {
        width: 60px;
        height: 60px;
        background: var(--primary);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-bottom: 25px;
        box-shadow: 0 10px 20px var(--primary-glow);
    }

    .protocol-item h3 {
        color: var(--text-main);
        font-weight: 800;
        margin-bottom: 15px;
        font-size: 1.3rem;
    }

    .protocol-item p {
        color: var(--text-dim);
        font-size: 0.95rem;
        line-height: 1.7;
    }

    @media (max-width: 768px) {
        .protocol-title {
            font-size: 2.5rem;
        }
    }
</style>

<div class="protocol-container py-hq">
    <div class="container">
        <div class="protocol-header" data-aos="fade-down">
            <span class="badge-os">Elite Security Ops</span>
            <h1 class="protocol-title">Security Protocols</h1>
            <p class="desc-hq mx-auto">Standard operating procedures for maximum asset protection and crisis neutralization.</p>
        </div>

        <div class="protocol-grid">
            <div class="protocol-item" data-aos="fade-up" data-aos-delay="100">
                <div class="protocol-icon"><i class="fas fa-eye"></i></div>
                <h3>Active Surveillance</h3>
                <p>24/7 high-definition monitoring synchronized with on-site patrol units to ensure continuous visibility of all tactical zones.</p>
            </div>

            <div class="protocol-item" data-aos="fade-up" data-aos-delay="200">
                <div class="protocol-icon"><i class="fas fa-bolt"></i></div>
                <h3>Rapid Response</h3>
                <p>Immediate mobilization of tactical units upon any security exception or breach detection. Average response time under 5 minutes.</p>
            </div>

            <div class="protocol-item" data-aos="fade-up" data-aos-delay="300">
                <div class="protocol-icon"><i class="fas fa-fingerprint"></i></div>
                <h3>Biometric Access</h3>
                <p>Strict identity verification using cutting-edge biometric hardware for all sensitive entry points and restricted operational areas.</p>
            </div>

            <div class="protocol-item" data-aos="fade-up" data-aos-delay="400">
                <div class="protocol-icon"><i class="fas fa-satellite"></i></div>
                <h3>NFC Synchronization</h3>
                <p>Real-time patrol tracking using NFC-enabled checkpoints, ensuring every square inch of your site is physically inspected on schedule.</p>
            </div>

            <div class="protocol-item" data-aos="fade-up" data-aos-delay="500">
                <div class="protocol-icon"><i class="fas fa-file-shield"></i></div>
                <h3>Encrypted Reporting</h3>
                <p>All incident reports and operational logs are stored with AES-256 encryption, accessible only through secure management portals.</p>
            </div>

            <div class="protocol-item" data-aos="fade-up" data-aos-delay="600">
                <div class="protocol-icon"><i class="fas fa-users-gear"></i></div>
                <h3>Crisis Management</h3>
                <p>Structured communication hierarchies for emergency situations, including direct lines to local law enforcement and emergency services.</p>
            </div>
        </div>
    </div>
</div>
@endsection
