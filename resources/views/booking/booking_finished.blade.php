@extends('layouts.master')

@section('title', 'Booking Berhasil — Split TheBill')

@section('content')
    <div class="transaction-screen">
        <main id="main-content" class="transaction-page transaction-screen__main site-shell flex min-h-screen min-h-dvh flex-col items-center justify-between gap-8 py-6 sm:py-10">
            <a href="{{ route('front.index') }}" class="transaction-brand shrink-0" aria-label="Split TheBill — Beranda">
                <img src="{{ asset('assets/images/logos/logoo.svg') }}" class="brand-logo--light h-9 w-auto sm:h-10" alt="Split TheBill">
                <img src="{{ asset('assets/images/logos/logos.svg') }}" class="brand-logo--dark h-9 w-auto sm:h-10" alt="Split TheBill">
            </a>

            <section class="transaction-panel w-full max-w-xl p-5 sm:p-10" aria-labelledby="booking-success-title">
                <img src="{{ asset('assets/images/icons/receipt-text-orange-fill.svg') }}" class="transaction-panel__icon h-10 w-10" alt="" aria-hidden="true">
                <h1 id="booking-success-title" class="section-title mt-5">Booking berhasil dibuat!</h1>
                <p class="mt-3 font-medium leading-7 text-patungan-grey">Kami sedang memverifikasi pembayaran kamu. Simpan kode berikut untuk melihat status pesanan.</p>

                <div class="mt-7 rounded-2xl border border-patungan-border bg-patungan-bg-grey p-4 sm:p-5">
                    <p class="text-sm font-semibold text-patungan-grey">Kode booking</p>
                    <p class="mt-1 break-all text-xl font-extrabold sm:text-2xl">{{ $productSubscription->booking_trx_id }}</p>
                </div>

                <a href="{{ route('front.check_booking') }}" class="btn-primary motion-glow mt-7 w-full">Lihat Pesananku</a>
            </section>

            <a href="{{ route('front.index') }}" class="transaction-home-link font-bold underline-offset-4 hover:underline">Kembali ke Beranda</a>
        </main>
    </div>
@endsection
