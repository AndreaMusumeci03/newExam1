@extends('layouts.app')

@section('title', $film->title)

@section('content')
<div class="container">
    <div class="film-detail">
        {{-- Header del Film --}}
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

        {{-- Layout a 2 Colonne --}}
        <div class="film-content-wrapper">
            {{-- Colonna Sinistra: Poster --}}
            <div class="film-poster-column">
                @if($film->poster_url)
                    <img src="{{ $film->poster_url }}" alt="{{ $film->title }}" class="film-poster">
                @else
                    <div class="film-poster-placeholder">🎬</div>
                @endif

                {{-- Pulsante Aggiungi alla Lista (solo per utenti autenticati) --}}
                @auth
                    <div class="quick-actions" style="margin-top: 1rem;">
                        @if($userFilmList)
                            <div class="alert alert-info" style="text-align: center;">
                                {{ $userFilmList->getStatusEmoji() }} 
                                <strong>{{ $userFilmList->getStatusLabel() }}</strong>
                                @if($userFilmList->rating)
                                    <br><span>⭐ Il tuo voto: {{ $userFilmList->rating }}/10</span>
                                @endif
                            </div>
                            <button 
                                onclick="removeFromFilmList({{ $film->id }})" 
                                class="btn btn-danger btn-block"
                            >
                                🗑️ Rimuovi dalla Lista
                            </button>
                        @else
                            <button 
                                onclick="showAddToListModal({{ $film->id }}, @json($film->title), 'film')" 
                                class="btn btn-primary btn-block"
                            >
                                📋 Aggiungi alla Lista
                            </button>
                        @endif
                    </div>
                @else
                    <div class="alert alert-info" style="margin-top: 1rem;">
                        <a href="{{ route('login') }}" style="color: #fff; text-decoration: underline;">Accedi</a> 
                        per aggiungere questo film alla tua lista!
                    </div>
                @endauth
            </div>

            {{-- Colonna Destra: Informazioni --}}
            <div class="film-info-column">
                {{-- Trama --}}
                @if($film->plot)
                    <div class="film-section">
                        <h3>📖 Trama</h3>
                        <p style="line-height: 1.8; color: #ddd;">{{ $film->plot }}</p>
                    </div>
                @endif

                {{-- Dettagli --}}
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

                {{-- Pulsante Torna Indietro --}}
                <div style="margin-top: 2rem;">
                    <a href="{{ route('recommended-films.index') }}" class="btn btn-secondary">
                        ← Torna ai Film Consigliati
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal per Aggiungere alla Lista --}}
@auth
@include('components.modal')
<script src="{{ asset('js/recommended-film-modal.js') }}"></script>
@endauth
@endsection