@extends('layouts.master')

@section('title', 'Detail Pesanan — Split TheBill')

@section('content')
    <x-navbar />

    @if ($bookingDetails->is_paid)
        @php
            $participants = $subscriptionGroup?->groupParticipants ?? collect();
            $messages = $subscriptionGroup?->groupMessages ?? collect();
            $totalParticipants = $participants->count();
            $remainingSlots = max(0, (int) $productCapacity - $totalParticipants);
            $destinationBanks = config('payment.destination_banks', []);
            $activeDestinationBanks = collect($destinationBanks)
                ->filter(fn ($bank): bool => is_array($bank) && filled($bank['account_number'] ?? null));
            $defaultDestinationBankKey = config('payment.default_destination_bank');
            $defaultDestinationBank = $activeDestinationBanks->get($defaultDestinationBankKey)
                ?? $activeDestinationBanks->first()
                ?? [];
            $destinationBankName = $bookingDetails->destination_bank_name ?? ($defaultDestinationBank['name'] ?? 'Rekening tujuan');
            $destinationBankAccountName = $bookingDetails->destination_bank_account_name ?? config('payment.destination_account_name');
            $destinationBankAccountNumber = $bookingDetails->destination_bank_account_number ?? ($defaultDestinationBank['account_number'] ?? null);
            $destinationBankConfig = collect($destinationBanks)->first(
                fn (array $bank): bool => ($bank['name'] ?? null) === $destinationBankName,
            );
        @endphp

        <main id="main-content" class="site-shell py-8 sm:py-10 lg:py-14">
            <header class="reveal-on-scroll mb-8 space-y-4 sm:mb-10">
                <nav aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center gap-2 text-sm font-semibold text-patungan-grey sm:text-base">
                        <li><a href="{{ route('front.index') }}" class="transition hover:text-patungan-black">Beranda</a></li>
                        <li aria-hidden="true">/</li>
                        <li><a href="{{ route('front.check_booking') }}" class="transition hover:text-patungan-black">Pesanan Saya</a></li>
                        <li aria-hidden="true">/</li>
                        <li class="text-patungan-black" aria-current="page">{{ $bookingDetails->booking_trx_id }}</li>
                    </ol>
                </nav>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="section-kicker">Pesanan terverifikasi</p>
                        <h1 class="section-title mt-2">Detail Pesanan Kamu</h1>
                    </div>
                    <p class="w-fit rounded-full bg-[#3D7452] px-4 py-2 text-sm font-bold text-white">Aktif</p>
                </div>
            </header>

            <div class="grid min-w-0 gap-8 xl:grid-cols-[minmax(0,2fr)_minmax(300px,1fr)] xl:items-start xl:gap-10">
                <div class="min-w-0 space-y-7">
                    <div class="reveal-on-scroll grid grid-cols-2 gap-3 sm:max-w-xl sm:gap-4" role="tablist" aria-label="Informasi pesanan">
                        <button
                            type="button"
                            id="broadcast-tab"
                            class="tab-link rounded-2xl bg-white px-3 py-4 text-sm font-bold transition aria-selected:bg-[#170C36] aria-selected:text-white sm:px-6 sm:text-base"
                            data-target-tab="#Broadcast-Message-Tab"
                            role="tab"
                            aria-controls="Broadcast-Message-Tab"
                            aria-selected="true"
                            tabindex="0"
                        >
                            Pesan Grup
                        </button>
                        <button
                            type="button"
                            id="order-details-tab"
                            class="tab-link rounded-2xl bg-white px-3 py-4 text-sm font-bold transition aria-selected:bg-[#170C36] aria-selected:text-white sm:px-6 sm:text-base"
                            data-target-tab="#Order-Details-Tab"
                            role="tab"
                            aria-controls="Order-Details-Tab"
                            aria-selected="false"
                            tabindex="-1"
                        >
                            Detail Pesanan
                        </button>
                    </div>

                    <section id="Broadcast-Message-Tab" class="tab-content surface-card motion-card reveal-on-scroll reveal-delay-1 min-w-0 p-5 sm:p-8" role="tabpanel" aria-labelledby="broadcast-tab" tabindex="0">
                        <div class="flex flex-col gap-4 rounded-2xl border border-patungan-border p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                            <div class="flex min-w-0 items-center gap-3 sm:gap-4">
                                <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl sm:h-[62px] sm:w-[62px]">
                                    <img src="{{ $bookingDetails->product->photo_url }}" class="h-full w-full object-contain" alt="Logo {{ $bookingDetails->product->name }}" width="62" height="62">
                                </div>
                                <div class="min-w-0">
                                    <h2 class="break-words text-xl font-bold">{{ $bookingDetails->product->name }}</h2>
                                    <p class="font-semibold text-patungan-grey">Rp {{ number_format($bookingDetails->product->price_per_person, 0, ',', '.') }} <span class="text-sm">/orang</span></p>
                                </div>
                            </div>
                            <div class="flex w-fit items-center gap-1 rounded-lg bg-patungan-red/10 px-3 py-2">
                                <img src="{{ asset('assets/images/icons/clock-red.svg') }}" class="h-5 w-5" alt="" aria-hidden="true">
                                <span class="font-bold text-patungan-red">{{ $bookingDetails->product->duration }}</span>
                            </div>
                        </div>

                        <div class="mt-7">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <h2 class="text-xl font-bold">Pesan dari Admin</h2>
                                <span class="rounded-full bg-[#3D7452] px-4 py-2 text-sm font-bold text-white">Terkirim</span>
                            </div>

                            <div class="mt-5 space-y-5">
                                @forelse ($messages as $message)
                                    <article class="flex items-start gap-3 sm:gap-4">
                                        <img src="{{ asset('assets/images/icons/Profile-logo.svg') }}" class="h-11 w-11 shrink-0 rounded-full sm:h-14 sm:w-14" alt="Admin Split TheBill" width="56" height="56" loading="lazy">
                                        <div class="min-w-0 flex-1 rounded-2xl rounded-tl-sm bg-patungan-bg-grey p-4 sm:p-6">
                                            <p class="break-words font-medium leading-7 sm:text-lg sm:leading-8">{{ $message->message }}</p>
                                            <time datetime="{{ $message->created_at->toIso8601String() }}" class="mt-3 block text-sm font-bold text-patungan-grey">{{ $message->created_at->format('d M Y, H:i') }}</time>
                                        </div>
                                    </article>
                                @empty
                                    <div class="rounded-2xl bg-patungan-bg-grey p-5 text-center">
                                        <p class="font-semibold text-patungan-grey">Belum ada pesan dari admin untuk grup ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </section>

                    <section id="Order-Details-Tab" class="tab-content surface-card motion-card hidden min-w-0 space-y-8 p-5 sm:p-8" role="tabpanel" aria-labelledby="order-details-tab" tabindex="0" hidden>
                        <section aria-labelledby="order-summary-title">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <h2 id="order-summary-title" class="text-xl font-bold">Ringkasan Pesanan</h2>
                                <span class="rounded-full bg-[#3D7452] px-4 py-2 text-sm font-bold text-white">Terverifikasi</span>
                            </div>
                            <div class="mt-5 rounded-2xl border border-patungan-border p-4 sm:rounded-3xl sm:p-6">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <img src="{{ $bookingDetails->product->photo_url }}" class="h-14 w-14 shrink-0 rounded-xl object-contain" alt="Logo {{ $bookingDetails->product->name }}" width="56" height="56">
                                        <div class="min-w-0">
                                            <h3 class="break-words text-xl font-bold">{{ $bookingDetails->product->name }}</h3>
                                            <p class="font-semibold text-patungan-grey">Rp {{ number_format($bookingDetails->product->price_per_person, 0, ',', '.') }} /orang</p>
                                        </div>
                                    </div>
                                    <span class="w-fit rounded-lg bg-patungan-red/10 px-3 py-2 font-bold text-patungan-red">{{ $bookingDetails->product->duration }}</span>
                                </div>

                                <dl class="mt-5 space-y-4 border-t border-patungan-border pt-5">
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="font-semibold text-patungan-grey">Tanggal booking</dt>
                                        <dd class="text-right font-bold">{{ $bookingDetails->created_at->format('d M Y') }}</dd>
                                    </div>
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="font-semibold text-patungan-grey">Harga asli</dt>
                                        <dd class="text-right font-bold">Rp {{ number_format($bookingDetails->product->price, 0, ',', '.') }}</dd>
                                    </div>
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="font-semibold text-patungan-grey">Harga patungan</dt>
                                        <dd class="text-right font-bold">Rp {{ number_format($bookingDetails->product->price_per_person, 0, ',', '.') }}</dd>
                                    </div>
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="font-semibold text-patungan-grey">Kapasitas grup</dt>
                                        <dd class="text-right font-bold">{{ $bookingDetails->product->capacity }} orang</dd>
                                    </div>
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="font-semibold text-patungan-grey">Biaya admin</dt>
                                        <dd class="text-right font-bold">Rp {{ number_format($bookingDetails->total_tax_amount, 0, ',', '.') }}</dd>
                                    </div>
                                    <div class="flex items-start justify-between gap-4 border-t border-patungan-border pt-4">
                                        <dt class="font-bold">Total</dt>
                                        <dd class="text-right text-xl font-extrabold text-patungan-red">Rp {{ number_format($bookingDetails->total_amount, 0, ',', '.') }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </section>

                        <section aria-labelledby="personal-info-title">
                            <h2 id="personal-info-title" class="text-xl font-bold">Informasi Pribadi</h2>
                            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                                <div class="rounded-2xl bg-patungan-bg-grey p-4">
                                    <dt class="text-sm font-semibold text-patungan-grey">Nama lengkap</dt>
                                    <dd class="mt-1 break-words font-bold">{{ $bookingDetails->name }}</dd>
                                </div>
                                <div class="rounded-2xl bg-patungan-bg-grey p-4">
                                    <dt class="text-sm font-semibold text-patungan-grey">Nomor WhatsApp</dt>
                                    <dd class="mt-1 break-words font-bold">{{ $bookingDetails->phone }}</dd>
                                </div>
                                <div class="rounded-2xl bg-patungan-bg-grey p-4 sm:col-span-2">
                                    <dt class="text-sm font-semibold text-patungan-grey">Alamat email</dt>
                                    <dd class="mt-1 break-all font-bold">{{ $bookingDetails->email }}</dd>
                                </div>
                            </dl>
                        </section>

                        <section aria-labelledby="transfer-info-title">
                            <h2 id="transfer-info-title" class="text-xl font-bold">Informasi Transfer</h2>
                            <div class="bank-details mt-5 rounded-2xl border border-patungan-border p-4 sm:p-5">
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    @if ($destinationBankConfig && isset($destinationBankConfig['logo']))
                                        <span class="media-well inline-flex rounded-lg px-2 py-1">
                                            <img src="{{ asset('assets/images/logos/' . $destinationBankConfig['logo']) }}" class="h-8 w-20 object-contain object-left" alt="{{ $destinationBankName }}" width="80" height="32" loading="lazy">
                                        </span>
                                    @else
                                        <span class="text-xl font-bold">{{ $destinationBankName }}</span>
                                    @endif
                                    <button type="button" class="copy-btn inline-flex items-center gap-2 font-bold text-[#170C36]" onclick="copyTransferTo(this)" aria-label="Salin nomor rekening tujuan">
                                        <img src="{{ asset('assets/images/icons/copy-orange.svg') }}" class="h-5 w-5" alt="" aria-hidden="true">
                                        <span data-copy-label aria-live="polite">Salin</span>
                                    </button>
                                </div>
                                <p class="mt-4 text-sm font-semibold text-patungan-grey">Transfer ke</p>
                                <p class="Transfer-To mt-1 break-all text-xl font-bold">{{ $destinationBankAccountNumber ?? 'Tidak tersedia' }}</p>
                                <p class="mt-1 font-semibold text-patungan-grey">{{ $destinationBankAccountName }}</p>
                            </div>

                            <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div class="rounded-2xl bg-patungan-bg-grey p-4">
                                    <dt class="text-sm font-semibold text-patungan-grey">Bank pengirim</dt>
                                    <dd class="mt-1 break-words font-bold">{{ $bookingDetails->customer_bank_name }}</dd>
                                </div>
                                <div class="rounded-2xl bg-patungan-bg-grey p-4">
                                    <dt class="text-sm font-semibold text-patungan-grey">Nama pemilik rekening</dt>
                                    <dd class="mt-1 break-words font-bold">{{ $bookingDetails->customer_bank_account }}</dd>
                                </div>
                                <div class="rounded-2xl bg-patungan-bg-grey p-4 sm:col-span-2">
                                    <dt class="text-sm font-semibold text-patungan-grey">Nomor rekening pengirim</dt>
                                    <dd class="mt-1 break-all font-bold">{{ $bookingDetails->customer_bank_number }}</dd>
                                </div>
                            </dl>
                        </section>

                        <section aria-labelledby="proof-title">
                            <h2 id="proof-title" class="text-xl font-bold">Bukti Pembayaran</h2>
                            @if ($bookingDetails->proof_url)
                                <a href="{{ $bookingDetails->proof_url }}" target="_blank" rel="noopener" class="mt-5 block overflow-hidden rounded-2xl border border-patungan-border bg-patungan-bg-grey p-3 transition hover:border-[#170C36]">
                                    <img src="{{ $bookingDetails->proof_url }}" class="mx-auto max-h-72 w-auto rounded-xl object-contain" alt="Bukti pembayaran {{ $bookingDetails->booking_trx_id }}" loading="lazy">
                                    <span class="mt-3 block text-center font-bold text-[#170C36]">Buka gambar ukuran penuh</span>
                                </a>
                            @else
                                <p class="mt-4 rounded-2xl bg-patungan-bg-grey p-4 font-semibold text-patungan-grey">Bukti pembayaran tidak tersedia.</p>
                            @endif
                        </section>
                    </section>
                </div>

                <aside class="motion-card reveal-on-scroll reveal-delay-2 overflow-hidden rounded-[32px] xl:sticky xl:top-28" aria-labelledby="member-status-title">
                    <div class="relative bg-[linear-gradient(113.19deg,#092267_0%,#06061C_100%)] px-5 pb-14 pt-7 text-center">
                        <img src="{{ asset('assets/images/backgrounds/header-lines-bg-small.svg') }}" class="motion-glow absolute inset-0 h-full w-full object-cover" alt="" aria-hidden="true">
                        <div class="relative">
                            <p class="font-semibold text-[#E2B9BB]">Kode booking</p>
                            <p class="mt-1 break-all text-2xl font-extrabold text-white sm:text-[32px]">{{ $bookingDetails->booking_trx_id }}</p>
                        </div>
                    </div>

                    <div class="relative -mt-8 rounded-[32px] bg-white p-5 sm:p-8">
                        <h2 id="member-status-title" class="text-xl font-bold">Anggota Grup {{ $totalParticipants }}/{{ $bookingDetails->product->capacity }}</h2>
                        <hr class="my-5 border-patungan-border">

                        <div class="space-y-5">
                            @foreach ($participants as $participant)
                                <div class="flex min-w-0 items-center gap-3">
                                    <img src="{{ asset('assets/images/icons/member.svg') }}" class="h-12 w-12 shrink-0 rounded-full sm:h-14 sm:w-14" alt="" aria-hidden="true" loading="lazy">
                                    <div class="min-w-0">
                                        <p class="break-words font-bold sm:text-lg">{{ $participant->name }}</p>
                                        <p class="text-sm font-semibold text-patungan-grey">Bergabung {{ $participant->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                            @endforeach

                            @for ($i = 0; $i < $remainingSlots; $i++)
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('assets/images/icons/waiting-member.svg') }}" class="h-12 w-12 shrink-0 rounded-full sm:h-14 sm:w-14" alt="" aria-hidden="true" loading="lazy">
                                    <p class="font-semibold italic text-patungan-grey">Menunggu anggota...</p>
                                </div>
                            @endfor

                            @if ($participants->isEmpty() && $remainingSlots === 0)
                                <p class="rounded-2xl bg-patungan-bg-grey p-4 text-center font-semibold text-patungan-grey">Informasi grup belum tersedia.</p>
                            @endif
                        </div>
                    </div>
                </aside>
            </div>
        </main>
    @else
        <main id="main-content" class="site-shell flex min-h-[70vh] flex-col items-center justify-center py-10 sm:py-14">
            <header class="reveal-on-scroll text-center">
                <nav aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center justify-center gap-2 text-sm font-semibold text-patungan-grey sm:text-base">
                        <li><a href="{{ route('front.index') }}" class="transition hover:text-patungan-black">Beranda</a></li>
                        <li aria-hidden="true">/</li>
                        <li><a href="{{ route('front.check_booking') }}" class="transition hover:text-patungan-black">Pesanan Saya</a></li>
                    </ol>
                </nav>
                <h1 class="section-title mt-4">Detail Pesanan Kamu</h1>
            </header>

            <section class="motion-card reveal-on-scroll reveal-delay-1 mt-7 w-full max-w-3xl overflow-hidden rounded-[32px]" aria-labelledby="pending-status-title">
                <div class="relative bg-[linear-gradient(113.19deg,#092267_0%,#06061C_100%)] px-5 pb-14 pt-7 text-center">
                    <img src="{{ asset('assets/images/backgrounds/header-lines-bg.svg') }}" class="motion-glow absolute inset-0 h-full w-full object-cover" alt="" aria-hidden="true">
                    <div class="relative">
                        <p class="font-semibold text-[#E2B9BB]">Kode booking</p>
                        <p class="mt-1 break-all text-2xl font-extrabold text-white sm:text-[32px]">{{ $bookingDetails->booking_trx_id }}</p>
                    </div>
                </div>
                <div class="relative -mt-8 rounded-[32px] bg-white p-5 text-center sm:p-10 lg:p-12">
                    <div class="mx-auto flex w-fit items-center gap-2 rounded-full bg-patungan-yellow px-4 py-3 text-[#170C36]">
                        <img src="{{ asset('assets/images/icons/clock-white.svg') }}" class="motion-float h-5 w-5" alt="" aria-hidden="true">
                        <span class="font-bold">Menunggu Verifikasi</span>
                    </div>
                    <h2 id="pending-status-title" class="mx-auto mt-7 max-w-2xl text-2xl font-bold leading-tight sm:text-[32px]">Pesananmu sedang kami verifikasi</h2>
                    <p class="mx-auto mt-4 max-w-2xl font-medium leading-7 text-patungan-grey">Mohon tunggu beberapa saat. Setelah pembayaran terverifikasi, kamu akan otomatis mendapatkan akses ke grup patungan yang tersedia.</p>
                    <a href="{{ route('front.check_booking') }}" class="btn-secondary motion-glow mt-7">Cek Pesanan Lain</a>
                </div>
            </section>
        </main>
    @endif
@endsection

@push('after-scripts')
    <script src="{{ asset('js/nav-tab.js') }}"></script>
    <script src="{{ asset('js/copy.js') }}"></script>
@endpush
