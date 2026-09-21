<?php

namespace Tests\Feature;

use App\Models\GroupParticipant;
use App\Models\Product;
use App\Models\ProductSubscription;
use App\Models\SubscriptionGroup;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Tests\TestCase;

class BackendIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_finished_booking_requires_a_valid_signature(): void
    {
        $subscription = $this->createSubscription();

        $this->get(route('front.booking_finished', $subscription))->assertForbidden();

        $signedUrl = URL::temporarySignedRoute(
            'front.booking_finished',
            now()->addMinute(),
            ['productSubscription' => $subscription],
        );

        $this->get($signedUrl)->assertOk();
    }

    public function test_payment_checkout_token_is_idempotent(): void
    {
        Storage::fake('local');
        config()->set('payment.destination_banks.bca.account_number', '4444 5555 6666');
        $product = $this->createProduct();
        $checkoutToken = (string) Str::uuid();
        $bookingData = [
            'product_id' => $product->id,
            'name' => 'Idempotent Customer',
            'email' => 'idempotent@example.com',
            'phone' => '+6281234567890',
            'duration' => $product->duration,
            'price' => $product->price_per_person,
            'total_admin' => 5_000,
            'total_amount' => 55_000,
            'checkout_token' => $checkoutToken,
        ];
        $service = app(BookingService::class);

        session(['booking_data' => $bookingData]);
        $firstId = $service->paymentStore($this->paymentData('first.png'));

        session(['booking_data' => $bookingData]);
        $secondId = $service->paymentStore($this->paymentData('second.png'));

        $this->assertSame($firstId, $secondId);
        $this->assertDatabaseCount('product_subscriptions', 1);
        $this->assertCount(1, Storage::disk('local')->allFiles('proofs'));
        $this->assertNull(session('booking_data'));
    }

    public function test_subscription_created_as_paid_is_assigned_once(): void
    {
        $subscription = $this->createSubscription(isPaid: true);

        $this->assertDatabaseCount('subscription_groups', 1);
        $this->assertDatabaseCount('group_participants', 1);
        $this->assertSame(1, SubscriptionGroup::query()->sole()->participant_count);
        $this->assertSame(
            $subscription->booking_trx_id,
            GroupParticipant::query()->sole()->booking_trx_id,
        );
    }

    public function test_verified_booking_lookup_uses_a_temporary_url_for_private_proof(): void
    {
        $proofPath = 'proofs/security-'.Str::uuid().'.png';
        Storage::disk('local')->put($proofPath, 'private payment proof');

        try {
            $subscription = $this->createSubscription();
            $subscription->update(['proof' => $proofPath]);

            $bookingData = app(BookingService::class)->getBookingDetailsWithGroupAndCapacity([
                'booking_trx_id' => $subscription->booking_trx_id,
                'phone' => $subscription->phone,
            ]);
            $temporaryProofUrl = $bookingData['bookingDetails']->proof_url;

            $this->assertStringContainsString('signature=', $temporaryProofUrl);
            $this->get(Storage::disk('local')->url($proofPath))->assertForbidden();
            $proofResponse = $this->get($temporaryProofUrl);
            $proofResponse->assertOk();

            $cacheControl = $proofResponse->headers->get('Cache-Control', '');
            foreach (['no-store', 'no-cache', 'must-revalidate', 'max-age=0'] as $directive) {
                $this->assertStringContainsString($directive, $cacheControl);
            }
        } finally {
            Storage::disk('local')->delete($proofPath);
        }
    }

    private function createSubscription(bool $isPaid = false): ProductSubscription
    {
        $product = $this->createProduct();

        return ProductSubscription::query()->create([
            'name' => 'Secure Customer',
            'phone' => '+6281234567890',
            'email' => 'secure@example.com',
            'customer_bank_name' => 'Bank BCA',
            'customer_bank_number' => '1234567890',
            'customer_bank_account' => 'Secure Customer',
            'proof' => 'proofs/secure.png',
            'total_amount' => 55_000,
            'duration' => '1 Bulan',
            'total_tax_amount' => 5_000,
            'price' => 50_000,
            'is_paid' => $isPaid,
            'product_id' => $product->id,
        ]);
    }

    private function createProduct(): Product
    {
        return Product::query()->create([
            'name' => 'Secure Product '.Str::random(8),
            'thumbnail' => 'products/thumbnail.png',
            'photo' => 'products/photo.png',
            'about' => 'Shared premium subscription.',
            'tagline' => 'Share safely.',
            'price' => 100_000,
            'price_per_person' => 50_000,
            'duration' => '1 Bulan',
            'capacity' => 2,
            'is_popular' => true,
        ]);
    }

    private function paymentData(string $fileName): array
    {
        $contents = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=',
            true,
        );

        return [
            'proof' => UploadedFile::fake()->createWithContent($fileName, $contents),
            'destination_bank' => 'bca',
            'customer_bank_name' => 'Bank BCA',
            'customer_bank_account' => 'Idempotent Customer',
            'customer_bank_number' => '1234567890',
        ];
    }
}
