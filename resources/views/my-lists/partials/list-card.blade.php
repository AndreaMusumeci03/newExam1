<div class="news-card">
    @php
        $film = $item->recommendedFilm;
        $imageUrl = $film?->poster_url;
        $filmId = $item->recommended_film_id;
        $routeName = 'recommended-films.show';
        $type = 'film';
    @endphp

    <a href="{{ route($routeName, $filmId) }}" class="card-image-link">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $film?->title }}" class="card-image">
        @else
            <div class="card-image-placeholder">🎬</div>
        @endif
    </a>
    
    <div class="card-content">
        <h3 class="card-title">
            <a href="{{ route($routeName, $filmId) }}">
                {{ $film?->title ?? 'Sconosciuto' }}
            </a>
        </h3>
        
        <div class="card-meta">
            <span>{{ $item->getStatusEmoji() }} {{ $item->getStatusLabel() }}</span>
            @if($item->rating)
                <span>⭐ {{ $item->rating }}/10</span>
            @endif
        </div>

        @if($item->personal_notes)
            <p class="card-description" style="font-style: italic; color: #999; font-size: 0.9rem;">
                "{{ Str::limit($item->personal_notes, 80) }}"
            </p>
        @endif

        <div class="card-actions">
            <a href="{{ route($routeName, $filmId) }}" class="btn btn-primary btn-sm">
                📖 Dettagli
            </a>
            <button 
               
                onclick="removeFromList({{ $filmId }}, this)" 
                class="btn btn-danger btn-sm"
            >
                🗑️ Rimuovi
            </button>
        </div>
    </div>
</div>