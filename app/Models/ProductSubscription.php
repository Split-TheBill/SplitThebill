<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'proof',
        'booking_trx_id',
        'checkout_token',
        'total_amount',
        'total_tax_amount',
        'customer_bank_name',
        'customer_bank_number',
        'customer_bank_account',
        'destination_bank_name',
        'destination_bank_account_name',
        'destination_bank_account_number',
        'is_paid',
        'duration',
        'price',
        'product_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'total_amount' => 'integer',
            'total_tax_amount' => 'integer',
            'is_paid' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function group(): HasOne
    {
        return $this->hasOne(SubscriptionGroup::class, 'product_subscription_id');
    }
}
