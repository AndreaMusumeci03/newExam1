@extends('layouts.app')

@section('title', 'Film Consigliati')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">🎥 Film Consigliati</h1>
            <p style="color: #999; margin-top: 0.5rem;">
                I migliori film classici selezionati per te da OMDb • {{ $films->total() }} film disponibili
            </p>
        </div>
    </div>

    @if($films->count() > 0)
        {{-- Grid di Card --}}
        <div class="news-grid">
            @foreach($films as $film)
                <div class="film-card">
                    {{-- Poster --}}
                    <a href="{{ route('recommended-films.show', $film->id) }}" class="card-image-link">
                        @if($film->poster_url)
                            <img src="{{ $film->poster_url }}" alt="{{ $film->title }}" class="card-image">
                        @else
                            <div class="card-image-placeholder">🎬</div>
                        @endif
                        
                        {{-- Badge Rating --}}
                        @if($film->imdb_rating)
                            <div class="rating-badge-overlay">
                                ⭐ {{ $film->imdb_rating }}
                            </div>
                        @endif
                    </a>
                    
                    <div class="card-content">
                        {{-- Titolo --}}
                        <h3 class="card-title">
                            <a href="{{ route('recommended-films.show', $film->id) }}">
                                {{ $film->title }}
                            </a>
                        </h3>
                        
                        {{-- Meta Info --}}
                        <div class="card-meta">
                            @if($film->year)
                                <span>📅 {{ $film->year }}</span>
                            @endif
                            @if($film->runtime)
                                <span>⏱️ {{ $film->runtime }}</span>
                            @endif
                        </div>

                        {{-- Genere --}}
                        @if($film->genre)
                            <div class="film-genres">
                                @foreach(explode(',', $film->genre) as $genre)
                                    <span class="genre-tag">{{ trim($genre) }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Trama breve --}}
                        @if($film->plot)
                            <p class="card-description">
                                {{ Str::limit($film->plot, 100) }}
                            </p>
                        @endif

                        {{-- Azioni --}}
                        <div class="card-actions">
                            <a href="{{ route('recommended-films.show', $film->id) }}" class="btn btn-primary btn-sm">
                                📖 Dettagli
                            </a>
                            
                            @auth
                                <button 
                                    onclick="showAddToListModal({{ $film->id }})" 
                                    class="btn btn-secondary btn-sm"
                                >
                                    📋 Lista
                                </button>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ✅ PAGINAZIONE CORRETTA CON $films --}}
        <div style="margin-top: 2rem;">
            {{ $films->links('vendor.pagination.custom') }}
        </div>
        
    @else
        <div class="empty-state">
            <h2>🎬 Caricamento Film...</h2>
            <p>I film consigliati verranno caricati automaticamente.</p>
            <p style="color: #666; margin-top: 1rem;">
                Ricarica la pagina tra qualche secondo...
            </p>
        </div>
    @endif
</div>

{{-- Modal Veloce per Aggiungere alla Lista --}}
@auth
<div id="quickAddModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>📋 Aggiungi alla Lista</h3>
            <button onclick="closeQuickAddModal()" class="modal-close">&times;</button>
        </div>
        <form id="quickAddForm" onsubmit="quickAddToList(); return false;">
            @csrf
            <input type="hidden" id="quickAddFilmId" value="">
            <div class="form-group">
                <label for="quick_status">Stato</label>
                <select name="status" id="quick_status" class="form-control" required>
                    <option value="plan_to_watch">📋 Da Vedere</option>
                    <option value="watching">▶️ Sto Guardando</option>
                    <option value="completed">✅ Completato</option>
                    <option value="dropped">❌ Abbandonato</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">Aggiungi</button>
                <button type="button" onclick="closeQuickAddModal()" class="btn btn-secondary">Annulla</button>
            </div>
        </form>
    </div>
</div>

<script>
function showAddToListModal(filmId) {
    document.getElementById('quickAddFilmId').value = filmId;
    document.getElementById('quickAddModal').style.display = 'flex';
}

function closeQuickAddModal() {
    document.getElementById('quickAddModal').style.display = 'none';
    document.getElementById('quickAddForm').reset();
}

function quickAddToList() {
    const filmId = document.getElementById('quickAddFilmId').value;
    const status = document.getElementById('quick_status').value;
    
    fetch(`/my-lists/film/${filmId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            closeQuickAddModal();
        } else {
            showAlert('error', data.message || 'Errore');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Errore di connessione');
    });
}

window.onclick = function(event) {
    const modal = document.getElementById('quickAddModal');
    if (event.target === modal) {
        closeQuickAddModal();
    }
}
</script>
@endauth
@endsection