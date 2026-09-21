<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentDestinationBankTest extends TestCase
{
    use RefreshDatabase;

    public function test_destination_bank_is_required_and_must_be_configured(): void
    {
        Storage::fake('local');
        $product = $this->createProduct();

        $this->withSession(['booking_data' => $this->bookingData($product)])
            ->post(route('front.payment_store'), $this->paymentData())
            ->assertSessionHasErrors('destination_bank');

        $this->assertDatabaseCount('product_subscriptions', 0);

        $this->withSession(['booking_data' => $this->bookingData($product)])
            ->post(route('front.payment_store'), [
                ...$this->paymentData(),
                'destination_bank' => 'not-a-configured-bank',
            ])
            ->assertSessionHasErrors('destination_bank');

        $this->assertDatabaseCount('product_subscriptions', 0);
    }

    public function test_selected_destination_account_is_snapshotted_and_rendered_from_the_booking(): void
    {
        Storage::fake('local');
        $product = $this->createProduct();

        config()->set('payment.destination_account_name', 'Split TheBill Testing');
        config()->set('payment.destination_banks.bri.account_number', '1111 2222 3333');

        $this->withSession(['booking_data' => $this->bookingData($product)])
            ->post(route('front.payment_store'), [
                ...$this->paymentData(),
                'destination_bank' => 'bri',
            ])
            ->assertRedirect();

        $subscription = ProductSubscription::query()->sole();

        $this->assertSame('BRI', $subscription->destination_bank_name);
        $this->assertSame('Split TheBill Testing', $subscription->destination_bank_account_name);
        $this->assertSame('1111 2222 3333', $subscription->destination_bank_account_number);

        config()->set('payment.destination_account_name', 'Changed Account');
        config()->set('payment.destination_banks.bri.account_number', '9999 9999 9999');

        $subscription->is_paid = true;
        $subscription->saveQuietly();
        $subscription->load('product');

        $this->view('booking.check_booking_details', [
            'bookingDetails' => $subscription,
            'subscriptionGroup' => null,
            'productCapacity' => $product->capacity,
        ])
            ->assertSee('BRI')
            ->assertSee('1111 2222 3333')
            ->assertSee('Split TheBill Testing')
            ->assertDontSee('9999 9999 9999')
            ->assertDontSee('Changed Account');
    }

    public function test_payment_page_reads_destination_accounts_from_configuration(): void
    {
        $product = $this->createProduct();

        config()->set('payment.destination_account_name', 'Configured Receiver');
        config()->set('payment.destination_banks.bca.account_number', '9876 5432 1000');

        $this->withSession(['booking_data' => $this->bookingData($product)])
            ->get(route('front.payment'))
            ->assertOk()
            ->assertSee('name="destination_bank"', false)
            ->assertSee('9876 5432 1000')
            ->assertSee('Configured Receiver')
            ->assertDontSee('PT Seaccount Angga');
    }

    private function bookingData(Product $product): array
    {
        return [
            'product_id' => $product->id,
            'name' => 'Destination Customer',
            'email' => 'destination@example.com',
            'phone' => '+6281234567890',
            'duration' => $product->duration,
            'price' => (int) $product->price_per_person,
            'sub_total' => (int) $product->price_per_person,
            'total_admin' => 5_000,
            'total_amount' => 55_000,
            'checkout_token' => (string) Str::uuid(),
        ];
    }

    private function paymentData(): array
    {
        return [
            'proof' => $this->fakePng('destination-proof.png'),
            'customer_bank_name' => 'Bank BCA',
            'customer_bank_account' => 'Destination Customer',
            'customer_bank_number' => '1234567890',
        ];
    }

    private function createProduct(): Product
    {
        return Product::query()->create([
            'name' => 'Destination Product',
            'thumbnail' => 'products/destination-thumbnail.png',
            'photo' => 'products/destination-logo.png',
            'about' => 'Destination bank test product.',
            'tagline' => 'Pay securely.',
            'price' => 100_000,
            'price_per_person' => 50_000,
            'duration' => '1 Bulan',
            'capacity' => 2,
            'is_popular' => false,
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
