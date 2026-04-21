<nav class="nav-elite" id="mainNav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ url('/') }}" class="brand-name">
            <i class="fas fa-shield-halved text-primary"></i> ELITE GUARD
        </a>

        <div class="d-none d-lg-flex align-items-center gap-5">
            <a href="{{ url('/') }}" class="nav-link-elite active">Home</a>
            <a href="#about" class="nav-link-elite">About</a>
            <a href="#services" class="nav-link-elite">Services</a>
            <a href="#contact" class="nav-link-elite">Contact Us</a>
        </div>

        <div class="d-flex align-items-center gap-3">
            @auth
            @if(Auth::user()->role === 'SuperAdmin')
            <a href="{{ url('/dashboard') }}" class="btn-action-hub btn-primary-hub py-2 px-4 fs-6">
                DASHBOARD <i class="fas fa-microchip"></i>
            </a>
            @endif
            @else
            <a href="{{ route('login') }}" class="btn-action-hub btn-secondary-hub py-2 px-4 fs-6">
                SYSTEM ACCESS <i class="fas fa-shield-halved"></i>
            </a>
            @endauth

            <!-- Mobile Toggle -->
            <button class="btn btn-link text-white d-lg-none p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav">
                <i class="fas fa-bars-staggered fs-3"></i>
            </button>
        </div>
    </div>
</nav>

<!-- Mobile Navigation Offcanvas -->
<div class="offcanvas offcanvas-end bg-dark text-white border-start border-secondary" tabindex="-1" id="mobileNav" style="--bs-offcanvas-bg: var(--bg-dark);">
    <div class="offcanvas-header border-bottom border-secondary">
        <h5 class="offcanvas-title brand-name">ELITE GUARD</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <div class="d-flex flex-column gap-4 py-3">
            <a href="{{ url('/') }}" class="nav-link-elite active">Home</a>
            <a href="#about" class="nav-link-elite">About</a>
            <a href="#services" class="nav-link-elite">Services</a>
            <a href="#contact" class="nav-link-elite">Contact Us</a>
            <hr class="border-secondary opacity-20">
            @auth
            <a href="{{ url('/dashboard') }}" class="btn-action-hub btn-primary-hub justify-content-center">DASHBOARD</a>
            @else
            <a href="{{ route('login') }}" class="btn-action-hub btn-secondary-hub justify-content-center">LOGIN</a>
            @endauth
        </div>
    </div>
</div>