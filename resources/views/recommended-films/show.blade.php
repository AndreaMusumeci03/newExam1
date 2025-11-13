@extends('layouts.app')

@section('title', $film->title)

@section('content')
<div class="container">
    <div class="film-detail">
        <div class="film-header">
            <h1>{{ $film->title }} @if($film->year)<span style="color: #999;">({{ $film->year }})</span>@endif</h1>
            
            <div class="film-meta-main">
                @if($film->imdb_rating)
                    <div class="rating-badge">
                        <span style="font-size: 2rem;">⭐</span>
                        <div>
                            <strong style="font-size: 1.5rem;">{{ $film->imdb_rating }}</strong>
                            <small style="display: block; color: #999;">/10 TMDb</small>
                        </div>
                    </div>
                @endif

                <div class="film-info">
                    @if($film->rated && !in_array($film->rated, ['T','N/A'])) <span class="info-badge">{{ $film->rated }}</span> 
                    @endif
                    @if($film->runtime)
                        <span class="info-badge">⏱️ {{ $film->runtime }}</span>
                    @endif
                    @if($film->genre)
                        <span class="info-badge">🎭 {{ $film->genre }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="film-content-wrapper">
            <div class="film-poster-column">
                @if($film->poster_url)
                    <img src="{{ $film->poster_url }}" alt="{{ $film->title }}" class="film-poster">
                @else
                    <div class="film-poster-placeholder">🎬</div>
                @endif

                @auth
                    <div class="quick-actions" style="margin-top: 1rem;">
                        <div class="add-to-list-section" style="width: 100%; margin-bottom: 1rem;">
                            <h3 style="margin-bottom: 1rem;">📋 Aggiungi alla Tua Lista</h3>
                            <form id="add-to-list-form" onsubmit="return addToFilmList({{ $film->id }}, this)" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                                @csrf
                                <div class="form-group" style="flex: 1; min-width: 200px;">
                                    <label for="status">Stato</label>
                                    <select name="status" class="form-control" required>
                                        <option value="plan_to_watch">📋 Da Vedere</option>
                                        <option value="watching">▶️ Sto Guardando</option>
                                        <option value="completed">✅ Completato</option>
                                        <option value="dropped">❌ Abbandonato</option>
                                    </select>
                                </div>
                                <div class="form-group" style="width: 150px;">
                                    <label for="rating">Voto (1-10)</label>
                                    <input type="number" name="rating" class="form-control" min="1" max="10" placeholder="Opzionale">
                                </div>
                                <div class="form-group" style="flex: 2; min-width: 300px;">
                                    <label for="personal_notes">Note Personali</label>
                                    <input type="text" name="personal_notes" class="form-control" maxlength="1000" placeholder="Opzionale">
                                </div>
                                <button type="submit" class="btn btn-primary">📋 Aggiungi</button>
                            </form>
                        </div>

                        @if(!empty($isFavoriteFilm))
                            <button 
                                onclick="removeFromFavorites({{ $film->id }})" 
                                class="btn btn-danger btn-block"
                            >
                                💔 Rimuovi dai Preferiti
                            </button>
                        @else
                            <button 
                                onclick="addToFavorites({{ $film->id }})" 
                                class="btn btn-success btn-block"
                            >
                                ❤️ Aggiungi ai Preferiti
                            </button>
                        @endif
                    </div>
                @else
                    <div class="alert alert-info" style="margin-top: 1rem;">
                        <a href="{{ route('login') }}" style="color: #fff; text-decoration: underline;">Accedi</a> 
                        per aggiungere questo film alla tua lista e ai preferiti!
                    </div>
                @endauth
            </div>

            <div class="film-info-column">
                @if($film->plot)
                    <div class="film-section">
                        <h3>📖 Trama</h3>
                        <p style="line-height: 1.8; color: #ddd;">{{ $film->plot }}</p>
                    </div>
                @endif

                <div class="film-section">
                    <h3>ℹ️ Dettagli</h3>
                    <div class="film-details-grid">
                        @if($film->director)
                            <div class="detail-item">
                                <strong>🎬 Regista:</strong>
                                <span>{{ $film->director }}</span>
                            </div>
                        @endif

                        @if($film->actors)
                            <div class="detail-item">
                                <strong>🎭 Attori:</strong>
                                <span>{{ $film->actors }}</span>
                            </div>
                        @endif

                        @if($film->released)
                            <div class="detail-item">
                                <strong>📅 Data Uscita:</strong>
                                <span>{{ \Carbon\Carbon::parse($film->released)->format('d/m/Y') }}</span>
                            </div>
                        @endif

                        <div class="detail-item">
                            <strong>🔗 Fonte:</strong>
                            <span>TMDb (The Movie Database)</span>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 2rem;">
                    <a href="{{ route('recommended-films.index') }}" class="btn btn-secondary">
                        ← Torna ai Film Consigliati
                    </a>
                </div>

                <div class="comments-section" style="margin-top: 2rem;">
                    <h3>💬 Commenti ({{ $film->comments->count() }})</h3>

                    @auth
                        <form onsubmit="return submitFilmComment({{ $film->id }}, this)" style="margin-bottom: 2rem;">
                            @csrf
                            <div class="form-group">
                                <textarea 
                                    name="content" 
                                    class="form-control" 
                                    placeholder="Scrivi il tuo commento..." 
                                    required
                                    maxlength="1000"
                                ></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Invia Commento</button>
                        </form>
                    @else
                        <div class="alert alert-info" style="margin-bottom: 2rem;">
                            <a href="{{ route('login') }}" style="color: #fff; text-decoration: underline;">Accedi</a> 
                            per lasciare un commento.
                        </div>
                    @endauth

                    @forelse($film->comments as $comment)
                        <div class="comment">
                            <div class="comment-header">
                                <span class="comment-author">{{ $comment->user->name }}</span>
                                <span class="comment-date">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="comment-body">
                                {{ $comment->content }}
                            </div>
                            @if(auth()->check() && auth()->id() === $comment->user_id)
                                <div class="comment-actions">
                                    <button 
                                        onclick="deleteComment({{ $comment->id }})" 
                                        class="btn btn-danger"
                                        style="padding: 0.5rem 1rem; font-size: 0.9rem;"
                                    >
                                        🗑️ Elimina
                                    </button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="empty-state" style="padding: 2rem;">
                            <p>Nessun commento ancora. Sii il primo a commentare!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@auth
<script src="{{ asset('js/film.js') }}"></script>
@endauth
@endsection