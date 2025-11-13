@extends('layouts.app')

@section('title', 'I Miei Preferiti')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">❤️ I Miei Preferiti</h1>
    </div>

    @if($favorites->count() === 0)
        <div class="empty-state">
            <h3>Nessun preferito salvato</h3>
            <p>Inizia ad aggiungere film ai tuoi preferiti per trovarli qui!</p>
            <a href="{{ route('recommended-films.index') }}" class="btn btn-primary" style="margin-top: 1rem;">
                Esplora i Film
            </a>
        </div>
    @else
        <div class="news-grid">
            @foreach($favorites as $film)
                <div class="news-card">
                    <a href="{{ route('recommended-films.show', $film->id) }}" class="card-image-link">
                        @if($film->poster_url)
                            <img src="{{ $film->poster_url }}" alt="{{ $film->title }}" class="card-image">
                        @else
                            <div class="card-image-placeholder">🎬</div>
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
                            @if($film->imdb_rating)
                                <span>⭐ {{ $film->imdb_rating }}</span>
                            @endif
                        </div>

                        @if($film->plot)
                            <p class="card-description">
                                {{ Str::limit($film->plot, 120) }}
                            </p>
                        @endif

                        <div class="card-actions">
                            <a href="{{ route('recommended-films.show', $film->id) }}" class="btn btn-primary btn-sm">
                                📖 Dettagli
                            </a>
                            <button 
                                onclick="removeFromFavorites({{ $film->id }})" 
                                class="btn btn-danger btn-sm"
                            >
                                🗑️ Rimuovi
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 2rem;">
            {{ $favorites->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>

@push('scripts')
<script src="{{ asset('js/film.js') }}"></script>
@endpush
@endsection