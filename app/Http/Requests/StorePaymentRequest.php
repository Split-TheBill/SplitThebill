<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $destinationBank = $this->input('destination_bank');
        $bankName = $this->input('customer_bank_name');
        $accountName = $this->input('customer_bank_account');
        $accountNumber = $this->input('customer_bank_number');

        $this->merge([
            'destination_bank' => is_string($destinationBank) ? strtolower(trim($destinationBank)) : $destinationBank,
            'customer_bank_name' => is_string($bankName) ? trim($bankName) : $bankName,
            'customer_bank_account' => is_string($accountName) ? trim($accountName) : $accountName,
            'customer_bank_number' => is_string($accountNumber)
                ? preg_replace('/[\s.-]+/', '', trim($accountNumber))
                : $accountNumber,
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $configuredDestinationBanks = collect(config('payment.destination_banks', []))
            ->filter(fn ($bank): bool => is_array($bank) && filled($bank['account_number'] ?? null))
            ->keys()
            ->all();

        return [
            //
            'proof' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:4096'],
            'destination_bank' => [
                'required',
                'string',
                Rule::in($configuredDestinationBanks),
            ],
            'customer_bank_name' => [
                'required',
                'string',
                Rule::in([
                    'Bank Syariah',
                    'Bank Mandiri',
                    'Bank BRI',
                    'Bank BNI',
                    'Bank BCA',
                    'Bank BTN',
                ]),
            ],
            'customer_bank_account' => ['required', 'string', 'max:100'],
            'customer_bank_number' => ['required', 'string', 'regex:/^[0-9]{6,30}$/'],

        ];
    }

    public function messages(): array
    {
        return [
            'proof.required' => 'Bukti pembayaran wajib diunggah.',
            'proof.image' => 'Bukti pembayaran harus berupa gambar.',
            'proof.mimes' => 'Bukti pembayaran harus berformat PNG, JPG, atau JPEG.',
            'proof.max' => 'Ukuran bukti pembayaran maksimal 4 MB.',
            'destination_bank.required' => 'Pilih rekening tujuan pembayaran.',
            'destination_bank.in' => 'Rekening tujuan pembayaran tidak valid.',
            'customer_bank_name.required' => 'Pilih bank asal pembayaran.',
            'customer_bank_name.in' => 'Bank asal pembayaran tidak valid.',
            'customer_bank_account.required' => 'Nama pemilik rekening wajib diisi.',
            'customer_bank_number.required' => 'Nomor rekening wajib diisi.',
            'customer_bank_number.regex' => 'Nomor rekening hanya boleh berisi 6–30 digit.',
        ];
    }
}
