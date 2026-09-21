<?php

namespace App\Http\Requests;

use App\Support\PhoneNumber;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCheckBookingRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $bookingTransactionId = $this->input('booking_trx_id');

        $this->merge([
            'booking_trx_id' => is_string($bookingTransactionId)
                ? trim($bookingTransactionId)
                : $bookingTransactionId,
            'phone' => PhoneNumber::normalize($this->input('phone')),
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
        return [
            //

            'booking_trx_id' => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9-]+$/'],
            'phone' => ['required', 'string', 'regex:/^\+62[1-9][0-9]{7,12}$/', 'max:16'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_trx_id.required' => 'Kode booking wajib diisi.',
            'booking_trx_id.regex' => 'Format kode booking tidak valid.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Masukkan nomor WhatsApp Indonesia yang valid.',
        ];
    }
}
