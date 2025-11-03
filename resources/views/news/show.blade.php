@extends('layouts.app')

@section('title', $news->title)

@section('content')
<div class="container">
    <div class="news-detail">
        {{-- Header della News --}}
        <div class="news-header">
            <h1>{{ $news->title }}</h1>
            <div class="card-meta" style="justify-content: flex-start; gap: 2rem; margin-top: 1rem;">
                <span>📅 {{ $news->published_at->format('d/m/Y') }}</span>
                <span>📰 {{ $news->source }}</span>
            </div>
        </div>

        {{-- Immagine della News --}}
        @if($news->image_url)
            <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="news-image">
        @endif

        {{-- Contenuto della News --}}
        <div class="news-content">
            <p style="white-space: pre-wrap;">{{ $news->content }}</p>
        </div>

        {{-- Pulsanti Azioni (solo per utenti autenticati) --}}
        @auth
            <div class="news-actions">
                {{-- ✅ NUOVA SEZIONE: AGGIUNGI ALLA LISTA --}}
                <div class="add-to-list-section" style="width: 100%; margin-bottom: 1rem;">
                    <h3 style="margin-bottom: 1rem;">📋 Aggiungi alla Tua Lista</h3>
                    <form onsubmit="addToList({{ $news->id }}); return false;" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                        @csrf
                        <div class="form-group" style="flex: 1; min-width: 200px;">
                            <label for="status">Stato</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="plan_to_watch">📋 Da Vedere</option>
                                <option value="watching">▶️ Sto Guardando</option>
                                <option value="completed">✅ Completato</option>
                                <option value="dropped">❌ Abbandonato</option>
                            </select>
                        </div>
                        <div class="form-group" style="width: 150px;">
                            <label for="rating">Voto (1-10)</label>
                            <input type="number" name="rating" id="rating" class="form-control" min="1" max="10" placeholder="Opzionale">
                        </div>
                        <div class="form-group" style="flex: 2; min-width: 300px;">
                            <label for="personal_notes">Note Personali</label>
                            <input type="text" name="personal_notes" id="personal_notes" class="form-control" maxlength="1000" placeholder="Opzionale">
                        </div>
                        <button type="submit" class="btn btn-primary">📋 Aggiungi</button>
                    </form>
                </div>

                {{-- Preferiti --}}
                @if($isFavorite)
                    <button 
                        onclick="removeFromFavorites({{ $news->id }})" 
                        class="btn btn-danger"
                    >
                        💔 Rimuovi dai Preferiti
                    </button>
                @else
                    <button 
                        onclick="addToFavorites({{ $news->id }})" 
                        class="btn btn-success"
                    >
                        ❤️ Aggiungi ai Preferiti
                    </button>
                @endif

                <a href="{{ route('news.index') }}" class="btn btn-secondary">← Torna alle News</a>
            </div>
        @else
            <div class="alert alert-info">
                <a href="{{ route('login') }}" style="color: #fff; text-decoration: underline;">Accedi</a> 
                per aggiungere questa notizia ai preferiti e alle tue liste personalizzate.
            </div>
        @endauth

        {{-- Sezione Commenti --}}
        <div class="comments-section">
            <h3>💬 Commenti ({{ $news->comments->count() }})</h3>

            {{-- Form per Aggiungere Commento (solo utenti autenticati) --}}
            @auth
                <form onsubmit="submitComment({{ $news->id }}); return false;" style="margin-bottom: 2rem;">
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

            {{-- Lista Commenti --}}
            @forelse($news->comments as $comment)
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
                                style="padding: 0.4rem 0.8rem; font-size: 0.9rem;"
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
@endsection

<script src="{{ asset('js/news.js') }}"></script>
    
    @stack('scripts')
