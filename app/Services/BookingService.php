<?php

namespace App\Services;

use App\Models\GroupParticipant;
use App\Models\Product;
use App\Models\ProductSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

class BookingService
{
    public const ADMIN_FEE_RATE = 0.10;

    public static function calculateAmounts(int $price): array
    {
        $adminFee = (int) round($price * self::ADMIN_FEE_RATE);

        return [
            'admin_fee' => $adminFee,
            'total' => $price + $adminFee,
        ];
    }

    public function getBookingDetails(array $validated)
    {
        return ProductSubscription::where('booking_trx_id', $validated['booking_trx_id'])
            ->where('phone', $validated['phone'])
            ->first();
    }

    protected function calculateBookingData(Product $product, $validatedData)
    {
        $price = (int) $product->price_per_person;
        $amounts = self::calculateAmounts($price);

        return [
            'product_id' => $product->id,
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'duration' => $product->duration,
            'price_per_person' => $price,
            'price' => $price,
            'sub_total' => $price,
            'total_admin' => $amounts['admin_fee'],
            'total_amount' => $amounts['total'],
            'checkout_token' => (string) Str::uuid(),

        ];
    }

    public function storeBookingInSession($product, $validatedData)
    {
        $bookingData = $this->calculateBookingData($product, $validatedData);
        Session::put('booking_data', $bookingData);
    }

    public function payment()
    {
        $booking = session('booking_data', []);
        if (empty($booking)) {
            Log::error('No booking data found in session. ');

            return null;
        }
        $product = Product::find($booking['product_id']);

        if (! $product) {
            Session::forget('booking_data');
            Log::warning('The product for the current booking no longer exists.');

            return null;
        }

        return compact('booking', 'product');
    }

    public function paymentStore(array $validated)
    {
        $bookingData = Session::get('booking_data');
        if (! $bookingData) {
            Log::error('No booking data found in session');

            return null;
        }

        $checkoutToken = $bookingData['checkout_token'] ?? (string) Str::uuid();
        $existingBooking = ProductSubscription::where('checkout_token', $checkoutToken)->first();

        if ($existingBooking) {
            Session::forget('booking_data');

            return $existingBooking->id;
        }

        $destinationBankKey = $validated['destination_bank'] ?? config('payment.default_destination_bank');
        $destinationBank = config("payment.destination_banks.{$destinationBankKey}");

        if (! is_array($destinationBank) || blank($destinationBank['account_number'] ?? null)) {
            throw new InvalidArgumentException('The selected destination bank is not configured.');
        }

        unset($validated['destination_bank']);

        $proofPath = null;

        try {
            $bookingId = DB::transaction(function () use ($validated, $bookingData, $checkoutToken, $destinationBank, &$proofPath) {
                if (isset($validated['proof'])) {
                    $proofPath = $validated['proof']->store('proofs', $this->paymentProofDisk());
                    $validated['proof'] = $proofPath;
                }

                $attributes = array_merge($validated, [
                    'name' => $bookingData['name'],
                    'email' => $bookingData['email'],
                    'phone' => $bookingData['phone'],
                    'duration' => $bookingData['duration'],
                    'price' => $bookingData['price'] ?? $bookingData['price_per_person'] ?? $bookingData['sub_total'],
                    'total_tax_amount' => $bookingData['total_admin'],
                    'total_amount' => $bookingData['total_amount'],
                    'product_id' => $bookingData['product_id'],
                    'checkout_token' => $checkoutToken,
                    'destination_bank_name' => $destinationBank['name'],
                    'destination_bank_account_name' => config('payment.destination_account_name'),
                    'destination_bank_account_number' => $destinationBank['account_number'],
                    'is_paid' => false,
                ]);

                $newBooking = ProductSubscription::firstOrCreate(
                    ['checkout_token' => $checkoutToken],
                    $attributes,
                );

                if (! $newBooking->wasRecentlyCreated && $proofPath) {
                    Storage::disk($this->paymentProofDisk())->delete($proofPath);
                    $proofPath = null;
                }

                return $newBooking->id;
            });
        } catch (Throwable $exception) {
            if ($proofPath) {
                Storage::disk($this->paymentProofDisk())->delete($proofPath);
            }

            throw $exception;
        }

        Session::forget('booking_data');

        return $bookingId;
    }

    public function getBookingDetailsWithGroupAndCapacity(array $validatedData)
    {
        $bookingDetails = ProductSubscription::with(['product'])
            ->where('booking_trx_id', $validatedData['booking_trx_id'])
            ->where('phone', $validatedData['phone'])
            ->first();

        if (! $bookingDetails) {
            return null;
        }

        $group = GroupParticipant::where('booking_trx_id', $bookingDetails->booking_trx_id)->first();

        $subscriptionGroup = null;

        if ($group) {
            $subscriptionGroup = $group->subscriptionGroup()
                ->with(['groupParticipants', 'groupMessages'])
                ->first();
        }

        $productCapacity = $bookingDetails->product->capacity ?? 0;

        $this->attachTemporaryProofAccess($bookingDetails);

        return [
            'bookingDetails' => $bookingDetails,
            'subscriptionGroup' => $subscriptionGroup,
            'productCapacity' => $productCapacity,
        ];
    }

    private function attachTemporaryProofAccess(ProductSubscription $bookingDetails): void
    {
        $proofPath = $bookingDetails->getRawOriginal('proof');

        $disk = Storage::disk($this->paymentProofDisk());

        if (blank($proofPath) || ! $disk->exists($proofPath)) {
            return;
        }

        $bookingDetails->setAttribute(
            'proof_url',
            $disk->temporaryUrl($proofPath, now()->addMinutes(10)),
        );
    }

    private function paymentProofDisk(): string
    {
        return config('filesystems.payment_proof_disk');
    }
}
