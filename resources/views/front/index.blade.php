@extends('layouts.master')

@section('title', 'Split TheBill — Langganan Premium Lebih Hemat')

@section('content')
    @php
        $steps = [
            [
                'number' => '01',
                'title' => 'Pilih layanan',
                'description' => 'Temukan layanan premium yang ingin kamu nikmati bersama dengan harga yang lebih ringan.',
                'image' => 'assets/images/thumbnails/select-product.png',
            ],
            [
                'number' => '02',
                'title' => 'Lengkapi pembayaran',
                'description' => 'Isi data dengan benar, transfer sesuai total pesanan, lalu unggah bukti pembayaran.',
                'image' => 'assets/images/thumbnails/payment-process.png',
            ],
            [
                'number' => '03',
                'title' => 'Bergabung ke grup',
                'description' => 'Setelah pembayaran diverifikasi, kamu akan masuk ke grup dan menerima informasi akses.',
                'image' => 'assets/images/thumbnails/join-group.png',
            ],
        ];

        $testimonials = [
            ['name' => 'Nabila Reyna', 'photo' => 'photo-1.png', 'text' => 'Prosesnya jelas dan cepat. Langganan jadi jauh lebih hemat tanpa perlu bingung mengatur grup sendiri.'],
            ['name' => 'Bapak Budi', 'photo' => 'photo-2.png', 'text' => 'Status pesanan mudah dicek dan semua informasi pembayaran tersusun rapi. Pengalaman yang sangat praktis.'],
            ['name' => 'Ibu Budi', 'photo' => 'photo-3.png', 'text' => 'Pilihan layanannya lengkap dan alur pesan mudah diikuti, bahkan dari ponsel. Sangat membantu untuk berhemat.'],
            ['name' => 'Murayiki Bazz', 'photo' => 'photo-4.png', 'text' => 'Tidak perlu lagi membayar harga penuh. Informasi grup dan progres pesanan juga transparan.'],
            ['name' => 'Bimore Atreidess', 'photo' => 'photo-5.png', 'text' => 'Mulai dari memilih produk sampai mengunggah bukti transfer terasa sederhana dan tidak berbelit-belit.'],
            ['name' => 'Unil Utami', 'photo' => 'photo-6.png', 'text' => 'Harga lebih ramah di kantong dan layanan pelanggan responsif saat saya membutuhkan bantuan.'],
        ];

        $faqs = [
            ['question' => 'Kapan langganan mulai aktif?', 'answer' => 'Langganan diproses setelah bukti pembayaran berhasil diverifikasi. Kamu dapat memantau statusnya kapan saja melalui menu Pesanan Saya.'],
            ['question' => 'Bagaimana cara memperpanjang langganan?', 'answer' => 'Pilih kembali layanan yang sama dan lakukan pemesanan baru sebelum masa langganan aktif berakhir agar akses tetap berlanjut.'],
            ['question' => 'Metode pembayaran apa yang tersedia?', 'answer' => 'Saat ini pembayaran dilakukan melalui transfer bank yang tersedia pada halaman pembayaran. Simpan bukti transfer untuk proses verifikasi.'],
            ['question' => 'Bagaimana jika akun langganan bermasalah?', 'answer' => 'Periksa pesan terbaru pada detail pesanan terlebih dahulu. Jika masalah berlanjut, hubungi tim dukungan dengan menyertakan kode booking.'],
            ['question' => 'Apakah data pribadi saya aman?', 'answer' => 'Kami hanya meminta data yang diperlukan untuk memproses pesanan. Jangan pernah membagikan kode booking kepada pihak yang tidak berkepentingan.'],
            ['question' => 'Bagaimana jika grup belum penuh?', 'answer' => 'Status dan jumlah anggota dapat dipantau melalui detail pesanan. Informasi lanjutan akan diberikan melalui pesan pada grup pesananmu.'],
        ];
    @endphp

    <x-navbar />

    <main id="main-content">
        <header class="relative isolate overflow-hidden">
            <div class="motion-glow absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_right,rgba(255,12,129,0.14),transparent_36%),radial-gradient(circle_at_bottom_left,rgba(226,85,32,0.12),transparent_32%)]"></div>
            <div class="site-shell flex min-h-[620px] flex-col items-center justify-center py-16 text-center sm:min-h-[680px] sm:py-20 lg:min-h-[720px]">
                <div class="reveal-on-scroll inline-flex max-w-full items-center gap-3 rounded-full bg-patungan-black px-4 py-2 text-left text-sm font-semibold text-white sm:px-5 sm:text-base">
                    <img src="{{ asset('assets/images/photos/Profiles.png') }}" class="motion-float h-8 w-auto shrink-0 sm:h-9" alt="">
                    <span><strong>16.500+</strong> pengguna sudah bergabung 🔥</span>
                </div>

                <h1 class="reveal-on-scroll reveal-delay-1 mt-8 max-w-5xl font-Grifter text-[clamp(2.6rem,8vw,5rem)] font-bold leading-[1.04] tracking-[-0.03em] sm:mt-10">
                    Patungan akun premium,
                    <span class="bg-gradient-to-r from-[#E25520] to-[#E45687] bg-clip-text text-transparent">hemat tanpa batas</span>
                </h1>

                <p class="reveal-on-scroll reveal-delay-2 mt-6 max-w-2xl text-base font-medium leading-7 text-patungan-grey sm:text-lg sm:leading-8">
                    Nikmati layanan streaming, musik, dan edukasi bersama. Alur pemesanan transparan, biaya lebih ringan, dan status mudah dipantau.
                </p>

                <div class="reveal-on-scroll reveal-delay-3 mt-9 flex w-full max-w-lg flex-col justify-center gap-3 sm:w-auto sm:max-w-none sm:flex-row sm:gap-4">
                    <a href="#Products" class="btn-primary">
                        Lihat Layanan
                        <img src="{{ asset('assets/images/icons/arrow-right-white.svg') }}" class="h-5 w-5" alt="">
                    </a>
                    <a href="#How-It-Works" class="btn-secondary">Pelajari Cara Pesan</a>
                </div>
            </div>
        </header>

        <section aria-label="Statistik Split TheBill" class="reveal-on-scroll bg-patungan-black text-white">
            <div class="site-shell grid grid-cols-2 gap-px bg-white/10 py-1 sm:grid-cols-4">
                @foreach ([['2.209+', 'Total pengguna'], ['9/10', 'Pelanggan puas'], ['12', 'Layanan'], ['4.920+', 'Transaksi']] as [$value, $label])
                    <div class="flex flex-col items-center bg-patungan-black px-3 py-8 text-center sm:py-10">
                        <strong class="font-Grifter text-3xl sm:text-4xl">{{ $value }}</strong>
                        <span class="mt-2 text-sm font-semibold text-patungan-violet sm:text-base">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section id="Products" class="section-space relative overflow-hidden">
            <div class="absolute -right-40 top-0 -z-10 h-80 w-80 rounded-full bg-pink-200/30 blur-3xl"></div>
            <div class="site-shell">
                <div class="reveal-on-scroll flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div class="max-w-2xl">
                        <p class="section-kicker">Layanan kami</p>
                        <h2 class="section-title mt-3">Beragam akun premium untuk dinikmati bersama</h2>
                    </div>
                    <a href="{{ route('front.check_booking') }}" class="font-bold text-patungan-grey transition hover:text-patungan-black">Sudah memesan? Cek status →</a>
                </div>

                @if ($newProducts->isEmpty())
                    <div class="surface-card motion-card reveal-on-scroll reveal-delay-1 mt-10 flex flex-col items-center px-6 py-14 text-center sm:px-10">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-patungan-bg-grey">
                            <img src="{{ asset('assets/images/icons/receipt-text-black.svg') }}" class="h-8 w-8" alt="">
                        </div>
                        <h3 class="mt-5 text-xl font-bold">Layanan sedang disiapkan</h3>
                        <p class="mt-2 max-w-lg leading-7 text-patungan-grey">Layanan baru sedang kami siapkan. Silakan kembali lagi dalam waktu dekat.</p>
                    </div>
                @else
                    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($newProducts->take(6) as $product)
                            <article class="surface-card motion-card reveal-on-scroll flex min-w-0 flex-col overflow-hidden">
                                <a href="{{ route('front.details', $product) }}" class="block aspect-[16/9] overflow-hidden bg-[#D9D9D9]">
                                    <img src="{{ $product->thumbnail_url }}" class="h-full w-full object-cover transition duration-300 hover:scale-105" alt="{{ $product->name }}">
                                </a>
                                <div class="flex flex-1 flex-col p-5 sm:p-6">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl bg-patungan-bg-grey">
                                            <img src="{{ $product->photo_url }}" class="h-full w-full object-contain" alt="">
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="truncate text-xl font-bold">{{ $product->name }}</h3>
                                            <p class="mt-1 text-sm font-semibold text-patungan-grey">{{ $product->duration }} · {{ $product->capacity }} orang</p>
                                        </div>
                                    </div>

                                    <div class="my-6 rounded-2xl border border-patungan-border bg-patungan-bg-grey p-4">
                                        <p class="text-sm font-semibold text-patungan-grey">Mulai dari</p>
                                        <p class="mt-1 text-2xl font-extrabold">Rp {{ number_format($product->price_per_person, 0, ',', '.') }}</p>
                                        <p class="mt-2 flex items-center gap-2 text-sm font-semibold text-patungan-grey">
                                            <img src="{{ asset('assets/images/icons/verify-green.svg') }}" class="h-4 w-4" alt="">
                                            Harga per orang
                                        </p>
                                    </div>

                                    <a href="{{ route('front.details', $product) }}" class="btn-primary mt-auto w-full">Lihat Detail</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section id="How-It-Works" class="section-space bg-white/55">
            <div class="site-shell grid gap-10 lg:grid-cols-[0.85fr_1.15fr] lg:gap-14">
                <div class="reveal-on-scroll lg:sticky lg:top-28 lg:self-start">
                    <p class="section-kicker">Cara pesan</p>
                    <h2 class="section-title mt-3">Mudah dari awal sampai akses diterima</h2>
                    <p class="mt-5 max-w-xl text-base font-medium leading-7 text-patungan-grey sm:text-lg sm:leading-8">
                        Setiap tahap dirancang agar informasi harga, pembayaran, dan status grup mudah dipahami.
                    </p>

                    <div class="mt-7 grid gap-3 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                        @foreach (['Data pribadi aman', 'Pembayaran transparan', 'Akun resmi & legal', 'Status mudah dipantau', 'Lebih hemat', 'Dukungan pelanggan'] as $benefit)
                            <p class="flex items-center gap-2 font-semibold text-patungan-grey">
                                <img src="{{ asset('assets/images/icons/verify-green.svg') }}" class="h-5 w-5 shrink-0" alt="">
                                {{ $benefit }}
                            </p>
                        @endforeach
                    </div>

                    <a href="#Products" class="btn-primary mt-8 w-full sm:w-auto">Mulai Pilih Layanan</a>
                </div>

                <div class="grid gap-6">
                    @foreach ($steps as $step)
                        <article class="surface-card motion-card reveal-on-scroll grid overflow-hidden sm:grid-cols-[1fr_220px]">
                            <div class="p-6 sm:p-8">
                                <span class="inline-flex h-12 min-w-12 items-center justify-center rounded-full bg-patungan-black px-3 font-Grifter text-lg text-white">{{ $step['number'] }}</span>
                                <h3 class="mt-5 text-2xl font-bold">{{ $step['title'] }}</h3>
                                <p class="mt-3 leading-7 text-patungan-grey">{{ $step['description'] }}</p>
                            </div>
                            <div class="h-52 overflow-hidden bg-patungan-bg-grey sm:h-full sm:min-h-64">
                                <img src="{{ asset($step['image']) }}" class="motion-float h-full w-full object-cover object-top" alt="Ilustrasi {{ strtolower($step['title']) }}">
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="Payment-Method" class="section-space">
            <div class="site-shell reveal-on-scroll text-center">
                <p class="section-kicker">Metode pembayaran</p>
                <h2 class="section-title mt-3">Transfer dari bank dan dompet digital favoritmu</h2>
                <div class="mt-6 flex flex-wrap justify-center gap-x-6 gap-y-3 text-sm font-semibold text-patungan-grey sm:text-base">
                    @foreach (['Transfer bank', 'Virtual account', 'Dompet digital'] as $method)
                        <span class="flex items-center gap-2">
                            <img src="{{ asset('assets/images/icons/verify-green.svg') }}" class="h-5 w-5" alt="">
                            {{ $method }}
                        </span>
                    @endforeach
                </div>
                <div class="surface-card media-well motion-card reveal-on-scroll reveal-delay-1 mx-auto mt-9 max-w-5xl overflow-hidden p-4 sm:p-8">
                    <img src="{{ asset('assets/images/thumbnails/supported-payments.png') }}" class="motion-float mx-auto h-auto w-full object-contain" alt="Bank dan metode pembayaran yang didukung">
                </div>
            </div>
        </section>

        <section id="Happy-Customer" class="section-space bg-white/55">
            <div class="site-shell">
                <div class="reveal-on-scroll mx-auto max-w-3xl text-center">
                    <p class="section-kicker">Cerita pelanggan</p>
                    <h2 class="section-title mt-3">Pengalaman hemat yang terasa lebih sederhana</h2>
                </div>

                <div class="reveal-on-scroll reveal-delay-1 mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($testimonials as $testimonial)
                        <figure class="surface-card motion-card flex h-full flex-col p-6 sm:p-7">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('assets/images/photos/' . $testimonial['photo']) }}" class="h-12 w-12 rounded-full object-cover" alt="Foto {{ $testimonial['name'] }}">
                                <figcaption>
                                    <p class="font-bold">{{ $testimonial['name'] }}</p>
                                    <p class="text-sm font-semibold text-patungan-grey">Pelanggan Split TheBill</p>
                                </figcaption>
                            </div>
                            <blockquote class="mt-6 flex-1 leading-7">“{{ $testimonial['text'] }}”</blockquote>
                            <div class="mt-6 flex" aria-label="5 dari 5 bintang">
                                @for ($star = 0; $star < 5; $star++)
                                    <img src="{{ asset('assets/images/icons/Star.svg') }}" class="h-5 w-5" alt="">
                                @endfor
                            </div>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="FAQ" class="section-space">
            <div class="site-shell">
                <div class="reveal-on-scroll mx-auto max-w-3xl text-center">
                    <p class="section-kicker">Pertanyaan umum</p>
                    <h2 class="section-title mt-3">Jawaban singkat sebelum kamu mulai</h2>
                </div>

                <div class="reveal-on-scroll reveal-delay-1 mx-auto mt-10 grid max-w-5xl gap-4 lg:grid-cols-2">
                    @foreach ($faqs as $index => $faq)
                        <details class="group surface-card motion-card overflow-hidden" @if ($index === 0) open @endif>
                            <summary class="flex cursor-pointer list-none items-start justify-between gap-4 p-5 font-bold sm:p-6">
                                <span class="flex gap-3">
                                    <span class="text-patungan-grey">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span>{{ $faq['question'] }}</span>
                                </span>
                                <span class="mt-0.5 text-xl transition group-open:rotate-45" aria-hidden="true">+</span>
                            </summary>
                            <p class="px-5 pb-5 pl-[3.75rem] leading-7 text-patungan-grey sm:px-6 sm:pb-6 sm:pl-[4.25rem]">{{ $faq['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-white bg-white/60 py-12 sm:py-14">
        <div class="site-shell reveal-on-scroll">
            <div class="grid gap-10 md:grid-cols-[1.25fr_0.75fr_0.75fr]">
                <div class="max-w-md">
                    <img src="{{ asset('assets/images/logos/logoo.svg') }}" class="h-10 w-auto" alt="Split TheBill">
                    <p class="mt-5 leading-7 text-patungan-grey">Berbagi biaya langganan premium dengan alur yang lebih mudah, transparan, dan terjangkau.</p>
                </div>

                <div>
                    <h2 class="font-bold">Jelajahi</h2>
                    <ul class="mt-4 grid gap-3 text-patungan-grey">
                        <li><a href="#Products" class="transition hover:text-patungan-black">Layanan</a></li>
                        <li><a href="#How-It-Works" class="transition hover:text-patungan-black">Cara Pesan</a></li>
                        <li><a href="#Happy-Customer" class="transition hover:text-patungan-black">Testimoni</a></li>
                        <li><a href="#FAQ" class="transition hover:text-patungan-black">FAQ</a></li>
                    </ul>
                </div>

                <div>
                    <h2 class="font-bold">Pesanan</h2>
                    <ul class="mt-4 grid gap-3 text-patungan-grey">
                        <li><a href="{{ route('front.check_booking') }}" class="transition hover:text-patungan-black">Cek Pesanan</a></li>
                        <li><a href="{{ url('/admin') }}" class="transition hover:text-patungan-black">Panel Admin</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 flex flex-col gap-5 border-t border-patungan-border pt-6 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm font-medium text-patungan-grey">© {{ now()->year }} Split TheBill. Hak cipta dilindungi.</p>
                <a href="https://www.instagram.com/split.thebill" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 font-semibold text-patungan-grey transition hover:text-patungan-black">
                    <img src="{{ asset('assets/images/icons/instagram.svg') }}" class="h-5 w-5" alt="">
                    Instagram
                </a>
            </div>
        </div>
    </footer>
@endsection
