<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable()->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('image_url')->nullable();
            $table->string('source')->nullable();
            $table->dateTime('published_at')->nullable(); // ✅ CAMBIATO da timestamp() a dateTime()
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};