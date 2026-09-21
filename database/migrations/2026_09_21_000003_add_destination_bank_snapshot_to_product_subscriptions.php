<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_subscriptions', function (Blueprint $table) {
            $table->string('destination_bank_name')->nullable()->after('customer_bank_account');
            $table->string('destination_bank_account_name')->nullable()->after('destination_bank_name');
            $table->string('destination_bank_account_number')->nullable()->after('destination_bank_account_name');
        });
    }

    public function down(): void
    {
        Schema::table('product_subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'destination_bank_name',
                'destination_bank_account_name',
                'destination_bank_account_number',
            ]);
        });
    }
};
