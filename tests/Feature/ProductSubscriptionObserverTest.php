<?php

namespace Tests\Feature;

use App\Models\GroupParticipant;
use App\Models\Product;
use App\Models\ProductSubscription;
use App\Models\SubscriptionGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSubscriptionObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_an_already_paid_subscription_assigns_one_group_membership(): void
    {
        $product = $this->createProduct();
        $subscription = $this->createSubscription(
            $product,
            isPaid: true,
            bookingCode: 'BOOKING-PAID-001',
        );

        $this->assertDatabaseCount('subscription_groups', 1);
        $this->assertDatabaseCount('group_participants', 1);
        $this->assertDatabaseHas('group_participants', [
            'booking_trx_id' => $subscription->booking_trx_id,
            'subscription_group_id' => SubscriptionGroup::query()->sole()->id,
        ]);
        $this->assertSame(1, SubscriptionGroup::query()->sole()->participant_count);
    }

    public function test_reapproving_the_same_subscription_does_not_duplicate_its_group_membership(): void
    {
        $product = $this->createProduct();
        $subscription = $this->createSubscription(
            $product,
            isPaid: false,
            bookingCode: 'BOOKING-IDEMPOTENT-001',
        );

        $subscription->update(['is_paid' => true]);
        $subscription->update(['is_paid' => false]);
        $subscription->update(['is_paid' => true]);

        $this->assertDatabaseCount('subscription_groups', 1);
        $this->assertDatabaseCount('group_participants', 1);
        $this->assertDatabaseHas('group_participants', [
            'booking_trx_id' => $subscription->booking_trx_id,
            'subscription_group_id' => SubscriptionGroup::query()->sole()->id,
        ]);
        $this->assertSame(
            1,
            GroupParticipant::query()
                ->where('booking_trx_id', $subscription->booking_trx_id)
                ->count(),
        );
        $this->assertSame(1, SubscriptionGroup::query()->sole()->participant_count);
    }

    public function test_force_deleting_the_group_founder_preserves_the_other_paid_member(): void
    {
        $product = $this->createProduct();
        $founder = $this->createSubscription(
            $product,
            isPaid: true,
            bookingCode: 'BOOKING-FOUNDER-001',
        );
        $remainingSubscription = $this->createSubscription(
            $product,
            isPaid: true,
            bookingCode: 'BOOKING-MEMBER-002',
        );
        $group = SubscriptionGroup::query()->sole();

        $this->assertSame($founder->id, $group->product_subscription_id);
        $this->assertSame(2, $group->participant_count);

        $founder->forceDelete();

        $this->assertDatabaseMissing('product_subscriptions', ['id' => $founder->id]);
        $this->assertDatabaseCount('subscription_groups', 1);
        $this->assertDatabaseHas('subscription_groups', [
            'id' => $group->id,
            'product_subscription_id' => null,
            'participant_count' => 1,
        ]);
        $this->assertDatabaseCount('group_participants', 1);
        $this->assertDatabaseHas('group_participants', [
            'booking_trx_id' => $remainingSubscription->booking_trx_id,
            'subscription_group_id' => $group->id,
        ]);
        $this->assertDatabaseMissing('group_participants', [
            'booking_trx_id' => $founder->booking_trx_id,
        ]);
    }

    private function createProduct(): Product
    {
        return Product::query()->create([
            'name' => 'YouTube Premium',
            'thumbnail' => 'products/youtube-thumbnail.png',
            'photo' => 'products/youtube-logo.png',
            'about' => 'Shared premium subscription.',
            'tagline' => 'Watch together.',
            'price' => 100_000,
            'price_per_person' => 50_000,
            'duration' => '1 Bulan',
            'capacity' => 2,
            'is_popular' => true,
        ]);
    }

    private function createSubscription(
        Product $product,
        bool $isPaid,
        string $bookingCode,
    ): ProductSubscription {
        return ProductSubscription::query()->create([
            'booking_trx_id' => $bookingCode,
            'name' => 'Repeat Customer',
            'phone' => '+6281234567890',
            'email' => 'repeat@example.com',
            'customer_bank_name' => 'Bank BCA',
            'customer_bank_number' => '1234567890',
            'customer_bank_account' => 'Repeat Customer',
            'proof' => 'proofs/repeat.png',
            'total_amount' => 55_000,
            'duration' => '1 Bulan',
            'total_tax_amount' => 5_000,
            'price' => 50_000,
            'is_paid' => $isPaid,
            'product_id' => $product->id,
        ]);
    }
}
