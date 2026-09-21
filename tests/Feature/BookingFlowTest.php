<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_page_without_booking_session_redirects_safely(): void
    {
        $response = $this->get(route('front.payment'));

        $response
            ->assertRedirect(route('front.index'))
            ->assertSessionHasErrors('error');
    }

    public function test_customer_can_book_and_submit_payment_with_a_price_snapshot(): void
    {
        Storage::fake('local');
        config()->set('payment.destination_banks.bri.account_number', '1111 2222 3333');
        $product = $this->createProduct();

        $bookingResponse = $this->post(route('front.booking_store', $product), [
            'name' => 'Calvin Customer',
            'phone' => '0812 3456 7890',
            'email' => 'CALVIN@example.com',
        ]);

        $bookingResponse
            ->assertRedirect(route('front.payment'))
            ->assertSessionHas('booking_data', function (array $bookingData) use ($product): bool {
                return $bookingData['product_id'] === $product->id
                    && $bookingData['phone'] === '+6281234567890'
                    && $bookingData['email'] === 'calvin@example.com'
                    && (int) $bookingData['price'] === 50_000
                    && (int) $bookingData['total_admin'] === 5_000
                    && (int) $bookingData['total_amount'] === 55_000;
            });

        $this->get(route('front.payment'))
            ->assertOk()
            ->assertViewHas('product', fn (Product $viewProduct): bool => $viewProduct->is($product));

        $paymentResponse = $this->post(route('front.payment_store'), [
            'proof' => $this->fakePng('proof.png'),
            'destination_bank' => 'bri',
            'customer_bank_name' => 'Bank BCA',
            'customer_bank_account' => 'Calvin Customer',
            'customer_bank_number' => '1234567890',
        ]);

        $subscription = ProductSubscription::query()->sole();
        $finishedUrl = $paymentResponse->headers->get('Location');

        $paymentResponse
            ->assertRedirect()
            ->assertSessionMissing('booking_data');
        $this->assertIsString($finishedUrl);
        $this->assertStringContainsString(
            "/booking/finished/{$subscription->id}",
            $finishedUrl,
        );
        $this->assertStringContainsString('signature=', $finishedUrl);
        $this->get($finishedUrl)
            ->assertOk()
            ->assertViewHas(
                'productSubscription',
                fn (ProductSubscription $booking): bool => $booking->is($subscription),
            );
        $this->get(route('front.booking_finished', $subscription))->assertForbidden();

        $this->assertDatabaseHas('product_subscriptions', [
            'id' => $subscription->id,
            'product_id' => $product->id,
            'name' => 'Calvin Customer',
            'phone' => '+6281234567890',
            'email' => 'calvin@example.com',
            'destination_bank_name' => 'BRI',
            'destination_bank_account_name' => 'Split TheBill',
            'destination_bank_account_number' => '1111 2222 3333',
            'price' => 50_000,
            'total_tax_amount' => 5_000,
            'total_amount' => 55_000,
            'is_paid' => false,
        ]);
        $this->assertNotEmpty($subscription->booking_trx_id);
        Storage::disk('local')->assertExists($subscription->proof);
    }

    private function createProduct(): Product
    {
        return Product::query()->create([
            'name' => 'Netflix Premium',
            'thumbnail' => 'products/netflix-thumbnail.png',
            'photo' => 'products/netflix-logo.png',
            'about' => 'Shared premium subscription.',
            'tagline' => 'Watch together.',
            'price' => 100_000,
            'price_per_person' => 50_000,
            'duration' => '1 Bulan',
            'capacity' => 2,
            'is_popular' => true,
        ]);
    }

    private function fakePng(string $name): UploadedFile
    {
        $contents = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=',
            true,
        );

        return UploadedFile::fake()->createWithContent($name, $contents);
    }
}
