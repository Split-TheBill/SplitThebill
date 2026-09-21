<a href="#main-content" class="skip-link">Lewati ke konten</a>

<nav class="sticky top-0 z-50 border-b border-white/60 bg-white/90 backdrop-blur-xl" aria-label="Navigasi utama">
    <div class="site-shell flex min-h-20 items-center justify-between gap-4 py-3">
        <a href="{{ route('front.index') }}" class="shrink-0" aria-label="Split TheBill — Beranda">
            <img src="{{ asset('assets/images/logos/logoo.svg') }}" class="h-9 w-auto sm:h-10" alt="Split TheBill">
        </a>

        <ul class="hidden items-center gap-6 lg:flex">
            <li><a href="{{ route('front.index') }}#Products" class="font-semibold text-patungan-grey transition hover:text-patungan-black">Layanan</a></li>
            <li><a href="{{ route('front.index') }}#How-It-Works" class="font-semibold text-patungan-grey transition hover:text-patungan-black">Cara Pesan</a></li>
            <li><a href="{{ route('front.index') }}#Happy-Customer" class="font-semibold text-patungan-grey transition hover:text-patungan-black">Testimoni</a></li>
            <li><a href="{{ route('front.index') }}#FAQ" class="font-semibold text-patungan-grey transition hover:text-patungan-black">FAQ</a></li>
        </ul>

        <div class="flex items-center gap-2 sm:gap-3">
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

            <a href="{{ route('front.check_booking') }}" class="btn-primary hidden min-h-11 px-5 py-2 text-sm sm:inline-flex sm:min-h-12 sm:px-6">
                <img src="{{ asset('assets/images/icons/receipt-text-white.svg') }}" class="h-5 w-5" alt="">
                <span>Pesanan Saya</span>
            </a>

            <button type="button" data-mobile-menu-button aria-expanded="false" aria-controls="mobile-navigation" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-patungan-border bg-white lg:hidden" aria-label="Buka menu navigasi">
                <svg viewBox="0 0 24 24" class="h-6 w-6" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                </svg>
            </button>
        </div>
    </div>

    <div id="mobile-navigation" data-mobile-menu class="hidden border-t border-patungan-border bg-white lg:hidden">
        <div class="site-shell flex flex-col gap-1 py-4">
            <a href="{{ route('front.index') }}#Products" class="rounded-2xl px-4 py-3 font-semibold hover:bg-patungan-bg-grey">Layanan</a>
            <a href="{{ route('front.index') }}#How-It-Works" class="rounded-2xl px-4 py-3 font-semibold hover:bg-patungan-bg-grey">Cara Pesan</a>
            <a href="{{ route('front.index') }}#Happy-Customer" class="rounded-2xl px-4 py-3 font-semibold hover:bg-patungan-bg-grey">Testimoni</a>
            <a href="{{ route('front.index') }}#FAQ" class="rounded-2xl px-4 py-3 font-semibold hover:bg-patungan-bg-grey">FAQ</a>
            <a href="{{ route('front.check_booking') }}" class="btn-primary mt-2 sm:hidden">Pesanan Saya</a>
        </div>
    </div>
</nav>
