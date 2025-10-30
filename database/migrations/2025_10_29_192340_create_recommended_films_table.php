<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommended_films', function (Blueprint $table) {
            $table->id();
            $table->string('imdb_id')->unique();
            $table->string('title');
            $table->integer('year')->nullable();
            $table->text('plot')->nullable();
            $table->string('director')->nullable();
            $table->text('actors')->nullable();
            $table->string('genre')->nullable();
            $table->string('poster_url')->nullable();
            $table->decimal('imdb_rating', 3, 1)->nullable();
            $table->string('runtime')->nullable();
            $table->string('rated')->nullable();
            $table->date('released')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommended_films');
    }
};