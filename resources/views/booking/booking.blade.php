@extends('layouts.master')

@section('title', 'Booking ' . $product->name . ' — Split TheBill')

@section('content')
    <x-navbar />

    <main id="main-content" class="site-shell py-8 sm:py-10 lg:py-14">
        <header class="reveal-on-scroll mb-8 space-y-4 sm:mb-10">
            <nav aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2 text-sm font-semibold text-patungan-grey sm:text-base">
                    <li><a href="{{ route('front.index') }}" class="transition hover:text-patungan-black">Beranda</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('front.details', $product) }}" class="transition hover:text-patungan-black">{{ $product->name }}</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-patungan-black" aria-current="page">Checkout</li>
                </ol>
            </nav>
            <h1 class="section-title">Booking Akun</h1>
        </header>

        <div class="grid min-w-0 gap-8 lg:grid-cols-3 lg:items-start xl:gap-10">
            <aside class="surface-card motion-card reveal-on-scroll overflow-hidden lg:sticky lg:top-28" aria-labelledby="selected-product-title">
                <div class="h-40 bg-[#D9D9D9] sm:h-48 lg:h-40">
                    <img src="{{ $product->thumbnail_url }}" class="h-full w-full object-cover" alt="Banner {{ $product->name }}" decoding="async">
                </div>
                <div class="space-y-5 p-5 sm:p-6">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl">
                            <img src="{{ $product->photo_url }}" class="h-full w-full object-contain" alt="Logo {{ $product->name }}" width="56" height="56" decoding="async">
                        </div>
                        <div class="min-w-0">
                            <h2 id="selected-product-title" class="break-words text-xl font-bold">{{ $product->name }}</h2>
                            <div class="mt-1 flex items-center gap-1" aria-label="Rating 4,9 dari 5">
                                <img src="{{ asset('assets/images/icons/Star.svg') }}" class="h-5 w-5" alt="" aria-hidden="true">
                                <span class="font-bold">4,9</span>
                                <span class="text-sm font-semibold text-patungan-grey">(2.120 ulasan)</span>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-patungan-border p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between lg:flex-col lg:items-start xl:flex-row xl:items-center">
                            <p class="text-xl font-extrabold">Rp {{ number_format($product->price_per_person, 0, ',', '.') }}</p>
                            <div class="flex w-fit items-center gap-1 rounded-lg bg-patungan-red/10 px-3 py-2">
                                <img src="{{ asset('assets/images/icons/clock-red.svg') }}" class="h-5 w-5" alt="" aria-hidden="true">
                                <span class="font-bold text-patungan-red">{{ $product->duration }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <form method="POST" action="{{ route('front.booking_store', $product->slug) }}" class="surface-card motion-card reveal-on-scroll reveal-delay-1 min-w-0 overflow-hidden lg:col-span-2">
                @csrf

                <div class="bg-[#007B9D] px-5 py-5 text-center text-sm font-bold leading-6 text-white sm:px-8 sm:text-base">
                    Masukkan data dengan benar. Bukti pesanan akan kami kirim ke email kamu.
                </div>

                <div class="space-y-8 p-5 sm:p-8">
                    @if ($errors->any())
                        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800" role="alert" aria-labelledby="booking-errors-title">
                            <p id="booking-errors-title" class="font-bold">Mohon periksa kembali data berikut:</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm font-medium">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <fieldset class="space-y-5">
                        <legend class="text-xl font-bold">Informasi Pribadi</legend>

                        <div>
                            <label for="name" class="mb-2 block font-bold text-patungan-grey">Nama lengkap</label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name') }}"
                                class="form-control @error('name') border-red-500 @enderror"
                                autocomplete="name"
                                placeholder="Masukkan nama lengkap"
                                required
                                @error('name')
                                    aria-describedby="name-error"
                                    aria-invalid="true"
                                @enderror
                            >
                            @error('name')
                                <p id="name-error" class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone-number" class="mb-2 block font-bold text-patungan-grey">Nomor WhatsApp</label>
                            <input
                                type="tel"
                                name="phone"
                                id="phone-number"
                                value="{{ old('phone') }}"
                                class="form-control @error('phone') border-red-500 @enderror"
                                autocomplete="tel"
                                inputmode="tel"
                                placeholder="+62 812 3456 7890"
                                required
                                @error('phone')
                                    aria-describedby="phone-error"
                                    aria-invalid="true"
                                @enderror
                            >
                            @error('phone')
                                <p id="phone-error" class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="mb-2 block font-bold text-patungan-grey">Alamat email</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                class="form-control @error('email') border-red-500 @enderror"
                                autocomplete="email"
                                placeholder="nama@email.com"
                                required
                                @error('email')
                                    aria-describedby="email-error"
                                    aria-invalid="true"
                                @enderror
                            >
                            @error('email')
                                <p id="email-error" class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </fieldset>

                    <section aria-labelledby="booking-price-title">
                        <h2 id="booking-price-title" class="text-xl font-bold">Rincian Harga</h2>
                        <dl class="mt-5 space-y-4 rounded-2xl border border-patungan-border p-4 sm:rounded-3xl sm:p-6">
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
                                <dd class="text-right font-bold">Rp {{ number_format($totalTaxAmount, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-4 border-t border-patungan-border pt-4">
                                <dt class="font-bold">Total</dt>
                                <dd class="text-right text-xl font-extrabold text-patungan-red">Rp {{ number_format($grandTotalAmount, 0, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </section>

                    <button type="submit" class="btn-primary motion-glow w-full">Lanjut ke Pembayaran</button>
                </div>
            </form>
        </div>
    </main>
@endsection

@push('after-scripts')
    <script src="{{ asset('js/whatsapp-number.js') }}"></script>
@endpush
