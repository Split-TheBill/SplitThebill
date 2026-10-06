@extends('layouts.master')

@section('title', 'Cek Pesanan — Split TheBill')

@section('content')
    <div class="transaction-screen">
        <main id="main-content" class="transaction-page transaction-screen__main site-shell flex min-h-screen min-h-dvh flex-col items-center justify-between gap-8 py-6 sm:py-10">
            <a href="{{ route('front.index') }}" class="transaction-brand shrink-0" aria-label="Split TheBill — Beranda">
                <img src="{{ asset('assets/images/logos/logoo.svg') }}" class="brand-logo--light h-9 w-auto sm:h-10" alt="Split TheBill">
                <img src="{{ asset('assets/images/logos/logos.svg') }}" class="brand-logo--dark h-9 w-auto sm:h-10" alt="Split TheBill">
            </a>

            <form action="{{ route('front.check_booking_details') }}" method="POST" class="transaction-panel w-full max-w-2xl p-5 sm:p-9 lg:p-12">
                @csrf

                <div class="transaction-panel__heading">
                    <img src="{{ asset('assets/images/icons/receipt-text-orange-fill-1.svg') }}" class="transaction-panel__icon h-10 w-10" alt="" aria-hidden="true">
                    <h1 class="section-title mt-5">Lihat Pesanan Kamu</h1>
                    <p class="mx-auto mt-3 max-w-xl font-medium leading-7 text-patungan-grey">Masukkan kode booking dan nomor WhatsApp yang digunakan saat memesan.</p>
                </div>

                @if ($errors->any())
                    <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-left text-red-800" role="alert" aria-labelledby="check-booking-errors-title">
                        <p id="check-booking-errors-title" class="font-bold">Pesanan belum dapat ditemukan:</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-7 grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="booking-trx-id" class="mb-2 block font-bold text-patungan-grey">Kode booking</label>
                        <input
                            type="text"
                            name="booking_trx_id"
                            id="booking-trx-id"
                            value="{{ old('booking_trx_id') }}"
                            class="form-control @error('booking_trx_id') border-red-500 @enderror"
                            autocomplete="off"
                            placeholder="Contoh: STB12345"
                            required
                            @error('booking_trx_id')
                                aria-describedby="booking-trx-id-error"
                                aria-invalid="true"
                            @enderror
                        >
                        @error('booking_trx_id')
                            <p id="booking-trx-id-error" class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
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
                </div>

                <button type="submit" class="btn-primary motion-glow mt-7 w-full">Lihat Pesananku</button>
            </form>

            <a href="{{ route('front.index') }}" class="transaction-home-link font-bold underline-offset-4 hover:underline">Kembali ke Beranda</a>
        </main>
    </div>
@endsection

@push('after-scripts')
    <script src="{{ asset('js/whatsapp-number.js') }}"></script>
@endpush
