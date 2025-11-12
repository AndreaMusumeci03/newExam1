<div class="news-card" data-item-card>
    @php
        $film = $item->getFilm();
        $imageUrl = $item->getImageUrl();
        $filmId = $item->news_id ?? $item->recommended_film_id;
        $routeName = $item->news_id ? 'news.show' : 'recommended-films.show';
        $type = $item->news_id ? 'news' : 'film';
    @endphp

    {{-- Immagine --}}
    <a href="{{ route($routeName, $filmId) }}" class="card-image-link">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $film->title }}" class="card-image">
        @else
            <div class="card-image-placeholder">🎬</div>
        @endif
    </a>
    
    <div class="card-content">
        {{-- Titolo --}}
        <h3 class="card-title">
            <a href="{{ route($routeName, $filmId) }}">
                {{ $film->title }}
            </a>
        </h3>
        
        {{-- Status e Rating --}}
        <div class="card-meta">
            <span>{{ $item->getStatusEmoji() }} {{ $item->getStatusLabel() }}</span>
            @if($item->rating)
                <span>⭐ {{ $item->rating }}/10</span>
            @endif
        </div>

        {{-- Note Personali --}}
        @if($item->personal_notes)
            <p class="card-description" style="font-style: italic; color: #999; font-size: 0.9rem;">
                "{{ Str::limit($item->personal_notes, 80) }}"
            </p>
        @endif

        {{-- Azioni --}}
        <div class="card-actions">
            <a href="{{ route($routeName, $filmId) }}" class="btn btn-primary btn-sm">
                📖 Dettagli
            </a>
            <button 
                data-remove-id="{{ $filmId }}"
                data-remove-type="{{ $type }}"
                data-remove-url-base="/my-lists"
                onclick="removeFromList({{ $filmId }}, '{{ $type }}', this)" 
                class="btn btn-danger btn-sm"
            >
                🗑️ Rimuovi
            </button>
        </div>
    </div>
</div>