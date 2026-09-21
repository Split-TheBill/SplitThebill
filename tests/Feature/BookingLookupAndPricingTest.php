<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingLookupAndPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_lookup_rejects_a_booking_code_with_the_wrong_phone_number(): void
    {
        $subscription = $this->createSubscription();

        $response = $this->post(route('front.check_booking_details'), [
            'booking_trx_id' => $subscription->booking_trx_id,
            'phone' => '+629999999999',
        ]);

        $response
            ->assertRedirect(route('front.check_booking'))
            ->assertSessionHasErrors('error');
    }

    public function test_booking_lookup_accepts_the_matching_normalized_phone_number(): void
    {
        $subscription = $this->createSubscription();

        $response = $this->post(route('front.check_booking_details'), [
            'booking_trx_id' => $subscription->booking_trx_id,
            'phone' => '0812 3456 7890',
        ]);

        $response
            ->assertOk()
            ->assertViewIs('booking.check_booking_details')
            ->assertViewHas(
                'bookingDetails',
                fn (ProductSubscription $booking): bool => $booking->is($subscription),
            );
    }

    public function test_product_details_and_booking_use_the_same_ten_percent_fee(): void
    {
        $product = $this->createProduct();

        $this->get(route('front.details', $product))
            ->assertOk()
            ->assertViewHas('totalPpn', fn ($fee): bool => (int) $fee === 5_000)
            ->assertViewHas('grandTotal', fn ($total): bool => (int) $total === 55_000);

        $this->get(route('front.booking', $product))
            ->assertOk()
            ->assertViewHas('totalTaxAmount', fn ($fee): bool => (int) $fee === 5_000)
            ->assertViewHas('grandTotalAmount', fn ($total): bool => (int) $total === 55_000);
    }

    private function createSubscription(): ProductSubscription
    {
        $product = $this->createProduct();

        return ProductSubscription::query()->create([
            'booking_trx_id' => 'BOOKING-PRIVATE-001',
            'name' => 'Private Customer',
            'phone' => '+6281234567890',
            'email' => 'private@example.com',
            'customer_bank_name' => 'Bank BCA',
            'customer_bank_number' => '1234567890',
            'customer_bank_account' => 'Private Customer',
            'proof' => 'proofs/private.png',
            'total_amount' => 55_000,
            'duration' => '1 Bulan',
            'total_tax_amount' => 5_000,
            'price' => 50_000,
            'is_paid' => false,
            'product_id' => $product->id,
        ]);
    }

    private function createProduct(): Product
    {
        return Product::query()->create([
            'name' => 'Spotify Premium',
            'thumbnail' => 'products/spotify-thumbnail.png',
            'photo' => 'products/spotify-logo.png',
            'about' => 'Shared premium subscription.',
            'tagline' => 'Listen together.',
            'price' => 100_000,
            'price_per_person' => 50_000,
            'duration' => '1 Bulan',
            'capacity' => 2,
            'is_popular' => true,
        ]);
    }
}
