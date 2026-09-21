<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_groups', function (Blueprint $table) {
            $table->dropForeign(['product_subscription_id']);
        });

        Schema::table('subscription_groups', function (Blueprint $table) {
            $table->foreignId('product_subscription_id')->nullable()->change();
            $table->foreign('product_subscription_id')
                ->references('id')
                ->on('product_subscriptions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('subscription_groups', function (Blueprint $table) {
            $table->dropForeign(['product_subscription_id']);
        });

        Schema::table('subscription_groups', function (Blueprint $table) {
            $table->foreignId('product_subscription_id')->nullable(false)->change();
            $table->foreign('product_subscription_id')
                ->references('id')
                ->on('product_subscriptions')
                ->cascadeOnDelete();
        });
    }
};
