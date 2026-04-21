@extends('layouts.frontend.main')
@section('title', 'Career Portal - Elite Guard')

@section('content')
<style>
    .career-container {
        padding: 120px 0 80px;
    }

    .career-hero {
        text-align: center;
        margin-bottom: 80px;
    }

    .job-card {
        background: var(--bg-card);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 30px;
        padding: 40px;
        margin-bottom: 30px;
        transition: all 0.4s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .job-card:hover {
        border-color: var(--primary);
        background: rgba(139, 92, 246, 0.05);
        transform: scale(1.02);
    }

    .job-info h3 {
        color: var(--text-main);
        font-weight: 800;
        margin-bottom: 10px;
    }

    .job-meta {
        display: flex;
        gap: 20px;
        color: var(--text-dim);
        font-size: 0.85rem;
    }

    .job-meta span {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .job-meta i {
        color: var(--primary);
    }

    .btn-apply {
        padding: 12px 30px;
        border-radius: 12px;
        background: var(--primary);
        color: white;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .btn-apply:hover {
        background: white;
        color: var(--bg-dark);
        transform: translateY(-3px);
    }

    .perks-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-top: 80px;
    }

    .perk-item {
        text-align: center;
        padding: 30px;
    }

    .perk-icon {
        font-size: 2.5rem;
        color: var(--primary);
        margin-bottom: 20px;
        display: block;
    }

    .perk-item h4 {
        color: var(--text-main);
        font-weight: 700;
        margin-bottom: 10px;
    }

    .perk-item p {
        color: var(--text-dim);
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .job-card {
            text-align: center;
            justify-content: center;
        }
        .job-meta {
            justify-content: center;
            flex-direction: column;
            gap: 5px;
        }
    }
</style>

<div class="career-container">
    <div class="container">
        <div class="career-hero" data-aos="fade-down">
            <span class="badge-os">Recruitment Phase active</span>
            <h1 class="title-hq text-glow">Join the Elite.</h1>
            <p class="desc-hq mx-auto">We are looking for disciplined, highly-skilled individuals to join our tactical security network.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="job-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="job-info">
                        <h3>Tactical Security Specialist</h3>
                        <div class="job-meta">
                            <span><i class="fas fa-location-dot"></i> Calgary, AB</span>
                            <span><i class="fas fa-clock"></i> Full-Time</span>
                            <span><i class="fas fa-vault"></i> $22 - $28 / hr</span>
                        </div>
                    </div>
                    <a href="mailto:careers@eliteguardinc.ca" class="btn-apply">Apply Now</a>
                </div>

                <div class="job-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="job-info">
                        <h3>Mobile Patrol Lead</h3>
                        <div class="job-meta">
                            <span><i class="fas fa-location-dot"></i> NE Calgary</span>
                            <span><i class="fas fa-clock"></i> Night Shift</span>
                            <span><i class="fas fa-vault"></i> $25 - $30 / hr</span>
                        </div>
                    </div>
                    <a href="mailto:careers@eliteguardinc.ca" class="btn-apply">Apply Now</a>
                </div>

                <div class="job-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="job-info">
                        <h3>Operations Coordinator</h3>
                        <div class="job-meta">
                            <span><i class="fas fa-location-dot"></i> Headquarters</span>
                            <span><i class="fas fa-clock"></i> Hybrid</span>
                            <span><i class="fas fa-vault"></i> Competitive Salary</span>
                        </div>
                    </div>
                    <a href="mailto:careers@eliteguardinc.ca" class="btn-apply">Apply Now</a>
                </div>
            </div>
        </div>

        <div class="perks-grid">
            <div class="perk-item" data-aos="zoom-in" data-aos-delay="100">
                <i class="fas fa-shield-heart perk-icon"></i>
                <h4>Premium Benefits</h4>
                <p>Full health, dental, and vision coverage for you and your family.</p>
            </div>
            <div class="perk-item" data-aos="zoom-in" data-aos-delay="200">
                <i class="fas fa-graduation-cap perk-icon"></i>
                <h4>Continuous Training</h4>
                <p>Advanced tactical training and certification sponsorship programs.</p>
            </div>
            <div class="perk-item" data-aos="zoom-in" data-aos-delay="300">
                <i class="fas fa-chart-line perk-icon"></i>
                <h4>Rapid Growth</h4>
                <p>Performance-based career tracks with clear leadership opportunities.</p>
            </div>
        </div>
    </div>
</div>
@endsection
