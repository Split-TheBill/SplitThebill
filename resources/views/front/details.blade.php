@extends('layouts.master')

@section('title', $product->name . ' — Split TheBill')

@section('content')
    <x-navbar />

    <main id="main-content" class="site-shell py-8 sm:py-10 lg:py-14">
        <header class="reveal-on-scroll mb-8 space-y-4 sm:mb-10">
            <nav aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2 text-sm font-semibold text-patungan-grey sm:text-base">
                    <li><a href="{{ route('front.index') }}" class="transition hover:text-patungan-black">Beranda</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-patungan-black" aria-current="page">{{ $product->name }}</li>
                </ol>
            </nav>
            <h1 class="section-title">Detail Produk</h1>
        </header>

        <div class="grid min-w-0 gap-8 lg:grid-cols-3 lg:items-start xl:gap-10">
            <div class="min-w-0 space-y-8 lg:col-span-2">
                <article class="surface-card motion-card reveal-on-scroll overflow-hidden">
                    <div class="h-44 bg-[#D9D9D9] sm:h-56 lg:h-64">
                        <img
                            src="{{ $product->thumbnail_url }}"
                            class="h-full w-full object-cover"
                            alt="Banner {{ $product->name }}"
                            decoding="async"
                        >
                    </div>

                    <div class="space-y-7 p-5 sm:p-8">
                        <div class="flex min-w-0 items-center gap-3 sm:gap-4">
                            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl sm:h-[62px] sm:w-[62px]">
                                <img
                                    src="{{ $product->photo_url }}"
                                    class="h-full w-full object-contain object-center"
                                    alt="Logo {{ $product->name }}"
                                    width="62"
                                    height="62"
                                    decoding="async"
                                >
                            </div>
                            <div class="min-w-0">
                                <h2 class="break-words text-xl font-bold leading-tight">{{ $product->name }}</h2>
                                <div class="mt-1 flex flex-wrap items-center gap-1" aria-label="Rating 4,9 dari 5 berdasarkan 2.120 ulasan">
                                    <img src="{{ asset('assets/images/icons/Star.svg') }}" class="h-5 w-5 shrink-0" alt="" aria-hidden="true">
                                    <span class="font-bold">4,9</span>
                                    <span class="font-semibold text-patungan-grey">(2.120 ulasan)</span>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-patungan-border p-4 sm:rounded-3xl sm:p-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-2xl font-extrabold leading-tight">
                                    Rp {{ number_format($product->price_per_person, 0, ',', '.') }}
                                    <span class="text-base font-semibold text-patungan-grey">/orang</span>
                                </p>
                                <div class="flex w-fit items-center gap-1 rounded-lg bg-patungan-red/10 px-3 py-2">
                                    <img src="{{ asset('assets/images/icons/clock-red.svg') }}" class="h-5 w-5 shrink-0" alt="" aria-hidden="true">
                                    <span class="font-bold text-patungan-red">{{ $product->duration }}</span>
                                </div>
                            </div>
                            <hr class="my-4 border-patungan-border">
                            <div class="flex items-center gap-2 text-patungan-grey">
                                <img src="{{ asset('assets/images/icons/verify-green.svg') }}" class="h-[18px] w-[18px] shrink-0" alt="" aria-hidden="true">
                                <span class="font-medium">Lebih hemat dengan harga patungan</span>
                            </div>
                        </div>

                        <section class="space-y-3" aria-labelledby="about-product">
                            <h2 id="about-product" class="text-xl font-bold">Tentang {{ $product->name }}</h2>
                            <p class="break-words text-base font-medium leading-7 text-patungan-grey sm:text-lg sm:leading-8">{{ $product->about }}</p>
                        </section>

                        <section class="space-y-4" aria-labelledby="product-features">
                            <h2 id="product-features" class="text-xl font-bold">Fitur {{ $product->name }}</h2>
                            @forelse ($product->keypoints as $keypoint)
                                <div class="flex items-start gap-3">
                                    <img src="{{ asset('assets/images/icons/verify-green.svg') }}" class="mt-0.5 h-5 w-5 shrink-0" alt="" aria-hidden="true">
                                    <p class="font-semibold leading-7 text-patungan-grey">{{ $keypoint->name }}</p>
                                </div>
                            @empty
                                <p class="font-medium text-patungan-grey">Informasi fitur akan segera tersedia.</p>
                            @endforelse
                        </section>

                        <hr class="border-patungan-border">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('assets/images/photos/Profiles.png') }}" class="h-9 w-auto shrink-0" alt="" aria-hidden="true">
                            <p class="font-semibold">5.219+ <span class="text-patungan-grey">pengguna telah bergabung</span></p>
                        </div>
                    </div>
                </article>

                <section class="surface-card motion-card reveal-on-scroll reveal-delay-1 space-y-6 p-5 sm:p-8" aria-labelledby="how-it-works-title">
                    <div>
                        <p class="section-kicker">Cara kerja</p>
                        <h2 id="how-it-works-title" class="mt-2 text-2xl font-bold">Sebelum bergabung</h2>
                    </div>
                    <ol class="list-decimal space-y-3 pl-5 font-medium leading-7 text-patungan-grey sm:text-lg sm:leading-8">
                        <li>Setiap anggota grup memiliki tanggung jawab yang sama dalam memenuhi komitmen pembayaran.</li>
                        <li>Pastikan pembayaran dilakukan sesuai tenggat waktu yang disepakati untuk kelancaran patungan.</li>
                        <li>Setiap transaksi tercatat dan dapat diakses oleh semua anggota untuk memastikan transparansi.</li>
                        <li>Keputusan mengenai produk atau layanan dilakukan melalui persetujuan bersama.</li>
                        <li>Dana yang terkumpul akan langsung dialokasikan sesuai tujuan grup.</li>
                    </ol>
                    <div class="flex items-start gap-3 rounded-2xl bg-patungan-red/10 p-4">
                        <img src="{{ asset('assets/images/icons/notification-box-red.svg') }}" class="h-10 w-10 shrink-0 sm:h-[52px] sm:w-[52px]" alt="" aria-hidden="true">
                        <p class="font-semibold leading-6 text-patungan-red sm:leading-7">Dana akan dikembalikan jika jumlah anggota yang dibutuhkan tidak terpenuhi dalam waktu yang ditentukan.</p>
                    </div>
                </section>
            </div>

            <aside class="surface-card motion-card reveal-on-scroll reveal-delay-2 min-w-0 p-5 sm:p-8 lg:sticky lg:top-28" aria-labelledby="price-details-title">
                <h2 id="price-details-title" class="text-xl font-bold">Rincian Harga</h2>
                <dl class="mt-6 space-y-4 rounded-2xl border border-patungan-border p-4 sm:rounded-3xl sm:p-6">
                    <div class="flex items-start justify-between gap-4">
                        <dt class="font-semibold text-patungan-grey">Harga asli</dt>
                        <dd class="text-right font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="font-semibold text-patungan-grey">Harga patungan</dt>
                        <dd class="text-right font-bold">Rp {{ number_format($product->price_per_person, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="font-semibold text-patungan-grey">Durasi</dt>
                        <dd class="text-right font-bold">{{ $product->duration }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="font-semibold text-patungan-grey">Kapasitas grup</dt>
                        <dd class="text-right font-bold">{{ $product->capacity }} orang</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="font-semibold text-patungan-grey">Biaya admin</dt>
                        <dd class="text-right font-bold">Rp {{ number_format($totalPpn, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4 border-t border-patungan-border pt-4">
                        <dt class="font-bold">Total</dt>
                        <dd class="text-right text-xl font-extrabold text-patungan-red">Rp {{ number_format($grandTotal, 0, ',', '.') }}</dd>
                    </div>
                </dl>
                <a href="{{ route('front.booking', $product) }}" class="btn-primary motion-glow mt-6 w-full">Pesan Sekarang</a>
            </aside>
        </div>
    </main>
@endsection
