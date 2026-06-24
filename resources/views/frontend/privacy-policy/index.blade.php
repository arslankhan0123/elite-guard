@extends('layouts.frontend.main')
@section('title', 'Privacy Policy - Elite Guard')

@section('content')
<style>
    .privacy-container {
        padding: 120px 0 80px;
        min-height: 100vh;
    }

    .privacy-policy {
        max-width: 100%;
        margin: 0 auto;
        background: var(--bg-card);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 30px;
        padding: 50px;
    }

    .privacy-kicker {
        color: var(--primary);
        display: block;
        font-size: 0.85rem;
        font-weight: 800;
        letter-spacing: 2px;
        margin-bottom: 16px;
        text-transform: uppercase;
    }

    .privacy-title {
        background: linear-gradient(to right, #fff, var(--primary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 2.45rem;
        font-weight: 850;
        margin-bottom: 18px;
    }

    .privacy-updated {
        color: var(--text-dim);
        font-size: 0.95rem;
        margin-bottom: 34px;
    }

    .privacy-content {
        color: var(--text-dim);
        line-height: 1.8;
    }

    .privacy-content h3 {
        color: var(--text-main);
        font-size: 1.35rem;
        font-weight: 750;
        margin: 34px 0 16px;
    }

    .privacy-content p {
        margin-bottom: 18px;
    }

    .privacy-list {
        list-style: none;
        margin: 0 0 24px;
        padding: 0;
    }

    .privacy-list li {
        margin-bottom: 13px;
        padding-left: 30px;
        position: relative;
    }

    .privacy-list li::before {
        color: var(--primary);
        content: '\f058';
        font-family: 'Font Awesome 6 Free';
        font-size: 0.9rem;
        font-weight: 900;
        left: 0;
        position: absolute;
        top: 2px;
    }

    .privacy-contact {
        background: rgba(16, 185, 129, 0.05);
        border: 1px solid rgba(16, 185, 129, 0.2);
        border-radius: 18px;
        margin-top: 36px;
        padding: 28px;
    }

    .privacy-contact h3 {
        color: var(--accent);
        margin-top: 0;
    }

    @media (max-width: 767px) {
        .privacy-policy {
            border-radius: 22px;
            padding: 30px 22px;
        }

        .privacy-title {
            font-size: 1.85rem;
        }
    }
</style>

<div class="privacy-container">
    <div class="container">
        <article class="privacy-policy" data-aos="fade-up">
            <span class="privacy-kicker">Elite Guard Inc.</span>
            <h1 class="privacy-title">Privacy Policy for Elite Guard App</h1>
            <p class="privacy-updated"><strong>Effective date:</strong> June 24, 2026</p>

            <div class="privacy-content">
                <p>
                    This Privacy Policy explains how Elite Guard Inc. collects, uses, discloses, stores, and protects information when employees, contractors, clients, or authorized users access the Elite Guard mobile application, website, dashboard, or related services.
                </p>
                <p>
                    This page is the direct privacy policy for the Elite Guard app listed on Google Play under package name <strong>com.eliteguard.app</strong>.
                </p>

                <h3>Information We Collect</h3>
                <p>Depending on your role and how you use our services, we may collect the following information:</p>
                <ul class="privacy-list">
                    <li><strong>Account information:</strong> name, email address, phone number, employee profile details, login credentials, role, and account status.</li>
                    <li><strong>Employment and scheduling information:</strong> assigned sites, shifts, availability, attendance, time clock records, open-shift claims, pay-slip records, policy acknowledgments, signatures, and related workplace documents.</li>
                    <li><strong>Location information:</strong> location data used for security operations, site attendance, shift verification, patrol activity, and time clock verification where enabled and permitted.</li>
                    <li><strong>Device and usage information:</strong> device type, operating system, app activity, log data, IP address, crash reports, and diagnostic information used to secure and improve the app.</li>
                    <li><strong>Communications and reports:</strong> forms, incident reports, security reports, uploaded files, messages, complaint information, and support requests submitted through the app or website.</li>
                </ul>

                <h3>How We Use Information</h3>
                <ul class="privacy-list">
                    <li>To create, verify, and manage user accounts.</li>
                    <li>To schedule shifts, track attendance, verify site presence, and support security operations.</li>
                    <li>To process policy acknowledgments, employment forms, reports, and workplace records.</li>
                    <li>To communicate about shifts, notices, incidents, support requests, and operational updates.</li>
                    <li>To maintain safety, prevent fraud or unauthorized access, troubleshoot issues, and improve app performance.</li>
                    <li>To comply with legal, regulatory, employment, licensing, insurance, and contractual obligations.</li>
                </ul>

                <h3>Sharing of Information</h3>
                <p>We do not sell personal information. We may share information only when necessary for the following purposes:</p>
                <ul class="privacy-list">
                    <li>With authorized Elite Guard Inc. administrators, supervisors, dispatchers, payroll staff, and operations personnel.</li>
                    <li>With clients or site representatives when needed to deliver contracted security services and verify attendance or reports.</li>
                    <li>With trusted service providers who help host, maintain, secure, or support our app and business systems.</li>
                    <li>With government, law enforcement, licensing, insurance, or legal authorities where required by law or to protect rights, safety, and security.</li>
                </ul>

                <h3>Location Data</h3>
                <p>
                    The Elite Guard app may use location data to confirm that security personnel are at an assigned work site, to support patrol and attendance records, and to help manage operational safety. Location data is used for legitimate security and workforce-management purposes and is not sold to third parties.
                </p>

                <h3>Data Retention</h3>
                <p>
                    We retain information for as long as needed to provide services, manage employment and operational records, comply with legal obligations, resolve disputes, enforce agreements, and meet security or audit requirements. When information is no longer required, we delete, anonymize, or securely archive it according to our record retention practices.
                </p>

                <h3>Security</h3>
                <p>
                    We use administrative, technical, and organizational safeguards designed to protect personal information from unauthorized access, loss, misuse, alteration, or disclosure. No system is completely secure, but we work to keep information protected and restrict access to authorized personnel only.
                </p>

                <h3>Your Choices and Rights</h3>
                <p>
                    You may request access to, correction of, or deletion of your personal information, subject to applicable legal, employment, contractual, and operational requirements. You may also contact us with privacy questions or concerns using the contact details below.
                </p>

                <h3>Children's Privacy</h3>
                <p>
                    The Elite Guard app is intended for authorized business and employment use. It is not directed to children, and we do not knowingly collect personal information from children.
                </p>

                <h3>Changes to This Privacy Policy</h3>
                <p>
                    We may update this Privacy Policy from time to time. Updates will be posted on this page with a revised effective date. Continued use of the app or services after an update means the revised policy applies.
                </p>

                <div class="privacy-contact">
                    <h3>Contact Us</h3>
                    <p class="mb-1"><strong>Elite Guard Inc.</strong></p>
                    <p class="mb-1">3961 52 Ave NE #2104, Calgary, AB T3J 0J8, Canada</p>
                    <p class="mb-1">Phone: (403) 830-7772</p>
                    <p class="mb-0">Email: info@eliteguardinc.com</p>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection
