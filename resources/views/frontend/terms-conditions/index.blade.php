@extends('layouts.frontend.main')
@section('title', 'Terms and Conditions - Elite Guard')

@section('content')
<style>
    .terms-container {
        padding: 120px 0 80px;
        min-height: 100vh;
    }

    .terms-policy {
        max-width: 100%;
        margin: 0 auto;
        background: var(--bg-card);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 30px;
        padding: 50px;
    }

    .terms-kicker {
        color: var(--primary);
        display: block;
        font-size: 0.85rem;
        font-weight: 800;
        letter-spacing: 2px;
        margin-bottom: 16px;
        text-transform: uppercase;
    }

    .terms-title {
        background: linear-gradient(to right, #fff, var(--primary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 2.45rem;
        font-weight: 850;
        margin-bottom: 18px;
    }

    .terms-updated {
        color: var(--text-dim);
        font-size: 0.95rem;
        margin-bottom: 34px;
    }

    .terms-content {
        color: var(--text-dim);
        line-height: 1.8;
    }

    .terms-content h3 {
        color: var(--text-main);
        font-size: 1.35rem;
        font-weight: 750;
        margin: 34px 0 16px;
    }

    .terms-content p {
        margin-bottom: 18px;
    }

    .terms-list {
        list-style: none;
        margin: 0 0 24px;
        padding: 0;
    }

    .terms-list li {
        margin-bottom: 13px;
        padding-left: 30px;
        position: relative;
    }

    .terms-list li::before {
        color: var(--primary);
        content: '\f14a';
        font-family: 'Font Awesome 6 Free';
        font-size: 0.9rem;
        font-weight: 900;
        left: 0;
        position: absolute;
        top: 2px;
    }

    .terms-contact {
        background: rgba(139, 92, 246, 0.05);
        border: 1px solid rgba(139, 92, 246, 0.2);
        border-radius: 18px;
        margin-top: 36px;
        padding: 28px;
    }

    .terms-contact h3 {
        margin-top: 0;
    }

    @media (max-width: 767px) {
        .terms-policy {
            border-radius: 22px;
            padding: 30px 22px;
        }

        .terms-title {
            font-size: 1.85rem;
        }
    }
</style>

<div class="terms-container">
    <div class="container">
        <article class="terms-policy" data-aos="fade-up">
            <span class="terms-kicker">Elite Guard Inc.</span>
            <h1 class="terms-title">Terms and Conditions for Elite Guard App</h1>
            <p class="terms-updated"><strong>Effective date:</strong> June 24, 2026</p>

            <div class="terms-content">
                <p>
                    These Terms and Conditions govern access to and use of the Elite Guard mobile application, website, dashboard, and related services provided by Elite Guard Inc. By using the app or services, you agree to these terms.
                </p>
                <p>
                    This page is the direct terms and conditions page for the Elite Guard app listed on Google Play under package name <strong>com.eliteguard.app</strong>.
                </p>

                <h3>Who May Use the App</h3>
                <p>
                    The Elite Guard app is intended for authorized employees, contractors, clients, administrators, supervisors, and other approved users. You must only use the app for legitimate business, employment, security operations, scheduling, reporting, or account-management purposes.
                </p>

                <h3>User Accounts</h3>
                <ul class="terms-list">
                    <li>You are responsible for keeping your login credentials confidential.</li>
                    <li>You must provide accurate account, employment, scheduling, and contact information when requested.</li>
                    <li>You must notify Elite Guard Inc. immediately if you suspect unauthorized account access.</li>
                    <li>Elite Guard Inc. may suspend or disable accounts that are misused, inactive, compromised, or no longer authorized.</li>
                </ul>

                <h3>Acceptable Use</h3>
                <p>You agree not to misuse the app or services. Prohibited activity includes:</p>
                <ul class="terms-list">
                    <li>Submitting false attendance, location, incident, shift, payroll, or employment information.</li>
                    <li>Accessing, copying, altering, or sharing information you are not authorized to view or use.</li>
                    <li>Uploading harmful, illegal, misleading, offensive, or unauthorized content.</li>
                    <li>Attempting to disrupt, reverse engineer, bypass, overload, or compromise the app, website, dashboard, or related systems.</li>
                    <li>Using the app in a way that violates company policy, client requirements, employment obligations, licensing rules, or applicable law.</li>
                </ul>

                <h3>Security Operations and Work Records</h3>
                <p>
                    The app may be used to manage shifts, assigned sites, availability, time clocks, open-shift claims, attendance, location verification, reports, notices, policy acknowledgments, signatures, and related workplace records. Records submitted through the app may be reviewed by authorized Elite Guard Inc. personnel and used for operational, safety, payroll, compliance, audit, investigation, or legal purposes.
                </p>

                <h3>Location and Attendance Features</h3>
                <p>
                    Where enabled, the app may use location-related features to verify site attendance, support patrol activity, confirm shift presence, and assist security operations. You must not spoof, falsify, disable, or manipulate location or attendance information when it is required for your role.
                </p>

                <h3>Confidentiality</h3>
                <p>
                    You may receive access to confidential company, client, employee, site, operational, security, or incident information. You must protect this information and may only use or disclose it as authorized by Elite Guard Inc., client requirements, company policy, or applicable law.
                </p>

                <h3>Uploaded Content and Reports</h3>
                <p>
                    You are responsible for the accuracy and appropriateness of reports, forms, files, signatures, photos, messages, and other content you submit. By submitting content, you confirm that you have the right to provide it and that Elite Guard Inc. may use it to operate, document, support, secure, and improve its services.
                </p>

                <h3>Privacy</h3>
                <p>
                    Use of the app is also governed by our Privacy Policy, which explains how we collect, use, disclose, retain, and protect information. The Privacy Policy is available at the direct privacy policy URL provided in the app and on Google Play.
                </p>

                <h3>Service Availability</h3>
                <p>
                    We work to keep the app and services available, but we do not guarantee uninterrupted or error-free operation. Access may be limited by maintenance, updates, connectivity issues, device settings, third-party services, security concerns, or events outside our control.
                </p>

                <h3>Employment and Client Agreements</h3>
                <p>
                    These terms do not replace signed employment agreements, contractor agreements, service agreements, client contracts, site instructions, company policies, or legal obligations. If a separate written agreement applies and conflicts with these terms, the written agreement will control to the extent of the conflict.
                </p>

                <h3>Termination or Suspension</h3>
                <p>
                    Elite Guard Inc. may suspend, restrict, or terminate access to the app or services at any time if access is no longer authorized, if these terms are violated, if security risks are identified, or if required for operational, legal, employment, or client reasons.
                </p>

                <h3>Disclaimer and Limitation of Liability</h3>
                <p>
                    The app and services are provided for business and operational use. To the maximum extent permitted by law, Elite Guard Inc. is not liable for indirect, incidental, special, consequential, or punitive damages arising from use of, or inability to use, the app or services.
                </p>

                <h3>Governing Law</h3>
                <p>
                    These terms are governed by the laws of Alberta and the applicable laws of Canada. Security operations may also be subject to applicable employment, occupational health and safety, privacy, licensing, and security-services laws and regulations.
                </p>

                <h3>Changes to These Terms</h3>
                <p>
                    We may update these Terms and Conditions from time to time. Updates will be posted on this page with a revised effective date. Continued use of the app or services after an update means the revised terms apply.
                </p>

                <div class="terms-contact">
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
