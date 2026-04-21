@extends('layouts.frontend.main')
@section('title', 'Our Services - Elite Guard')

@section('content')
<style>
    .services-container {
        padding: 120px 0 80px;
    }

    .service-detailed-card {
        background: var(--bg-card);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 35px;
        padding: 50px;
        margin-bottom: 40px;
        display: flex;
        gap: 40px;
        align-items: center;
        transition: all 0.4s ease;
    }

    .service-detailed-card:hover {
        border-color: var(--primary);
        transform: translateY(-5px);
        background: rgba(15, 23, 42, 0.8);
    }

    .service-img-placeholder {
        width: 150px;
        height: 150px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: white;
        flex-shrink: 0;
    }

    .service-info h2 {
        color: var(--text-main);
        font-weight: 850;
        margin-bottom: 15px;
        font-size: 2rem;
    }

    .service-info p {
        color: var(--text-dim);
        font-size: 1.05rem;
        line-height: 1.7;
        margin-bottom: 20px;
    }

    .service-features {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .feature-tag {
        background: rgba(139, 92, 246, 0.1);
        border: 1px solid rgba(139, 92, 246, 0.2);
        color: var(--primary);
        padding: 6px 15px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    @media (max-width: 768px) {
        .service-detailed-card {
            flex-direction: column;
            text-align: center;
            padding: 30px;
        }
        .service-features {
            justify-content: center;
        }
    }
</style>

<div class="services-container py-hq">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-down">
            <span class="badge-os">Operational Spectrum</span>
            <h1 class="title-hq text-glow">Elite Protection Services.</h1>
            <p class="desc-hq mx-auto">Tactical security solutions engineered for accuracy, accountability, and maximum deterrence.</p>
        </div>

        <div class="service-detailed-card" data-aos="fade-up" data-aos-delay="100">
            <div class="service-img-placeholder">
                <i class="fas fa-building-shield"></i>
            </div>
            <div class="service-info">
                <h2>Industrial & Commercial</h2>
                <p>Advanced site protection for manufacturing hubs, warehouses, and corporate headquarters. We specialize in access control and loss prevention strategies.</p>
                <div class="service-features">
                    <span class="feature-tag">Access Control</span>
                    <span class="feature-tag">Loss Prevention</span>
                    <span class="feature-tag">HD Surveillance</span>
                </div>
            </div>
        </div>

        <div class="service-detailed-card" data-aos="fade-up" data-aos-delay="200">
            <div class="service-img-placeholder" style="background: linear-gradient(135deg, #10b981, #3b82f6);">
                <i class="fas fa-car-rear"></i>
            </div>
            <div class="service-info">
                <h2>Mobile Tactical Patrols</h2>
                <p>Highly visible vehicle patrols equipped with real-time GPS tracking and NFC checkpoint synchronization for comprehensive site verification.</p>
                <div class="service-features">
                    <span class="feature-tag">NFC Scans</span>
                    <span class="feature-tag">GPS Tracking</span>
                    <span class="feature-tag">Incident Logging</span>
                </div>
            </div>
        </div>

        <div class="service-detailed-card" data-aos="fade-up" data-aos-delay="300">
            <div class="service-img-placeholder" style="background: linear-gradient(135deg, #f59e0b, #ef4444);">
                <i class="fas fa-user-secret"></i>
            </div>
            <div class="service-info">
                <h2>VIP & Executive Protection</h2>
                <p>Personalized protection details for high-net-worth individuals and corporate executives, focusing on discretion and threat mitigation.</p>
                <div class="service-features">
                    <span class="feature-tag">Discreet Ops</span>
                    <span class="feature-tag">Threat Mitigation</span>
                    <span class="feature-tag">Secure Transit</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
