<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unique('slug', 'products_slug_unique');
        });

        Schema::table('product_subscriptions', function (Blueprint $table) {
            $table->uuid('checkout_token')->nullable()->after('booking_trx_id');
            $table->unique('booking_trx_id', 'product_subscriptions_booking_trx_id_unique');
            $table->unique('checkout_token', 'product_subscriptions_checkout_token_unique');
        });

        Schema::table('group_participants', function (Blueprint $table) {
            $table->unique('booking_trx_id', 'group_participants_booking_trx_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('group_participants', function (Blueprint $table) {
            $table->dropUnique('group_participants_booking_trx_id_unique');
        });

        Schema::table('product_subscriptions', function (Blueprint $table) {
            $table->dropUnique('product_subscriptions_booking_trx_id_unique');
            $table->dropUnique('product_subscriptions_checkout_token_unique');
            $table->dropColumn('checkout_token');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_slug_unique');
        });
    }
};
