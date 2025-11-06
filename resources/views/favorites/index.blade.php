@extends('layouts.app')

@section('title', 'I Miei Preferiti')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">❤️ I Miei Preferiti</h1>
    </div>

    @if($favorites->isEmpty())
        <div class="empty-state">
            <h3>Nessun preferito salvato</h3>
            <p>Inizia ad aggiungere notizie ai tuoi preferiti per trovarle qui!</p>
            <a href="{{ route('news.index') }}" class="btn btn-primary" style="margin-top: 1rem;">
                Esplora le News
            </a>
        </div>
    @else
        <div class="news-grid">
            @foreach($favorites as $item)
                <div class="news-card">
                    {{-- Immagine --}}
                    <a href="{{ route('news.show', $item->id) }}" class="card-image-link">
                        @if($item->image_url)
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="card-image">
                        @else
                            <div class="card-image-placeholder">🎬</div>
                        @endif
                    </a>

                    <div class="card-content">
                        {{-- Titolo --}}
                        <h3 class="card-title">
                            <a href="{{ route('news.show', $item->id) }}">
                                {{ $item->title }}
                            </a>
                        </h3>

                        {{-- Meta Info --}}
                        <div class="card-meta">
                            <span>📰 {{ $item->source }}</span>
                            @if($item->published_at)
                                <span>📅 {{ $item->published_at->format('d/m/Y') }}</span>
                            @endif
                        </div>

                        {{-- Descrizione --}}
                        @if($item->description)
                            <p class="card-description">
                                {{ Str::limit($item->description, 120) }}
                            </p>
                        @endif

                        {{-- Azioni --}}
                        <div class="card-actions">
                            <a href="{{ route('news.show', $item->id) }}" class="btn btn-primary btn-sm">
                                📖 Leggi di più
                            </a>
                            <button 
                                onclick="removeFromFavorites({{ $item->id }})" 
                                class="btn btn-danger btn-sm"
                            >
                                🗑️ Rimuovi
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginazione (uguale a News) --}}
        <div style="margin-top: 2rem;">
            {{ $favorites->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>
@endsection

@push('scripts')

<script src="{{ asset('js/news.js') }}"></script>
@endpush