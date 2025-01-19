<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDurationColumnInProductSubscription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_subscriptions', function (Blueprint $table) {
            $table->string('duration')->change(); // Ubah tipe data menjadi string
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('duration')->change(); // Kembalikan ke tipe sebelumnya
        });
    }
}
