@extends('layouts.frontend.main')
@section('title', 'Terms & Conditions - Elite Guard')

@section('content')
<style>
    .policy-container {
        padding: 120px 0 80px;
        min-height: 100vh;
    }

    .policy-sidebar {
        position: sticky;
        top: 100px;
        background: var(--bg-card);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 25px;
        padding: 30px;
        height: fit-content;
    }

    .policy-nav-link {
        display: block;
        color: var(--text-dim);
        text-decoration: none;
        padding: 12px 15px;
        border-radius: 12px;
        margin-bottom: 5px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .policy-nav-link:hover, .policy-nav-link.active {
        background: rgba(139, 92, 246, 0.1);
        color: var(--primary);
        transform: translateX(5px);
    }

    .policy-card {
        background: var(--bg-card);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 35px;
        padding: 50px;
        margin-bottom: 40px;
        transition: border-color 0.3s ease;
        scroll-margin-top: 120px;
    }

    .policy-card:hover {
        border-color: rgba(139, 92, 246, 0.3);
    }

    .policy-title {
        font-size: 2.2rem;
        font-weight: 850;
        margin-bottom: 30px;
        background: linear-gradient(to right, #fff, var(--primary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .policy-section-title {
        color: var(--primary);
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 0.85rem;
        margin-bottom: 15px;
        display: block;
    }

    .policy-content {
        color: var(--text-dim);
        line-height: 1.8;
    }

    .policy-content h3 {
        color: var(--text-main);
        font-weight: 700;
        margin: 40px 0 20px;
        font-size: 1.4rem;
    }

    .policy-list {
        list-style: none;
        padding: 0;
        margin-bottom: 30px;
    }

    .policy-list li {
        position: relative;
        padding-left: 30px;
        margin-bottom: 15px;
    }

    .policy-list li::before {
        content: '\f14a';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        left: 0;
        color: var(--primary);
        font-size: 0.9rem;
    }

    .acknowledgment-box {
        background: rgba(139, 92, 246, 0.05);
        border: 1px solid rgba(139, 92, 246, 0.2);
        padding: 30px;
        border-radius: 20px;
        margin-top: 40px;
    }

    .company-footer {
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid var(--glass-border);
        font-size: 0.85rem;
        color: var(--text-dim);
    }

    @media (max-width: 991px) {
        .policy-sidebar {
            position: relative;
            top: 0;
            margin-bottom: 40px;
        }
    }
</style>

<div class="policy-container">
    <div class="container">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3">
                <div class="policy-sidebar" data-aos="fade-right">
                    <span class="policy-section-title">Legal Menu</span>
                    <a href="#introduction" class="policy-nav-link active">Introduction</a>
                    <a href="#definitions" class="policy-nav-link">Definitions</a>
                    <a href="#services" class="policy-nav-link">Scope of Services</a>
                    <a href="#billing" class="policy-nav-link">Billing & Payment</a>
                    <a href="#obligations" class="policy-nav-link">Client Obligations</a>
                    <a href="#confidentiality" class="policy-nav-link">Confidentiality</a>
                    <a href="#liability" class="policy-nav-link">Liability & Indemnity</a>
                    <a href="#termination" class="policy-nav-link">Termination</a>
                    <a href="#general" class="policy-nav-link">General Provisions</a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-lg-9">
                <!-- 1. Introduction -->
                <section id="introduction" class="policy-card" data-aos="fade-up">
                    <span class="policy-section-title">Section 01</span>
                    <h2 class="policy-title">Terms of Service</h2>
                    <div class="policy-content">
                        <p>Welcome to Elite Guard Inc. By engaging our services, you agree to be bound by these Terms and Conditions. These terms constitute a legally binding agreement between Elite Guard Inc. ("Company", "We", "Us") and the client ("Client", "You") receiving security services.</p>
                        <p>Our operations are governed by the Security Services and Investigators Act (SSIA) of Alberta. We are committed to maintaining the highest ethical standards and tactical excellence.</p>
                    </div>
                </section>

                <!-- 2. Definitions -->
                <section id="definitions" class="policy-card" data-aos="fade-up">
                    <span class="policy-section-title">Section 02</span>
                    <h2 class="policy-title">Definitions</h2>
                    <div class="policy-content">
                        <ul class="policy-list">
                            <li><strong>Services:</strong> Security personnel, patrol management, tactical monitoring, and operational protection as defined in the Work Order.</li>
                            <li><strong>Site:</strong> The physical location or property designated by the Client for security coverage.</li>
                            <li><strong>Incident:</strong> Any security breach, medical emergency, or site-specific event requiring documentation.</li>
                            <li><strong>Confidential Information:</strong> Any non-public operational data, security protocols, or financial details shared between parties.</li>
                        </ul>
                    </div>
                </section>

                <!-- 3. Scope of Services -->
                <section id="services" class="policy-card" data-aos="fade-up">
                    <span class="policy-section-title">Section 03</span>
                    <h2 class="policy-title">Scope of Services</h2>
                    <div class="policy-content">
                        <p>Elite Guard Inc. provides military-grade security infrastructure and personnel management. Services are performed by licensed guards compliant with SSIA standards.</p>
                        <ul class="policy-list">
                            <li><strong>Tactical Deployment:</strong> Guards are deployed based on the agreed risk assessment and site instructions.</li>
                            <li><strong>Monitoring:</strong> Includes NFC checkpoint synchronization and real-time incident reporting through our OS.</li>
                            <li><strong>Professional Conduct:</strong> All personnel adhere to our internal Code of Conduct and Zero-Tolerance Harassment policies.</li>
                        </ul>
                    </div>
                </section>

                <!-- 4. Billing & Payment -->
                <section id="billing" class="policy-card" data-aos="fade-up">
                    <span class="policy-section-title">Section 04</span>
                    <h2 class="policy-title">Billing & Payment Terms</h2>
                    <div class="policy-content">
                        <ul class="policy-list">
                            <li><strong>Rates:</strong> Services are billed at the tactical hourly rates specified in your individual Service Agreement.</li>
                            <li><strong>Invoicing:</strong> Invoices are issued [Weekly/Bi-Weekly/Monthly] and are due within 15 days.</li>
                            <li><strong>Late Fees:</strong> Overdue accounts incur a 2.5% monthly interest charge to cover operational financing costs.</li>
                            <li><strong>Suspension:</strong> Service may be immediately suspended for accounts overdue by more than 30 days without notice.</li>
                        </ul>
                    </div>
                </section>

                <!-- 5. Client Obligations -->
                <section id="obligations" class="policy-card" data-aos="fade-up">
                    <span class="policy-section-title">Section 05</span>
                    <h2 class="policy-title">Client Obligations</h2>
                    <div class="policy-content">
                        <p>To ensure maximum protection, the Client must:</p>
                        <ul class="policy-list">
                            <li><strong>Operational Access:</strong> Ensure guards have unobstructed access to all patrol zones and equipment.</li>
                            <li><strong>Hazard Disclosure:</strong> Immediately notify our safety officer of any new site hazards or threats.</li>
                            <li><strong>Safe Environment:</strong> Maintain the site in compliance with Alberta OHS standards for our personnel.</li>
                            <li><strong>Support:</strong> Provide a designated site lead for emergency communication and shift briefing.</li>
                        </ul>
                    </div>
                </section>

                <!-- 6. Confidentiality -->
                <section id="confidentiality" class="policy-card" data-aos="fade-up">
                    <span class="policy-section-title">Section 06</span>
                    <h2 class="policy-title">Confidentiality Agreement</h2>
                    <div class="policy-content">
                        <p>Mutual confidentiality is critical for security operations. Both parties agree to:</p>
                        <ul class="policy-list">
                            <li><strong>Non-Disclosure:</strong> Not reveal security protocols, site vulnerabilities, or operational data to third parties.</li>
                            <li><strong>Data Protection:</strong> Maintain strict digital security for all incident reports and employee records.</li>
                            <li><strong>Post-Termination:</strong> Confidentiality obligations continue for five (5) years after the service ends.</li>
                        </ul>
                    </div>
                </section>

                <!-- 7. Liability & Indemnity -->
                <section id="liability" class="policy-card" data-aos="fade-up">
                    <span class="policy-section-title">Section 07</span>
                    <h2 class="policy-title">Liability & Indemnity</h2>
                    <div class="policy-content">
                        <p>Elite Guard Inc. maintains professional tactical liability insurance. However:</p>
                        <ul class="policy-list">
                            <li><strong>Client Responsibility:</strong> Clients must maintain primary property and general liability insurance.</li>
                            <li><strong>Limitations:</strong> We are not liable for losses resulting from Client negligence or failure to disclose hazards.</li>
                            <li><strong>Maximum Cap:</strong> Liability for any claim is capped at the total service fees paid during the month of the incident.</li>
                        </ul>
                    </div>
                </section>

                <!-- 8. Termination -->
                <section id="termination" class="policy-card" data-aos="fade-up">
                    <span class="policy-section-title">Section 08</span>
                    <h2 class="policy-title">Termination & Cancellation</h2>
                    <div class="policy-content">
                        <ul class="policy-list">
                            <li><strong>Notice:</strong> Either party may terminate with 30 days' written notice.</li>
                            <li><strong>Breach:</strong> Immediate termination for breach of SSIA, OHS, or Confidentiality terms.</li>
                            <li><strong>Shift Cancellation:</strong> Must be made by **calling dispatch directly**. Text or email is not valid.</li>
                            <li><strong>Short-Notice Fee:</strong> Shifts cancelled with less than 24 hours' notice are billed at 100% value.</li>
                        </ul>
                    </div>
                </section>

                <!-- 9. General Provisions -->
                <section id="general" class="policy-card" data-aos="fade-up" style="margin-bottom: 0;">
                    <span class="policy-section-title">Section 09</span>
                    <h2 class="policy-title">General Provisions</h2>
                    <div class="policy-content">
                        <ul class="policy-list">
                            <li><strong>Jurisdiction:</strong> Governed by the laws of Alberta and the Security Services and Investigators Act.</li>
                            <li><strong>Force Majeure:</strong> Performance is excused in cases of extreme natural disasters or war.</li>
                            <li><strong>Updates:</strong> We may update these terms with 14 days' notice to reflect regulatory changes.</li>
                        </ul>

                        <div class="acknowledgment-box">
                            <h5>Legal Acknowledgment</h5>
                            <p class="mb-0">Engagement of Elite Guard Inc. services constitutes full acceptance of these Terms & Conditions. All agreements are subject to annual review.</p>
                        </div>
                    </div>
                </section>

                <!-- Company Footer -->
                <div class="company-footer text-center">
                    <p class="mb-1"><strong>ELITE GUARD INC. LEGAL DEPARTMENT</strong></p>
                    <p class="mb-1">3961 52 Ave, NE #2104, Calgary, AB T3J0J8</p>
                    <p class="mb-0">Contact for Legal Inquiries: saqib@eliteguardinc.ca | +1 403-830-7772</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.policy-nav-link');
        const sections = document.querySelectorAll('section');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 150) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active');
                }
            });
        });
    });
</script>
@endsection