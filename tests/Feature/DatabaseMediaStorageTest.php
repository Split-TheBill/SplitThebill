<?php

namespace Tests\Feature;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Product;
use App\Models\ProductSubscription;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class DatabaseMediaStorageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::forgetDisk(['database-public', 'database-private']);
    }

    public function test_database_disks_serve_public_media_and_signed_private_media(): void
    {
        $image = $this->pngContents();
        $publicPath = 'products/thumbnails/public.png';
        $privatePath = 'proofs/private.png';

        $publicDisk = Storage::disk('database-public');
        $privateDisk = Storage::disk('database-private');

        $publicDisk->put($publicPath, $image);
        $privateDisk->put($privatePath, $image);

        $this->assertDatabaseHas('stored_media', [
            'bucket' => 'public',
            'path' => $publicPath,
            'size' => strlen($image),
            'mime_type' => 'image/png',
            'visibility' => 'public',
        ]);
        $this->assertDatabaseHas('stored_media', [
            'bucket' => 'private',
            'path' => $privatePath,
            'visibility' => 'private',
        ]);

        $publicResponse = $this->get($publicDisk->url($publicPath));
        $publicResponse
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertSame($image, $publicResponse->getContent());

        $this->get(route('media.private', ['path' => $privatePath]))->assertForbidden();

        $privateResponse = $this->get($privateDisk->temporaryUrl($privatePath, now()->addMinute()));
        $privateResponse
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');
        $this->assertStringContainsString('no-store', $privateResponse->headers->get('Cache-Control', ''));
        $this->assertSame($image, $privateResponse->getContent());
    }

    public function test_booking_can_store_and_read_a_proof_from_the_database_disk(): void
    {
        config()->set('filesystems.payment_proof_disk', 'database-private');
        config()->set('payment.destination_banks.bca.account_number', '1234 5678 9000');
        Storage::forgetDisk('database-private');

        $product = $this->createProduct();
        session(['booking_data' => [
            'product_id' => $product->id,
            'name' => 'Database Media Customer',
            'email' => 'media@example.com',
            'phone' => '+6281234567890',
            'duration' => $product->duration,
            'price' => $product->price_per_person,
            'total_admin' => 5_000,
            'total_amount' => 55_000,
            'checkout_token' => (string) Str::uuid(),
        ]]);

        $bookingId = app(BookingService::class)->paymentStore([
            'proof' => UploadedFile::fake()->createWithContent('proof.png', $this->pngContents()),
            'destination_bank' => 'bca',
            'customer_bank_name' => 'Bank BCA',
            'customer_bank_account' => 'Database Media Customer',
            'customer_bank_number' => '1234567890',
        ]);

        $subscription = ProductSubscription::findOrFail($bookingId);

        Storage::disk('database-private')->assertExists($subscription->proof);
        $this->assertDatabaseHas('stored_media', [
            'bucket' => 'private',
            'path' => $subscription->proof,
        ]);

        $details = app(BookingService::class)->getBookingDetailsWithGroupAndCapacity([
            'booking_trx_id' => $subscription->booking_trx_id,
            'phone' => $subscription->phone,
        ]);
        $proofUrl = $details['bookingDetails']->proof_url;

        $this->assertIsString($proofUrl);
        $this->assertStringContainsString('signature=', $proofUrl);
        $this->get($proofUrl)->assertOk();
    }

    public function test_product_urls_follow_the_configured_public_media_disk(): void
    {
        config()->set('filesystems.product_media_disk', 'database-public');
        Storage::forgetDisk('database-public');

        $product = $this->createProduct();
        Storage::disk('database-public')->put($product->thumbnail, $this->pngContents());
        Storage::disk('database-public')->put($product->photo, $this->pngContents());

        $this->assertStringContainsString('/media/public/products/thumbnail.png', $product->thumbnail_url);
        $this->assertStringContainsString('/media/public/products/photo.png', $product->photo_url);
        $this->get($product->thumbnail_url)->assertOk();
        $this->get($product->photo_url)->assertOk();
    }

    public function test_payment_proof_limit_stays_below_the_serverless_request_limit(): void
    {
        $request = new StorePaymentRequest;
        $rules = $request->rules();

        $this->assertContains('max:4096', $rules['proof']);
        $this->assertSame(
            'Ukuran bukti pembayaran maksimal 4 MB.',
            $request->messages()['proof.max'],
        );
    }

    private function createProduct(): Product
    {
        return Product::query()->create([
            'name' => 'Database Media '.Str::random(8),
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

    private function pngContents(): string
    {
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=',
            true,
        );
    }
}
