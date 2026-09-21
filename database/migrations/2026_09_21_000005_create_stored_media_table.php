<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stored_media', function (Blueprint $table) {
            $table->id();
            $table->string('bucket', 64);
            $table->string('path', 512);
            $table->longText('contents');
            $table->unsignedBigInteger('size');
            $table->string('mime_type', 191);
            $table->string('visibility', 16);
            $table->unsignedBigInteger('last_modified');
            $table->timestamps();

            $table->unique(['bucket', 'path']);
            $table->index(['bucket', 'visibility']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stored_media');
    }
};
