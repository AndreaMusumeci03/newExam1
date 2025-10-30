@extends('layouts.app')

@section('title', $statusLabel)

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <a href="{{ route('my-lists.index') }}" style="color: #999; text-decoration: none; display: inline-block; margin-bottom: 0.5rem;">
                ← Torna alle Liste
            </a>
            <h1 class="page-title">{{ $statusLabel }}</h1>
            <p style="color: #999; margin-top: 0.5rem;">
                {{ $items->total() }} elementi in questa lista
            </p>
        </div>
    </div>

    @if($items->count() > 0)
        <div class="news-grid">
            @foreach($items as $item)
                @include('my-lists.partials.list-card', ['item' => $item])
            @endforeach
        </div>

        {{-- Paginazione --}}
        <div style="margin-top: 2rem;">
            {{ $items->links() }}
        </div>
    @else
        <div class="empty-state">
            <h2>📋 Nessun Elemento in Questa Lista</h2>
            <p>Aggiungi film e serie TV a "{{ $statusLabel }}"</p>
            <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: center;">
                <a href="{{ route('news.index') }}" class="btn btn-primary">
                    🎬 Esplora Film e Serie
                </a>
                <a href="{{ route('recommended-films.index') }}" class="btn btn-secondary">
                    ⭐ Film Consigliati
                </a>
            </div>
        </div>
    @endif
</div>

{{-- Script per Rimuovere dalla Lista --}}
<script>
function removeFromList(id, type) {
    if (!confirm('Sei sicuro di voler rimuovere questo elemento dalla tua lista?')) {
        return;
    }

    const url = type === 'news' 
        ? `/my-lists/${id}` 
        : `/my-lists/film/${id}`;

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAlert('error', data.message || 'Errore durante la rimozione');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Errore di connessione');
    });
}
</script>
@endsection