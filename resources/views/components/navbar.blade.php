<a href="#main-content" class="skip-link">Lewati ke konten</a>

<nav class="site-nav" aria-label="Navigasi utama">
    <div class="stb-shell nav-inner">
        <a href="{{ route('front.index') }}" class="nav-brand" aria-label="Split TheBill — Beranda">
            <img src="{{ asset('assets/images/logos/logoo.svg') }}" class="brand-logo--light nav-logo" alt="Split TheBill">
            <img src="{{ asset('assets/images/logos/logos.svg') }}" class="brand-logo--dark nav-logo" alt="Split TheBill">
        </a>

        <ul class="nav-links">
            <li><a href="{{ route('front.index') }}#Products"><span>01</span> Layanan</a></li>
            <li><a href="{{ route('front.index') }}#How-It-Works"><span>02</span> Cara Pesan</a></li>
            <li><a href="{{ route('front.index') }}#Resources"><span>03</span> Resource</a></li>
        </ul>

        <div class="nav-actions">
            <button
                type="button"
                class="theme-toggle theme-toggle--navbar"
                data-theme-toggle
                data-navbar-theme-toggle
                aria-label="Aktifkan mode gelap"
                aria-pressed="false"
                title="Aktifkan mode gelap"
            >
                <svg class="theme-toggle__icon theme-toggle__icon--moon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20.2 15.3A8.4 8.4 0 0 1 8.7 3.8 8.5 8.5 0 1 0 20.2 15.3Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                </svg>
                <svg class="theme-toggle__icon theme-toggle__icon--sun" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="3.7" fill="none" stroke="currentColor" stroke-width="1.8" />
                    <path d="M12 2.3v2M12 19.7v2M4.3 12h-2M21.7 12h-2M5.1 5.1l1.4 1.4M17.5 17.5l1.4 1.4M18.9 5.1l-1.4 1.4M6.5 17.5l-1.4 1.4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" />
                </svg>
                <span class="sr-only" data-theme-toggle-label>Aktifkan mode gelap</span>
            </button>

            <a href="{{ route('front.check_booking') }}" class="nav-order">
                <span>Pesanan Saya</span><span aria-hidden="true">↗</span>
            </a>

            <button type="button" data-mobile-menu-button aria-expanded="false" aria-controls="mobile-navigation" class="nav-menu-toggle" aria-label="Buka menu navigasi">
                <svg viewBox="0 0 24 24" class="h-6 w-6" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                </svg>
            </button>
        </div>
    </div>

    <div id="mobile-navigation" data-mobile-menu class="hidden nav-mobile">
        <div class="stb-shell nav-mobile__links">
            <a href="{{ route('front.index') }}#Products" class="nav-mobile-link"><span>01</span> Layanan</a>
            <a href="{{ route('front.index') }}#How-It-Works" class="nav-mobile-link"><span>02</span> Cara Pesan</a>
            <a href="{{ route('front.index') }}#Resources" class="nav-mobile-link"><span>03</span> Resource</a>
            <a href="{{ route('front.check_booking') }}" class="nav-mobile-order">Pesanan Saya <span aria-hidden="true">↗</span></a>
        </div>
    </div>
</nav>
