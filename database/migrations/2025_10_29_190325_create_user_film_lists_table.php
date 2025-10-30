<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_film_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('news_id')->constrained('news')->onDelete('cascade');
            $table->enum('status', ['plan_to_watch', 'watching', 'completed', 'dropped'])->default('plan_to_watch');
            $table->integer('rating')->nullable()->comment('Voto da 1 a 10');
            $table->text('personal_notes')->nullable();
            $table->timestamps();
            
            // Un utente può avere un film solo una volta nella sua lista
            $table->unique(['user_id', 'news_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_film_lists');
    }
};