<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_keypoints', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // This historical migration sorts before create_products_table.php.
            // Add the foreign key in a later compatibility migration, once the
            // products table is guaranteed to exist on every database driver.
            $table->foreignId('product_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_keypoints');
    }
};
