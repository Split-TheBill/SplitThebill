<?php

namespace App\Observers;

use App\Models\GroupParticipant;
use App\Models\Product;
use App\Models\ProductSubscription;
use App\Models\SubscriptionGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ProductSubscriptionObserver
{
    public function creating(ProductSubscription $subscription): void
    {
        $subscription->booking_trx_id ??= $this->generateUniqueBookingTrxId();
    }

    public function created(ProductSubscription $productSubscription): void
    {
        if ($productSubscription->is_paid) {
            $this->addParticipantToGroup($productSubscription);
        }
    }

    public function updated(ProductSubscription $productSubscription): void
    {
        if (! $productSubscription->wasChanged('is_paid')) {
            return;
        }

        if ($productSubscription->is_paid) {
            $this->addParticipantToGroup($productSubscription);

            return;
        }

        $this->removeParticipantFromGroup($productSubscription);
    }

    public function deleted(ProductSubscription $productSubscription): void
    {
        $this->removeParticipantFromGroup($productSubscription);
    }

    public function restored(ProductSubscription $productSubscription): void
    {
        if ($productSubscription->is_paid) {
            $this->addParticipantToGroup($productSubscription);
        }
    }

    public function forceDeleted(ProductSubscription $productSubscription): void
    {
        $this->removeParticipantFromGroup($productSubscription, force: true);
    }

    private function addParticipantToGroup(ProductSubscription $productSubscription): void
    {
        DB::transaction(function () use ($productSubscription): void {
            $product = Product::query()
                ->whereKey($productSubscription->product_id)
                ->lockForUpdate()
                ->first();

            if (! $product) {
                throw new RuntimeException('Cannot assign a subscription for a missing product.');
            }

            if ((int) $product->capacity < 1) {
                throw new RuntimeException('Cannot assign a subscription to a product with no capacity.');
            }

            $existingParticipant = GroupParticipant::withTrashed()
                ->where('booking_trx_id', $productSubscription->booking_trx_id)
                ->lockForUpdate()
                ->first();

            if ($existingParticipant) {
                $group = SubscriptionGroup::find($existingParticipant->subscription_group_id);

                if ($group) {
                    if ($existingParticipant->trashed()) {
                        $existingParticipant->restore();
                    }

                    $this->synchronizeParticipantCount($group);

                    return;
                }

                $existingParticipant->forceDelete();
            }

            $currentGroup = SubscriptionGroup::query()
                ->where('product_id', $productSubscription->product_id)
                ->whereColumn('participant_count', '<', 'max_capacity')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (! $currentGroup) {
                $currentGroup = SubscriptionGroup::create([
                    'product_id' => $productSubscription->product_id,
                    'product_subscription_id' => $productSubscription->id,
                    'max_capacity' => $product->capacity,
                    'participant_count' => 0,
                ]);
            }

            GroupParticipant::firstOrCreate(
                ['booking_trx_id' => $productSubscription->booking_trx_id],
                [
                    'name' => $productSubscription->name,
                    'email' => $productSubscription->email,
                    'subscription_group_id' => $currentGroup->id,
                ],
            );

            $this->synchronizeParticipantCount($currentGroup);
        });
    }

    private function removeParticipantFromGroup(
        ProductSubscription $productSubscription,
        bool $force = false,
    ): void {
        DB::transaction(function () use ($productSubscription, $force): void {
            $participant = GroupParticipant::withTrashed()
                ->where('booking_trx_id', $productSubscription->booking_trx_id)
                ->lockForUpdate()
                ->first();

            if (! $participant) {
                return;
            }

            $group = SubscriptionGroup::find($participant->subscription_group_id);

            if ($force) {
                $participant->forceDelete();
            } elseif (! $participant->trashed()) {
                $participant->delete();
            }

            if ($group) {
                $this->synchronizeParticipantCount($group);
            }
        });
    }

    private function synchronizeParticipantCount(SubscriptionGroup $group): void
    {
        $group->update([
            'participant_count' => $group->groupParticipants()->count(),
        ]);
    }

    private function generateUniqueBookingTrxId(): string
    {
        do {
            $bookingCode = 'STB-'.Str::upper((string) Str::ulid());
        } while (ProductSubscription::withTrashed()->where('booking_trx_id', $bookingCode)->exists());

        return $bookingCode;
    }
}
