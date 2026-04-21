@extends('layouts.frontend.main')
@section('title', 'Contact Us - Elite Guard')

@section('content')
<style>
    .contact-container {
        padding: 120px 0 80px;
    }

    .contact-form-card {
        background: var(--bg-card);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 35px;
        padding: 50px;
        height: 100%;
    }

    .form-group-elite {
        margin-bottom: 25px;
    }

    .form-label-elite {
        display: block;
        color: var(--text-dim);
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 10px;
        letter-spacing: 1px;
    }

    .form-control-elite {
        width: 100%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        border-radius: 15px;
        padding: 15px 20px;
        color: white;
        transition: all 0.3s ease;
    }

    .form-control-elite:focus {
        background: rgba(255, 255, 255, 0.08);
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 20px var(--primary-glow);
    }

    .contact-info-card {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 35px;
        padding: 50px;
        color: white;
        height: 100%;
        box-shadow: 0 20px 40px var(--primary-glow);
    }

    .info-item {
        display: flex;
        gap: 20px;
        margin-bottom: 40px;
    }

    .info-icon {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .info-text h4 {
        font-weight: 800;
        margin-bottom: 5px;
    }

    .info-text p {
        opacity: 0.8;
        font-size: 0.95rem;
        margin: 0;
    }

    .btn-send {
        width: 100%;
        padding: 18px;
        border-radius: 15px;
        background: var(--primary);
        border: none;
        color: white;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        transition: all 0.3s ease;
    }

    .btn-send:hover {
        background: white;
        color: var(--primary);
        transform: translateY(-5px);
    }
</style>

<div class="contact-container">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-down">
            <span class="badge-os">Open Communications</span>
            <h1 class="title-hq text-glow">Establish Contact.</h1>
            <p class="desc-hq mx-auto">Our tactical coordination center is available 24/7 for urgent security consultations.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-lg-7">
                <div class="contact-form-card" data-aos="fade-right">
                    <form action="#">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-elite">
                                    <label class="form-label-elite">Full Name</label>
                                    <input type="text" class="form-control-elite" placeholder="Operational Lead Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-elite">
                                    <label class="form-label-elite">Email Address</label>
                                    <input type="email" class="form-control-elite" placeholder="secure-mail@domain.com">
                                </div>
                            </div>
                        </div>
                        <div class="form-group-elite">
                            <label class="form-label-elite">Service Required</label>
                            <select class="form-control-elite" style="appearance: none;">
                                <option>Industrial Protection</option>
                                <option>Mobile Patrols</option>
                                <option>Executive Protection</option>
                                <option>Emergency Consultation</option>
                            </select>
                        </div>
                        <div class="form-group-elite">
                            <label class="form-label-elite">Brief Intelligence</label>
                            <textarea class="form-control-elite" rows="5" placeholder="Describe the security requirements..."></textarea>
                        </div>
                        <button type="submit" class="btn-send">Initiate Transmission</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="contact-info-card" data-aos="fade-left">
                    <h2 class="fw-900 mb-5">Tactical Hub</h2>
                    
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-location-dot"></i></div>
                        <div class="info-text">
                            <h4>Headquarters</h4>
                            <p>3961 52 Ave, NE #2104<br>Calgary, AB T3J0J8</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-phone"></i></div>
                        <div class="info-text">
                            <h4>Direct Line</h4>
                            <p>+1 403-830-7772</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-envelope"></i></div>
                        <div class="info-text">
                            <h4>Operations Mail</h4>
                            <p>info@eliteguardinc.ca</p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <h4 class="mb-3 fw-bold">Live Status</h4>
                        <div class="d-flex align-items-center gap-2">
                            <div class="spinner-grow text-white" role="status" style="width: 1rem; height: 1rem;"></div>
                            <span class="small fw-bold">OPERATIONAL HUB ACTIVE (24/7)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
