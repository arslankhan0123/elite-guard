<footer class="footer-elite">
    <div class="container">
        <div class="row g-5">
            <!-- Column 1: Logo & About -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-column">
                    <a href="#" class="brand-name mb-4" style="font-size: 1.5rem;">
                        <i class="fas fa-shield-halved text-primary"></i> ELITE GUARD
                    </a>
                    <p class="footer-about-text">
                        Elite Guard provides military-grade security infrastructure and personnel management for
                        high-stakes operations. We synchronize technology and human intelligence to ensure maximum
                        protection.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-btn"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div class="col-lg-2 col-md-6">
                <div class="footer-column">
                    <h4>Quick Links</h4>
                    <div class="footer-links-list">
                        <a href="{{ url('/') }}" class="footer-link"><i class="fas fa-chevron-right"></i> Home</a>
                        <a href="{{ route('about') }}" class="footer-link"><i class="fas fa-chevron-right"></i> About
                            Us</a>
                        <a href="{{ route('services') }}" class="footer-link"><i class="fas fa-chevron-right"></i> Our
                            Services</a>
                        <a href="{{ route('contact') }}" class="footer-link"><i class="fas fa-chevron-right"></i>
                            Contact Us</a>
                        <a href="{{ route('architecture') }}" class="footer-link"><i class="fas fa-chevron-right"></i>
                            Architecture</a>
                    </div>
                </div>
            </div>

            <!-- Column 3: Other Pages -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-column">
                    <h4>Resources</h4>
                    <div class="footer-links-list">
                        <a href="{{ route('privacy-policy') }}" class="footer-link"><i class="fas fa-chevron-right"></i>
                            Privacy Policy</a>
                        <a href="{{ route('terms-conditions') }}" class="footer-link"><i
                                class="fas fa-chevron-right"></i> Terms of Service</a>
                        <a href="{{ route('security-protocol') }}" class="footer-link"><i
                                class="fas fa-chevron-right"></i> Security Protocol</a>
                        <a href="{{ route('career-portal') }}" class="footer-link"><i class="fas fa-chevron-right"></i>
                            Career Portal</a>
                        <a href="{{ route('operational-faq') }}" class="footer-link"><i
                                class="fas fa-chevron-right"></i> Operational FAQ</a>
                    </div>
                </div>
            </div>

            <!-- Column 4: Location & Contact -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-column">
                    <h4>Our Headquarters</h4>
                    <div class="contact-info">
                        <div class="contact-item">
                            <div class="contact-icon"><i class="fas fa-location-dot"></i></div>
                            <div>
                                <strong>Location:</strong><br>
                                123 Security Avenue, Tech Plaza,<br>
                                London, UK
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon"><i class="fas fa-phone"></i></div>
                            <div>
                                <strong>Phone:</strong><br>
                                +44 (0) 20 1234 5678
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                            <div>
                                <strong>Email:</strong><br>
                                ops@eliteguard.com
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="copyright-text">
                &copy; {{ date('Y') }} <strong>Elite Guard Operational Systems</strong>. All rights reserved.
            </div>
            <div class="copyright-text d-flex gap-4">
                <span class="text-primary fw-bold">AES-256 ENCRYPTED</span>
                <span class="text-dim">SYSTEM STATUS: OPTIMAL</span>
            </div>
        </div>
    </div>
</footer>