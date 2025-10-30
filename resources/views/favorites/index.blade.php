@extends('layouts.app')

@section('title', 'I Miei Preferiti')

@section('content')
<div class="container">
    <h1 style="text-align: center; margin-bottom: 2rem; color: #e50914;">
        ❤️ I Miei Preferiti
    </h1>

    @if($favorites->isEmpty())
        <div class="empty-state">
            <h3>Nessun preferito salvato</h3>
            <p>Inizia ad aggiungere notizie ai tuoi preferiti per trovarle qui!</p>
            <a href="{{ route('news.index') }}" class="btn btn-primary" style="margin-top: 1rem;">
                Esplora le News
            </a>
        </div>
    @else
        <div class="grid">
            @foreach($favorites as $item)
                <div class="card">
                    @if($item->image_url)
                        <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="card-image">
                    @else
                        <div class="card-image" style="display: flex; align-items: center; justify-content: center; background: #2a2a2a;">
                            <span style="font-size: 3rem;">🎬</span>
                        </div>
                    @endif

                    <div class="card-body">
                        <h3 class="card-title">
                            <a href="{{ route('news.show', $item->id) }}">{{ $item->title }}</a>
                        </h3>

                        <p class="card-text">{{ Str::limit($item->description, 150) }}</p>

                        <div class="card-meta">
                            <span>{{ $item->source }}</span>
                            <span>{{ $item->published_at->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('news.show', $item->id) }}" class="btn btn-primary" style="flex: 1;">
                                Leggi
                            </a>
                            <button 
                                onclick="removeFromFavorites({{ $item->id }})" 
                                class="btn btn-danger"
                                style="flex: 1;"
                            >
                                Rimuovi
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginazione --}}
        <div class="pagination">
            @if ($favorites->onFirstPage())
                <span class="disabled">« Precedente</span>
            @else
                <a href="{{ $favorites->previousPageUrl() }}">« Precedente</a>
            @endif

            @foreach ($favorites->getUrlRange(1, $favorites->lastPage()) as $page => $url)
                @if ($page == $favorites->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if ($favorites->hasMorePages())
                <a href="{{ $favorites->nextPageUrl() }}">Successivo »</a>
            @else
                <span class="disabled">Successivo »</span>
            @endif
        </div>
    @endif
</div>
@endsection