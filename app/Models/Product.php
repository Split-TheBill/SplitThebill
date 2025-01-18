<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'photo',
        'about',
        'tagline',
        'price',
        'price_per_person',
        'duration',
        'capacity',
        'is_popular',
    ];
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => [
                'name' => $value,
                'slug' => Str::slug($value),
            ],
        );
    }

    public function groups(): HasMany
    {
        return $this->hasMany(SubscriptionGroup::class);
    }

    public function keypoints(): HasMany
    {
        return $this->hasMany(ProductKeyPoint::class);
    }
}
