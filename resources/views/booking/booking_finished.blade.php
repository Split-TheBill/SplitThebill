@extends('layouts.master')

@section('title', 'Booking Berhasil — Split TheBill')

@section('content')
    <div class="relative min-h-screen min-h-dvh overflow-hidden bg-[linear-gradient(113.19deg,#092267_0%,#06061C_100%)]">
        <img src="{{ asset('assets/images/backgrounds/Full-bg.svg') }}" class="motion-glow absolute inset-0 h-full w-full object-cover" alt="" aria-hidden="true">

        <main id="main-content" class="site-shell relative flex min-h-screen min-h-dvh flex-col items-center justify-between gap-8 py-6 sm:py-10">
            <a href="{{ route('front.index') }}" class="reveal-on-scroll shrink-0" aria-label="Split TheBill — Beranda">
                <img src="{{ asset('assets/images/logos/logos.svg') }}" class="h-9 w-auto sm:h-10" alt="Split TheBill">
            </a>

            <section class="motion-card reveal-on-scroll reveal-delay-1 w-full max-w-xl rounded-[32px] bg-white p-5 text-center shadow-2xl sm:rounded-[48px] sm:p-10" aria-labelledby="booking-success-title">
                <img src="{{ asset('assets/images/icons/receipt-text-orange-fill.svg') }}" class="motion-float mx-auto h-14 w-14 sm:h-[62px] sm:w-[62px]" alt="" aria-hidden="true">
                <h1 id="booking-success-title" class="section-title mt-5">Booking berhasil dibuat!</h1>
                <p class="mt-3 font-medium leading-7 text-patungan-grey">Kami sedang memverifikasi pembayaran kamu. Simpan kode berikut untuk melihat status pesanan.</p>

                <div class="mt-7 rounded-2xl border border-patungan-border bg-patungan-bg-grey p-4 sm:p-5">
                    <p class="text-sm font-semibold text-patungan-grey">Kode booking</p>
                    <p class="mt-1 break-all text-xl font-extrabold sm:text-2xl">{{ $productSubscription->booking_trx_id }}</p>
                </div>

                <a href="{{ route('front.check_booking') }}" class="btn-primary motion-glow mt-7 w-full">Lihat Pesananku</a>
            </section>

            <a href="{{ route('front.index') }}" class="font-bold text-white underline-offset-4 hover:underline">Kembali ke Beranda</a>
        </main>
    </div>
@endsection
