@extends('layouts.app')

@section('title', 'Film Consigliati')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">🎥 Film Consigliati</h1>
            <p style="color: #999; margin-top: 0.5rem;">
                I migliori film selezionati per te da TMDb • {{ $films->total() }} film disponibili • Aggiornati quotidianamente
            </p>
        </div>
    </div>

    @if($films->count() > 0)
        <div class="news-grid">
            @foreach($films as $film)
                <div class="film-card" data-item-card>
                    <a href="{{ route('recommended-films.show', $film->id) }}" class="card-image-link">
                        @if($film->poster_url)
                            <img src="{{ $film->poster_url }}" alt="{{ $film->title }}" class="card-image">
                        @else
                            <div class="card-image-placeholder">🎬</div>
                        @endif
                        @if($film->imdb_rating)
                            <div class="rating-badge-overlay">
                                ⭐ {{ $film->imdb_rating }}
                            </div>
                        @endif
                    </a>
                    <div class="card-content">
                        <h3 class="card-title">
                            <a href="{{ route('recommended-films.show', $film->id) }}">
                                {{ $film->title }}
                            </a>
                        </h3>
                        <div class="card-meta">
                            @if($film->year)
                                <span>📅 {{ $film->year }}</span>
                            @endif
                            @if($film->runtime)
                                <span>⏱️ {{ $film->runtime }}</span>
                            @endif
                        </div>
                        @if($film->genre)
                            <div class="film-genres">
                                @foreach(explode(',', $film->genre) as $genre)
                                    <span class="genre-tag">{{ trim($genre) }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if($film->plot)
                            <p class="card-description">
                                {{ Str::limit($film->plot, 100) }}
                            </p>
                        @endif
                        <div class="card-actions">
                            <a href="{{ route('recommended-films.show', $film->id) }}" class="btn btn-primary btn-sm">
                                📖 Dettagli
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 2rem;">
            {{ $films->links('vendor.pagination.custom') }}
        </div>
        
    @else
        <div class="empty-state">
            <h2>🎬 Caricamento Film...</h2>
            <p>I film consigliati verranno caricati automaticamente da TMDb.</p>
            <p style="color: #666; margin-top: 1rem;">
                Ricarica la pagina tra qualche secondo...
            </p>
        </div>
    @endif
</div>
@endsection