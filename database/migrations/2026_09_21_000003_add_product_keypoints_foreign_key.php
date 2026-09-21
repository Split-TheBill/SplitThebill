<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_keypoints') || ! Schema::hasTable('products')) {
            return;
        }

        $foreignKeyExists = collect(Schema::getForeignKeys('product_keypoints'))
            ->contains(function (array $foreignKey): bool {
                return in_array('product_id', $foreignKey['columns'] ?? [], true)
                    && ($foreignKey['foreign_table'] ?? null) === 'products';
            });

        if ($foreignKeyExists) {
            return;
        }

        Schema::table('product_keypoints', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Intentionally preserve the constraint. On an existing installation,
        // it may have been created by the historical migration rather than by
        // this compatibility migration. The original products migration drops
        // the dependent table first during a complete rollback.
    }
};
