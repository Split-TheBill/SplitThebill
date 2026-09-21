@extends('layouts.master')

@section('title', 'Pembayaran — Split TheBill')

@section('content')
    <x-navbar />

    @if (empty($product) || empty($booking))
        <main id="main-content" class="site-shell flex min-h-[60vh] items-center justify-center py-12">
            <section class="surface-card motion-card reveal-on-scroll w-full max-w-xl p-6 text-center sm:p-10" role="alert">
                <h1 class="section-title">Data booking tidak ditemukan</h1>
                <p class="mt-4 font-medium leading-7 text-patungan-grey">Sesi booking kamu sudah berakhir atau belum dibuat. Silakan pilih layanan kembali untuk melanjutkan.</p>
                <a href="{{ route('front.index') }}#Products" class="btn-primary mt-7">Pilih Layanan</a>
            </section>
        </main>
    @else
        <main id="main-content" class="site-shell py-8 sm:py-10 lg:py-14">
            <header class="reveal-on-scroll mb-8 space-y-4 sm:mb-10">
                <nav aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center gap-2 text-sm font-semibold text-patungan-grey sm:text-base">
                        <li><a href="{{ route('front.index') }}" class="transition hover:text-patungan-black">Beranda</a></li>
                        <li aria-hidden="true">/</li>
                        <li><a href="{{ route('front.details', $product) }}" class="transition hover:text-patungan-black">{{ $product->name }}</a></li>
                        <li aria-hidden="true">/</li>
                        <li class="text-patungan-black" aria-current="page">Pembayaran</li>
                    </ol>
                </nav>
                <h1 class="section-title">Detail Pembayaran</h1>
            </header>

            <div class="grid min-w-0 gap-8 lg:grid-cols-3 lg:items-start xl:gap-10">
                <aside class="surface-card motion-card reveal-on-scroll overflow-hidden lg:sticky lg:top-28" aria-labelledby="payment-product-title">
                    <div class="h-40 bg-[#D9D9D9] sm:h-48 lg:h-40">
                        <img src="{{ $product->thumbnail_url }}" class="h-full w-full object-cover" alt="Banner {{ $product->name }}" decoding="async">
                    </div>
                    <div class="space-y-5 p-5 sm:p-6">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl">
                                <img src="{{ $product->photo_url }}" class="h-full w-full object-contain" alt="Logo {{ $product->name }}" width="56" height="56" decoding="async">
                            </div>
                            <div class="min-w-0">
                                <h2 id="payment-product-title" class="break-words text-xl font-bold">{{ $product->name }}</h2>
                                <p class="mt-1 font-semibold text-patungan-grey">{{ $product->duration }}</p>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-patungan-border p-4">
                            <p class="text-sm font-semibold text-patungan-grey">Total pembayaran</p>
                            <p class="mt-1 text-2xl font-extrabold">Rp {{ number_format($booking['total_amount'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </aside>

                <form enctype="multipart/form-data" method="POST" action="{{ route('front.payment_store') }}" class="surface-card motion-card reveal-on-scroll reveal-delay-1 min-w-0 p-5 sm:p-8 lg:col-span-2">
                    @csrf

                    <div class="space-y-8">
                        @if ($errors->any())
                            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800" role="alert" aria-labelledby="payment-errors-title">
                                <p id="payment-errors-title" class="font-bold">Mohon periksa kembali data berikut:</p>
                                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm font-medium">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <fieldset class="space-y-5">
                            <legend class="text-xl font-bold">Pilih Rekening Tujuan</legend>
                            <p class="font-medium leading-7 text-patungan-grey">Pilih salah satu rekening lalu salin nomor tujuan untuk melakukan transfer.</p>

                            @php
                                $destinationBanks = collect(config('payment.destination_banks', []))
                                    ->filter(fn ($bank): bool => is_array($bank) && filled($bank['account_number'] ?? null));
                                $destinationAccountName = config('payment.destination_account_name');
                                $configuredDefaultBank = config('payment.default_destination_bank');
                                $defaultDestinationBank = $destinationBanks->has($configuredDefaultBank)
                                    ? $configuredDefaultBank
                                    : $destinationBanks->keys()->first();
                            @endphp

                            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                                @forelse ($destinationBanks as $bankId => $bank)
                                    <div class="group motion-card rounded-2xl border border-patungan-border bg-white p-4 transition has-[:checked]:border-[#170C36] sm:p-5">
                                        <label class="flex cursor-pointer items-center justify-between gap-3" for="destination-{{ $bankId }}">
                                            <span class="flex min-w-0 items-center gap-3">
                                                <span class="media-well inline-flex shrink-0 rounded-lg px-2 py-1">
                                                    <img src="{{ asset('assets/images/logos/' . $bank['logo']) }}" class="h-8 w-16 object-contain object-left" alt="{{ $bank['name'] }}" loading="lazy" width="64" height="32">
                                                </span>
                                                <span class="font-bold">{{ $bank['name'] }}</span>
                                            </span>
                                            <input
                                                type="radio"
                                                name="destination_bank"
                                                id="destination-{{ $bankId }}"
                                                value="{{ $bankId }}"
                                                class="h-5 w-5 shrink-0 accent-[#170C36]"
                                                required
                                                @checked(old('destination_bank', $defaultDestinationBank) === $bankId)
                                                @error('destination_bank') aria-invalid="true" @enderror
                                            >
                                        </label>
                                        <div class="bank-details mt-4 border-t border-patungan-border pt-4">
                                            <p class="text-sm font-semibold text-patungan-grey">Transfer ke</p>
                                            <p class="Transfer-To mt-1 break-all font-bold">{{ $bank['account_number'] }}</p>
                                            <p class="mt-1 text-sm font-semibold text-patungan-grey">{{ $destinationAccountName }}</p>
                                            <button type="button" class="copy-btn mt-3 inline-flex items-center gap-2 font-bold text-[#170C36]" onclick="copyTransferTo(this)" aria-label="Salin nomor rekening {{ $bank['name'] }}">
                                                <img src="{{ asset('assets/images/icons/copy-orange.svg') }}" class="h-5 w-5" alt="" aria-hidden="true">
                                                <span data-copy-label aria-live="polite">Salin</span>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm font-semibold text-amber-900 sm:col-span-2 xl:col-span-3" role="alert">
                                        Rekening tujuan belum tersedia. Silakan hubungi pengelola sebelum melanjutkan pembayaran.
                                    </div>
                                @endforelse
                            </div>
                            @error('destination_bank')
                                <p class="text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <fieldset class="space-y-5">
                            <legend class="text-xl font-bold">Informasi Pengirim</legend>

                            <div>
                                <label for="customer-bank-name" class="mb-2 block font-bold text-patungan-grey">Bank pengirim</label>
                                <select
                                    name="customer_bank_name"
                                    id="customer-bank-name"
                                    class="form-control @error('customer_bank_name') border-red-500 @enderror"
                                    required
                                    @error('customer_bank_name')
                                        aria-describedby="customer-bank-name-error"
                                        aria-invalid="true"
                                    @enderror
                                >
                                    <option value="" disabled @selected(!old('customer_bank_name'))>Pilih bank asal</option>
                                    @foreach (['Bank Syariah', 'Bank Mandiri', 'Bank BRI', 'Bank BNI', 'Bank BCA', 'Bank BTN'] as $bankName)
                                        <option value="{{ $bankName }}" @selected(old('customer_bank_name') === $bankName)>{{ $bankName }}</option>
                                    @endforeach
                                </select>
                                @error('customer_bank_name')
                                    <p id="customer-bank-name-error" class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer-bank-account" class="mb-2 block font-bold text-patungan-grey">Nama pemilik rekening</label>
                                <input
                                    type="text"
                                    name="customer_bank_account"
                                    id="customer-bank-account"
                                    value="{{ old('customer_bank_account') }}"
                                    class="form-control @error('customer_bank_account') border-red-500 @enderror"
                                    autocomplete="name"
                                    placeholder="Nama sesuai rekening"
                                    required
                                    @error('customer_bank_account')
                                        aria-describedby="customer-bank-account-error"
                                        aria-invalid="true"
                                    @enderror
                                >
                                @error('customer_bank_account')
                                    <p id="customer-bank-account-error" class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer-bank-number" class="mb-2 block font-bold text-patungan-grey">Nomor rekening pengirim</label>
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    name="customer_bank_number"
                                    id="customer-bank-number"
                                    value="{{ old('customer_bank_number') }}"
                                    class="form-control @error('customer_bank_number') border-red-500 @enderror"
                                    pattern="[0-9 ]*"
                                    title="Gunakan angka dan spasi saja"
                                    autocomplete="off"
                                    placeholder="Masukkan nomor rekening"
                                    required
                                    @error('customer_bank_number')
                                        aria-describedby="customer-bank-number-error"
                                        aria-invalid="true"
                                    @enderror
                                >
                                @error('customer_bank_number')
                                    <p id="customer-bank-number-error" class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </fieldset>

                        <section aria-labelledby="payment-price-title">
                            <h2 id="payment-price-title" class="text-xl font-bold">Rincian Harga</h2>
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
                                    <dd class="text-right font-bold">Rp {{ number_format($booking['total_admin'], 0, ',', '.') }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-4 border-t border-patungan-border pt-4">
                                    <dt class="font-bold">Total</dt>
                                    <dd class="text-right text-xl font-extrabold text-patungan-red">Rp {{ number_format($booking['total_amount'], 0, ',', '.') }}</dd>
                                </div>
                            </dl>
                        </section>

                        <div>
                            <label for="File-Input" class="mb-2 block text-xl font-bold">Bukti Pembayaran</label>
                            <label for="File-Input" class="flex min-h-28 cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border border-dashed border-patungan-border bg-patungan-bg-grey p-5 text-center transition hover:border-[#170C36]">
                                <img src="{{ asset('assets/images/icons/gallery-import-black.svg') }}" class="h-7 w-7" alt="" aria-hidden="true">
                                <span id="File-Name" class="max-w-full break-all font-bold">Pilih foto bukti transfer</span>
                                <span class="text-sm font-medium text-patungan-grey">PNG atau JPG</span>
                            </label>
                            <input
                                type="file"
                                name="proof"
                                id="File-Input"
                                class="sr-only"
                                accept="image/png,image/jpeg"
                                required
                                aria-describedby="proof-help{{ $errors->has('proof') ? ' proof-error' : '' }}"
                                @error('proof') aria-invalid="true" @enderror
                            >
                            <p id="proof-help" class="mt-2 text-sm text-patungan-grey">Pastikan nominal dan detail transaksi terlihat jelas.</p>
                            @error('proof')
                                <p id="proof-error" class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn-primary motion-glow w-full disabled:cursor-not-allowed disabled:opacity-50" @disabled($destinationBanks->isEmpty())>Kirim Pembayaran</button>
                    </div>
                </form>
            </div>
        </main>
    @endif
@endsection

@push('after-scripts')
    <script src="{{ asset('js/copy.js') }}"></script>
    <script src="{{ asset('js/file-upload.js') }}"></script>
@endpush
