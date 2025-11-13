<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RecommendedFilmController;
use App\Http\Controllers\UserFilmListController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Autenticazione
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Film Consigliati
Route::get('/recommended-films', [RecommendedFilmController::class, 'index'])->name('recommended-films.index');
Route::get('/recommended-films/{id}', [RecommendedFilmController::class, 'show'])->name('recommended-films.show');

// Autenticati
Route::middleware('auth')->group(function () {
    // Commenti per film
    Route::post('/films/{filmId}/comments', [CommentController::class, 'storeForFilm'])->name('films.comments.store');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Preferiti per film
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/film/{filmId}', [FavoriteController::class, 'storeFilm'])->name('favorites.store-film');
    Route::delete('/favorites/film/{filmId}', [FavoriteController::class, 'destroyFilm'])->name('favorites.destroy-film');

    // Liste Personalizzate per film
    Route::get('/my-lists', [UserFilmListController::class, 'index'])->name('my-lists.index');
    Route::get('/my-lists/{status}', [UserFilmListController::class, 'show'])->name('my-lists.show');
    Route::post('/my-lists/film/{filmId}', [UserFilmListController::class, 'storeFilm'])->name('my-lists.store-film');
    Route::delete('/my-lists/film/{filmId}', [UserFilmListController::class, 'destroyFilm'])->name('my-lists.destroy-film');
});

// Route per caricare i dettagli completi dei film in background
Route::get('/films/load-details', [RecommendedFilmController::class, 'loadFullDetails'])
    ->name('films.load-details');