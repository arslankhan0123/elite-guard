@extends('layouts.frontend.main')
@section('title', 'Operational FAQ - Elite Guard')

@section('content')
<style>
    .faq-container {
        padding: 120px 0 80px;
    }

    .faq-item {
        background: var(--bg-card);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        margin-bottom: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .faq-item:hover {
        border-color: var(--primary);
    }

    .faq-question {
        padding: 25px 35px;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: 700;
        color: var(--text-main);
        font-size: 1.2rem;
        text-align: center;
        position: relative;
    }

    .faq-answer {
        padding: 0 35px 35px;
        color: var(--text-dim);
        line-height: 1.8;
        display: none;
        text-align: center;
        max-width: 800px;
        margin: 0 auto;
    }

    .faq-item.active .faq-answer {
        display: block;
    }

    .faq-item.active .faq-question i {
        transform: rotate(180deg);
        color: var(--primary);
    }

    .faq-question i {
        transition: transform 0.3s ease;
        font-size: 0.9rem;
        position: absolute;
        right: 35px;
    }

    .faq-categories {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 50px;
        flex-wrap: wrap;
    }

    .category-btn {
        padding: 10px 25px;
        border-radius: 50px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        color: var(--text-dim);
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .category-btn.active {
        background: var(--primary);
        color: white;
        border-color: transparent;
        box-shadow: 0 10px 20px var(--primary-glow);
    }
</style>

<div class="faq-container py-hq">
    <div class="container">
        <div class="faq-hero text-center" data-aos="fade-down">
            <span class="badge-os">Intelligence Database</span>
            <h1 class="title-hq text-glow">Operational FAQ.</h1>
            <p class="desc-hq mx-auto">Get detailed intelligence on how Elite Guard operates and how we protect your assets.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="faq-item" data-aos="fade-up">
                    <div class="faq-question">
                        What areas do you provide security coverage for?
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        We currently provide primary coverage in Calgary and surrounding areas in Alberta. Our tactical network is expanding to other major Canadian hubs. We specialize in industrial sites, commercial properties, and high-value residential complexes.
                    </div>
                </div>

                <div class="faq-item" data-aos="fade-up">
                    <div class="faq-question">
                        How does the NFC patrol tracking work?
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        We install encrypted NFC (Near Field Communication) tags at strategic locations throughout your site. Our guards must physically scan these tags with their operational devices to confirm their presence. This data is synced in real-time to your dashboard, providing 100% accountability.
                    </div>
                </div>

                <div class="faq-item" data-aos="fade-up">
                    <div class="faq-question">
                        What is your average emergency response time?
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        For sites under active monitoring, our tactical response units typically arrive on-site within 5 to 8 minutes. We maintain dedicated patrol units in key zones to ensure rapid neutralization of threats.
                    </div>
                </div>

                <div class="faq-item" data-aos="fade-up">
                    <div class="faq-question">
                        Are your guards licensed and insured?
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Yes, every Elite Guard operative is licensed under the Alberta Security Services and Investigators Act (SSIA). We maintain comprehensive liability insurance and follow all provincial training mandates.
                    </div>
                </div>

                <div class="faq-item" data-aos="fade-up">
                    <div class="faq-question">
                        Do you provide 24/7 client dashboards?
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Absolutely. Clients have 24/7 access to our cloud-based Operational OS. You can view real-time patrol logs, download incident reports, and monitor active guard locations from any device.
                    </div>
                </div>

                <div class="faq-item" data-aos="fade-up">
                    <div class="faq-question">
                        What equipment do your guards carry?
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Our guards are equipped with tactical uniforms, high-grade radio communication devices, NFC-enabled operational smartphones, and body cams for high-risk sites. We do not carry firearms; we focus on de-escalation and rapid neutralization through technology and physical presence.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const faqItems = document.querySelectorAll('.faq-item');
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            question.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                
                // Close all other items
                faqItems.forEach(otherItem => {
                    otherItem.classList.remove('active');
                });
                
                // Toggle current item
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });
    });
</script>
@endsection
