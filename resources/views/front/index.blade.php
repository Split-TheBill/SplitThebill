@extends('layouts.master')

@section('title', 'Split TheBill — Langganan Premium Lebih Hemat')

@section('content')
    @php
        $steps = [
            ['title' => 'Pilih layanan', 'description' => 'Bandingkan pilihan, harga per orang, dan durasinya.'],
            ['title' => 'Buat pesanan', 'description' => 'Isi data, transfer sesuai rincian, lalu unggah bukti pembayaran.'],
            ['title' => 'Pantau status', 'description' => 'Gunakan kode booking dan nomor WhatsApp untuk melihat progres pesanan.'],
        ];

        $faqs = [
            ['question' => 'Kapan langganan mulai aktif?', 'answer' => 'Langganan diproses setelah bukti pembayaran diverifikasi. Kamu dapat memantau statusnya melalui menu Pesanan Saya.'],
            ['question' => 'Metode pembayaran apa yang tersedia?', 'answer' => 'Metode dan rekening tujuan yang tersedia tercantum di halaman pembayaran. Simpan bukti transfer untuk proses verifikasi.'],
            ['question' => 'Bagaimana jika akses bermasalah?', 'answer' => 'Periksa pesan terbaru pada detail pesanan. Jika masalah berlanjut, hubungi tim dukungan dan sertakan kode booking.'],
            ['question' => 'Bagaimana jika grup belum penuh?', 'answer' => 'Status dan jumlah anggota dapat dilihat pada detail pesanan. Informasi lanjutan akan disampaikan melalui pesan pada grup pesananmu.'],
        ];
    @endphp

    <x-navbar />

    <main id="main-content">
        <header class="site-shell flex min-h-[480px] flex-col justify-center py-20 sm:min-h-[560px] sm:py-24" data-interactive-hero>
            <p class="reveal-on-scroll section-kicker">Split TheBill</p>
            <h1 class="reveal-on-scroll reveal-delay-1 mt-5 max-w-4xl font-Grifter text-[clamp(2.75rem,7vw,5.25rem)] font-bold leading-[1.06] tracking-[-0.04em]">
                Langganan premium,<br>
                <span class="text-patungan-red">lebih ringan bersama.</span>
            </h1>
            <p class="reveal-on-scroll reveal-delay-2 mt-6 max-w-2xl text-base font-medium leading-7 text-patungan-grey sm:text-lg sm:leading-8">
                Pilih layanan, lihat rincian biaya, dan pantau pesananmu dalam satu tempat.
            </p>
            <div class="reveal-on-scroll reveal-delay-3 mt-9 flex flex-col gap-3 sm:flex-row sm:items-center">
                <a href="#Products" class="btn-primary w-full sm:w-auto">Jelajahi layanan</a>
                <a href="{{ route('front.check_booking') }}" class="btn-secondary w-full sm:w-auto">Cek pesanan</a>
            </div>
        </header>

        <section id="Products" class="section-space border-t border-patungan-border">
            <div class="site-shell">
                <div class="reveal-on-scroll max-w-2xl">
                    <p class="section-kicker">Layanan</p>
                    <h2 class="section-title mt-3">Pilih yang sesuai untukmu</h2>
                    <p class="mt-4 leading-7 text-patungan-grey">Lihat harga dan informasi setiap layanan sebelum memesan.</p>
                </div>

                @if ($newProducts->isEmpty())
                    <div class="surface-card reveal-on-scroll mt-10 max-w-3xl p-7 sm:p-10">
                        <p class="section-kicker">Segera hadir</p>
                        <h3 class="mt-3 text-2xl font-bold">Layanan sedang kami siapkan</h3>
                        <p class="mt-3 leading-7 text-patungan-grey">Pilihan layanan belum tersedia saat ini. Ikuti kabar terbaru untuk mengetahui saat layanan kembali dibuka.</p>
                        <a href="https://www.instagram.com/split.thebill" target="_blank" rel="noopener noreferrer" class="btn-secondary mt-7 w-full sm:w-auto">Lihat kabar terbaru <span aria-hidden="true">↗</span></a>
                    </div>
                @else
                    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($newProducts->take(6) as $product)
                            <article class="surface-card motion-card product-card reveal-on-scroll flex min-w-0 flex-col overflow-hidden">
                                <a href="{{ route('front.details', $product) }}" class="block aspect-[16/9] overflow-hidden bg-patungan-bg-grey" aria-label="Lihat detail {{ $product->name }}">
                                    <img src="{{ $product->thumbnail_url }}" class="product-card__image h-full w-full object-cover" alt="" loading="lazy">
                                </a>
                                <div class="flex flex-1 flex-col p-5 sm:p-6">
                                    <h3 class="text-xl font-bold">{{ $product->name }}</h3>
                                    <p class="mt-2 text-sm font-medium text-patungan-grey">{{ $product->duration }} · {{ $product->capacity }} orang</p>
                                    <p class="mt-6 text-sm font-semibold text-patungan-grey">Harga per orang</p>
                                    <p class="mt-1 text-2xl font-extrabold">Rp {{ number_format($product->price_per_person, 0, ',', '.') }}</p>
                                    <a href="{{ route('front.details', $product) }}" class="btn-primary mt-6 w-full">Lihat detail</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section id="How-It-Works" class="section-space bg-white/55">
            <div class="site-shell">
                <div class="reveal-on-scroll max-w-2xl">
                    <p class="section-kicker">Cara pesan</p>
                    <h2 class="section-title mt-3">Tiga langkah sederhana</h2>
                </div>
                <ol class="mt-10 grid gap-5 md:grid-cols-3">
                    @foreach ($steps as $index => $step)
                        <li class="surface-card motion-card step-card reveal-on-scroll p-6 sm:p-8">
                            <span class="font-Grifter text-sm text-patungan-red">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3 class="mt-5 text-xl font-bold">{{ $step['title'] }}</h3>
                            <p class="mt-3 leading-7 text-patungan-grey">{{ $step['description'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        <section id="FAQ" class="section-space">
            <div class="site-shell grid gap-10 lg:grid-cols-[0.75fr_1.25fr] lg:gap-16">
                <div class="reveal-on-scroll max-w-lg">
                    <p class="section-kicker">Pertanyaan umum</p>
                    <h2 class="section-title mt-3">Yang perlu kamu tahu</h2>
                    <p class="mt-4 leading-7 text-patungan-grey">Jawaban singkat tentang pembayaran dan status pesanan.</p>
                </div>
                <div class="grid gap-3">
                    @foreach ($faqs as $index => $faq)
                        <details class="group surface-card faq-item reveal-on-scroll overflow-hidden" @if ($index === 0) open @endif>
                            <summary class="flex cursor-pointer list-none items-start justify-between gap-4 p-5 font-bold sm:p-6">
                                <span>{{ $faq['question'] }}</span>
                                <span class="text-xl transition group-open:rotate-45" aria-hidden="true">+</span>
                            </summary>
                            <p class="px-5 pb-5 leading-7 text-patungan-grey sm:px-6 sm:pb-6">{{ $faq['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="Resources" class="section-space border-t border-patungan-border bg-white/55">
            <div class="site-shell">
                <div class="reveal-on-scroll max-w-2xl">
                    <p class="section-kicker">Resource</p>
                    <h2 class="section-title mt-3">Temukan informasi yang kamu butuhkan</h2>
                </div>
                <div class="mt-9 grid gap-3 sm:grid-cols-2">
                    <a href="#How-It-Works" class="surface-card motion-card reveal-on-scroll flex items-center justify-between gap-4 p-5 font-bold sm:p-6">Panduan pemesanan <span aria-hidden="true">↗</span></a>
                    <a href="#FAQ" class="surface-card motion-card reveal-on-scroll flex items-center justify-between gap-4 p-5 font-bold sm:p-6">Pertanyaan umum <span aria-hidden="true">↗</span></a>
                    <a href="{{ route('front.check_booking') }}" class="surface-card motion-card reveal-on-scroll flex items-center justify-between gap-4 p-5 font-bold sm:p-6">Cek status pesanan <span aria-hidden="true">↗</span></a>
                    <a href="https://www.instagram.com/split.thebill" target="_blank" rel="noopener noreferrer" class="surface-card motion-card reveal-on-scroll flex items-center justify-between gap-4 p-5 font-bold sm:p-6">Kabar terbaru <span aria-hidden="true">↗</span></a>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-patungan-border bg-white/60 py-9 sm:py-11">
        <div class="site-shell flex flex-col gap-7 md:flex-row md:items-center md:justify-between">
            <div>
                <img src="{{ asset('assets/images/logos/logoo.svg') }}" class="brand-logo--light h-9 w-auto" alt="Split TheBill">
                <img src="{{ asset('assets/images/logos/logos.svg') }}" class="brand-logo--dark h-9 w-auto" alt="Split TheBill">
                <p class="mt-3 max-w-sm text-sm leading-6 text-patungan-grey">Berbagi biaya langganan premium dengan alur yang jelas.</p>
            </div>
            <div class="flex flex-wrap gap-x-6 gap-y-3 text-sm font-semibold text-patungan-grey">
                <a href="#Products" class="transition hover:text-patungan-black">Layanan</a>
                <a href="#Resources" class="transition hover:text-patungan-black">Resource</a>
                <a href="{{ route('front.check_booking') }}" class="transition hover:text-patungan-black">Cek pesanan</a>
            </div>
        </div>
        <div class="site-shell mt-7 border-t border-patungan-border pt-5 text-sm text-patungan-grey">© {{ now()->year }} Split TheBill.</div>
    </footer>
@endsection
