@extends('layouts.master')

@section('title', 'Split TheBill — Satu Tagihan, Lebih Ringan Bersama')

@section('content')
    @php
        $steps = [
            ['title' => 'Temukan yang kamu suka.', 'description' => 'Pilih layanan dan baca rincian harga, durasi, serta jumlah anggota sebelum memesan.', 'label' => 'Pilih layanan', 'note' => 'Nama · durasi · kapasitas'],
            ['title' => 'Bayar bagianmu.', 'description' => 'Lengkapi data pesanan, transfer ke rekening yang tertera, lalu unggah bukti pembayaran.', 'label' => 'Kirim pembayaran', 'note' => 'Rincian biaya · bukti transfer'],
            ['title' => 'Pantau, lalu nikmati.', 'description' => 'Simpan kode booking. Gunakan kode dan nomor WhatsApp untuk melihat verifikasi, informasi grup, dan akses layanan.', 'label' => 'Pantau pesanan', 'note' => 'Kode booking · informasi akses'],
        ];
        $faqs = [
            ['question' => 'Kapan langganan mulai aktif?', 'answer' => 'Langganan diproses setelah bukti pembayaran diverifikasi. Kamu dapat memantau statusnya melalui menu Pesanan Saya.'],
            ['question' => 'Bagaimana cara membayarnya?', 'answer' => 'Rekening tujuan dan total biaya tercantum di halaman pembayaran. Transfer sesuai rincian, lalu unggah bukti pembayaran untuk diverifikasi.'],
            ['question' => 'Bagaimana jika akses bermasalah?', 'answer' => 'Periksa pesan terbaru pada detail pesanan. Jika masalah berlanjut, hubungi tim dukungan melalui Instagram dan sertakan kode booking.'],
            ['question' => 'Bagaimana jika grup belum penuh?', 'answer' => 'Status dan jumlah anggota dapat dilihat pada detail pesanan. Informasi lanjutan disampaikan melalui pesan pada grup pesananmu.'],
        ];
        $resources = [
            ['number' => '01', 'title' => 'Panduan pemesanan', 'description' => 'Dari pilih layanan sampai cek akses.', 'href' => '#How-It-Works', 'external' => false],
            ['number' => '02', 'title' => 'Cek status pesanan', 'description' => 'Sudah pesan? Lihat progresnya di sini.', 'href' => route('front.check_booking'), 'external' => false],
            ['number' => '03', 'title' => 'Pertanyaan umum', 'description' => 'Pembayaran, grup, dan informasi akses.', 'href' => '#FAQ', 'external' => false],
            ['number' => '04', 'title' => 'Kabar terbaru', 'description' => 'Temui kami di Instagram.', 'href' => 'https://www.instagram.com/split.thebill', 'external' => true],
        ];
    @endphp

    <x-navbar />

    <main id="main-content" class="landing">
        <header class="stb-shell split-hero" data-interactive-hero>
            <div class="hero-copy">
                <p class="eyebrow"><span class="square-mark" aria-hidden="true"></span> Untuk hal-hal yang kamu suka.</p>
                <h1 class="hero-title">
                    <span class="hero-title__line"><span data-hero-word>Satu tagihan.</span></span>
                    <span class="hero-title__line"><span data-hero-word>Lebih ringan</span></span>
                    <span class="hero-title__line hero-title__line--serif"><em data-hero-word>bersama.</em><span class="split-asterisk" aria-hidden="true">✳</span></span>
                </h1>
                <div data-hero-intro>
                    <p class="hero-description">Langganan premium buat keseharianmu.<br class="desktop-break"> Pilih layanannya, bagi biayanya, nikmati bersama.</p>
                    <div class="hero-actions">
                        <a href="#Products" class="stb-button"><span>Jelajahi layanan</span><span aria-hidden="true">↗</span></a>
                        <a href="#How-It-Works" class="text-link">Gimana cara pesannya? <span aria-hidden="true">↓</span></a>
                    </div>
                    <p class="hero-footnote"><span aria-hidden="true">//</span> Biaya jelas. Status bisa dipantau.</p>
                </div>
            </div>

            <div class="receipt-stage" data-receipt-stage>
                <div class="receipt-backplate" aria-hidden="true"><span>SHARED<br>IS BETTER.</span><span class="backplate-code">STB—001 / BAGI BIAYANYA</span></div>
                <div class="receipt-stage__label">Coba bagi tagihannya <span aria-hidden="true">↘</span></div>
                <section class="shared-receipt" data-receipt data-split-demo data-total="120000" data-count="4" aria-labelledby="split-demo-title">
                    <div class="receipt-topline"><span>STB / 001</span><span>SIMULASI BIAYA</span></div>
                    <h2 id="split-demo-title" class="receipt-title">Let's split<br>this bill.</h2>
                    <span class="receipt-stamp" data-receipt-stamp aria-hidden="true">SPLIT<br>& SAVE</span>
                    <div class="receipt-rule" data-receipt-line></div>
                    <div class="receipt-detail"><span>Total / bulan</span><strong>Rp 120.000</strong></div>
                    <div class="receipt-detail receipt-detail--people">
                        <span>Bagi dengan</span>
                        <div class="split-controls" role="group" aria-label="Jumlah orang dalam simulasi">
                            <button type="button" data-split-minus aria-label="Kurangi jumlah orang">−</button>
                            <span><output data-split-count aria-live="off">4</output> orang</span>
                            <button type="button" data-split-plus aria-label="Tambah jumlah orang">+</button>
                        </div>
                    </div>
                    <div class="receipt-shares" data-split-shares aria-hidden="true">
                        @for ($person = 1; $person <= 6; $person++)
                            <span class="receipt-share" data-split-share data-flip-id="share-{{ $person }}" @if ($person > 4) hidden @endif>{{ str_pad($person, 2, '0', STR_PAD_LEFT) }}</span>
                        @endfor
                    </div>
                    <div class="receipt-rule receipt-rule--dashed" data-receipt-line></div>
                    <p class="receipt-total-label">Bagianmu jadi</p>
                    <p class="receipt-total"><output data-split-price aria-live="off">Rp 30.000</output></p>
                    <p class="receipt-total-unit">per orang / bulan</p>
                    <div class="receipt-bottomline"><span>ONE BILL. SHARED.</span><span aria-hidden="true">✳</span></div>
                    <p class="sr-only" data-split-announcement aria-live="polite" aria-atomic="true"></p>
                </section>
                <p class="receipt-disclaimer" data-split-note>Contoh perhitungan, bukan harga layanan.<br>Belum termasuk biaya admin.</p>
            </div>
        </header>

        <div class="stb-ticker" data-marquee aria-label="Dengarkan, tonton, belajar, berkarya bersama">
            <div class="stb-ticker__track" data-marquee-track aria-hidden="true">
                @for ($repeat = 0; $repeat < 3; $repeat++)
                    <span>DENGARKAN.</span><span class="ticker-divider">✳</span><span>TONTON.</span><span class="ticker-divider">✳</span><span>BELAJAR.</span><span class="ticker-divider">✳</span><span>BERKARYA.</span><span class="ticker-divider">✳</span>
                @endfor
            </div>
        </div>

        <section id="Products" class="stb-shell catalog-section">
            <div class="section-index"><span>01 / LAYANAN</span><span>PILIH SESUAI KEBUTUHANMU</span></div>
            <div class="catalog-heading">
                <h2 class="editorial-title" data-reveal>Upgrade keseharian.<br><em>Ringankan tagihan.</em></h2>
                <p>Harga, durasi, dan kapasitas grup.<br>Semua bisa kamu lihat sebelum pesan.</p>
            </div>

            @if ($newProducts->isEmpty())
                <div class="catalog-empty" data-reveal>
                    <div class="catalog-empty__ticket" aria-hidden="true"><span>ON THE<br>WAY.</span><small>STB / NEXT DROP</small></div>
                    <div class="catalog-empty__content">
                        <p class="eyebrow">Katalog akan segera dibuka</p>
                        <h3>Layanan sedang kami siapkan</h3>
                        <p>Pilihan langganan belum tersedia saat ini. Ikuti kabar terbaru untuk tahu kapan layanan kembali dibuka.</p>
                        <a href="https://www.instagram.com/split.thebill" target="_blank" rel="noopener noreferrer" class="text-link">Lihat kabar terbaru <span aria-hidden="true">↗</span></a>
                    </div>
                </div>
            @else
                <div class="catalog-ledger">
                    <div class="catalog-legend" aria-hidden="true"><span>LAYANAN</span><span>DURASI / GRUP</span><span>BIAYA PER ORANG</span></div>
                    @foreach ($newProducts->take(6) as $product)
                        <a href="{{ route('front.details', $product) }}" class="catalog-row" data-catalog-row>
                            <span class="ledger-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="catalog-row__service">
                                <span class="catalog-row__logo"><img src="{{ $product->photo_url }}" alt="" loading="lazy" width="64" height="64"></span>
                                <span><strong>{{ $product->name }}</strong><small>Lihat rincian layanan</small></span>
                            </span>
                            <span class="catalog-row__meta">{{ $product->duration }} <small>{{ $product->capacity }} orang / grup</small></span>
                            <span class="catalog-row__price">Rp {{ number_format($product->price_per_person, 0, ',', '.') }} <small>per orang</small></span>
                            <span class="ledger-arrow" data-row-arrow aria-hidden="true">↗</span>
                            <span class="ledger-hover-line" data-row-line aria-hidden="true"></span>
                        </a>
                    @endforeach
                </div>
            @endif
            <div class="catalog-note"><span>Sudah punya kode booking?</span><a href="{{ route('front.check_booking') }}" class="text-link">Cek pesananmu <span aria-hidden="true">↗</span></a></div>
        </section>

        <section id="How-It-Works" class="process-section" data-process>
            <div class="stb-shell">
                <div class="section-index section-index--inverse"><span>02 / CARA PESAN</span><span>DARI PILIH SAMPAI NIKMATI</span></div>
                <div class="process-heading"><h2 class="editorial-title" data-reveal>Alurnya jelas.<br><em>Tenang dari awal.</em></h2><p>Tiga langkah.<br>Satu tempat untuk memantau semuanya.</p></div>
                <div class="process-grid">
                    <ol class="process-steps">
                        @foreach ($steps as $step)
                            <li class="process-step @if ($loop->first) is-active @endif" data-process-step>
                                <span class="process-step__number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <div><h3>{{ $step['title'] }}</h3><p>{{ $step['description'] }}</p></div>
                            </li>
                        @endforeach
                    </ol>
                    <div class="process-visual-column">
                        <div class="process-ticket" data-process-visual aria-label="Ringkasan alur pemesanan">
                            <div class="process-ticket__header"><span>SPLIT THEBILL</span><span class="process-ticket__symbol" aria-hidden="true">↗</span></div>
                            <p class="process-ticket__label">ALUR PESANAN</p>
                            <div class="process-state-track">
                                <span class="process-state-rail" aria-hidden="true"><span data-process-progress></span></span>
                                @foreach ($steps as $step)
                                    <div class="process-state @if ($loop->first) is-active @endif" data-process-state>
                                        <span class="process-state__number" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        <div><strong>{{ $step['label'] }}</strong><small>{{ $step['note'] }}</small></div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="process-ticket__footer"><span>YOUR ORDER, IN ONE PLACE.</span><span aria-hidden="true">///</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="Resources" class="stb-shell help-section">
            <div class="section-index"><span>03 / RESOURCE</span><span>SELALU ADA JAWABANNYA</span></div>
            <div class="help-heading"><h2 class="editorial-title" data-reveal>Biar nggak ada<br><em>tanda tanya.</em></h2><p>Butuh panduan atau kabar terbaru?<br>Mulai dari sini.</p></div>
            <div class="help-grid">
                <div class="resource-index">
                    <h3 class="small-label">TAUTAN YANG BERGUNA</h3>
                    @foreach ($resources as $resource)
                        <a href="{{ $resource['href'] }}" @if ($resource['external']) target="_blank" rel="noopener noreferrer" @endif class="resource-row" data-resource-row>
                            <span class="ledger-number">{{ $resource['number'] }}</span>
                            <span><strong>{{ $resource['title'] }}</strong><small>{{ $resource['description'] }}</small></span>
                            <span class="ledger-arrow" data-row-arrow aria-hidden="true">↗</span>
                            <span class="ledger-hover-line" data-row-line aria-hidden="true"></span>
                        </a>
                    @endforeach
                </div>
                <div id="FAQ" class="faq-ledger">
                    <h3 class="small-label">PERTANYAAN UMUM</h3>
                    @foreach ($faqs as $faq)
                        <details class="editorial-faq" @if ($loop->first) open @endif>
                            <summary><span>{{ $faq['question'] }}</span><span class="faq-plus" aria-hidden="true">+</span></summary>
                            <div class="faq-answer"><p>{{ $faq['answer'] }}</p></div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <footer class="stb-footer">
        <div class="stb-shell">
            <div class="footer-topline"><p>Hal baik lebih seru kalau dibagi.</p><a href="#main-content" class="footer-top-link" aria-label="Kembali ke atas">↑</a></div>
            <p class="footer-wordmark" aria-label="Split TheBill"><span>split</span><span class="footer-slash" aria-hidden="true">/</span><span>thebill.</span></p>
            <div class="footer-bottomline"><span>© {{ now()->year }} Split TheBill</span><span>SHARED SUBSCRIPTIONS, BETTER DAYS.</span><a href="https://www.instagram.com/split.thebill" target="_blank" rel="noopener noreferrer">Instagram ↗</a></div>
        </div>
    </footer>
@endsection
