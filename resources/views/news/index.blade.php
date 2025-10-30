@extends('layouts.app')

@section('title', 'News - Film e Serie TV')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">🎬 Ultime News</h1>
    </div>

    @if($news->count() > 0)
        {{-- ✅ USA: news-grid e news-card --}}
        <div class="news-grid">
            @foreach($news as $item)
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
                            @if($item->release_date)
                                <span>📅 {{ \Carbon\Carbon::parse($item->release_date)->format('d/m/Y') }}</span>
                            @endif
                            <span>🎭 {{ ucfirst($item->type) }}</span>
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
                            @auth
                                <button 
                                    onclick="showAddToListModal({{ $item->id }})" 
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

       {{-- Paginazione --}}
<div style="margin-top: 2rem;">
    {{ $news->links('vendor.pagination.custom') }}
</div>
        <div class="empty-state">
            <h2>🎬 Nessuna News Disponibile</h2>
            <p>Le ultime notizie su film e serie TV verranno caricate a breve.</p>
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
            <input type="hidden" id="quickAddItemId" value="">
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
function showAddToListModal(itemId) {
    document.getElementById('quickAddItemId').value = itemId;
    document.getElementById('quickAddModal').style.display = 'flex';
}

function closeQuickAddModal() {
    document.getElementById('quickAddModal').style.display = 'none';
    document.getElementById('quickAddForm').reset();
}

function quickAddToList() {
    const itemId = document.getElementById('quickAddItemId').value;
    const status = document.getElementById('quick_status').value;
    
    fetch(`/my-lists/${itemId}`, {
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