<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductKeypoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_keypoints_foreign_key_is_added_after_fresh_migrations(): void
    {
        $foreignKeyExists = collect(Schema::getForeignKeys('product_keypoints'))
            ->contains(function (array $foreignKey): bool {
                return in_array('product_id', $foreignKey['columns'] ?? [], true)
                    && ($foreignKey['foreign_table'] ?? null) === 'products';
            });

        $this->assertTrue($foreignKeyExists);

        $product = Product::query()->create([
            'name' => 'Schema Test Product',
            'thumbnail' => 'products/schema-thumbnail.png',
            'photo' => 'products/schema-logo.png',
            'about' => 'Verifies the product keypoint constraint.',
            'tagline' => 'Schema integrity.',
            'price' => 100_000,
            'price_per_person' => 50_000,
            'duration' => '1 Bulan',
            'capacity' => 2,
            'is_popular' => false,
        ]);
        $keypoint = ProductKeypoint::query()->create([
            'name' => 'Premium access',
            'product_id' => $product->id,
        ]);

        $product->forceDelete();

        $this->assertDatabaseMissing('product_keypoints', ['id' => $keypoint->id]);
    }
}
