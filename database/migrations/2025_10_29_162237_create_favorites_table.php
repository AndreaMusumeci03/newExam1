<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('news_id')->constrained('news')->onDelete('cascade');
            $table->timestamps();
            
            // Previene duplicati: un utente può aggiungere una news ai preferiti solo una volta
            $table->unique(['user_id', 'news_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};