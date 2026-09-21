<?php

namespace Tests\Feature;

use App\Filament\Resources\SubscriptionGroupResource\Pages\EditSubscriptionGroup;
use App\Filament\Resources\SubscriptionGroupResource\RelationManagers\GroupParticipantsRelationManager;
use App\Models\Product;
use App\Models\ProductSubscription;
use App\Models\SubscriptionGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentGroupParticipantsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_group_participant_with_all_required_fields(): void
    {
        $this->actingAs(User::factory()->create());
        $product = Product::query()->create([
            'name' => 'Filament Test Product',
            'thumbnail' => 'products/filament-thumbnail.png',
            'photo' => 'products/filament-logo.png',
            'about' => 'Verifies the participant relation form.',
            'tagline' => 'Admin integrity.',
            'price' => 100_000,
            'price_per_person' => 50_000,
            'duration' => '1 Bulan',
            'capacity' => 2,
            'is_popular' => false,
        ]);
        $subscription = ProductSubscription::query()->create([
            'booking_trx_id' => 'BOOKING-FILAMENT-001',
            'name' => 'Filament Customer',
            'phone' => '+6281234567890',
            'email' => 'filament@example.com',
            'customer_bank_name' => 'Bank BCA',
            'customer_bank_number' => '1234567890',
            'customer_bank_account' => 'Filament Customer',
            'proof' => 'proofs/filament.png',
            'total_amount' => 55_000,
            'duration' => '1 Bulan',
            'total_tax_amount' => 5_000,
            'price' => 50_000,
            'is_paid' => false,
            'product_id' => $product->id,
        ]);
        $group = SubscriptionGroup::query()->create([
            'product_id' => $product->id,
            'product_subscription_id' => $subscription->id,
            'max_capacity' => 2,
            'participant_count' => 0,
        ]);

        Livewire::test(GroupParticipantsRelationManager::class, [
            'ownerRecord' => $group,
            'pageClass' => EditSubscriptionGroup::class,
        ])->callTableAction('create', data: [
            'name' => 'Manual Participant',
            'email' => 'participant@example.com',
            'booking_trx_id' => 'BOOKING-MANUAL-002',
        ])->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('group_participants', [
            'subscription_group_id' => $group->id,
            'name' => 'Manual Participant',
            'email' => 'participant@example.com',
            'booking_trx_id' => 'BOOKING-MANUAL-002',
        ]);
        $this->assertSame(1, $group->refresh()->participant_count);
    }
}
