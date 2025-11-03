@extends('layouts.app')

@section('title', 'Le Mie Liste')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">📋 Le Mie Liste</h1>
        <p style="color: #999; margin-top: 0.5rem;">
            Organizza i tuoi film e serie TV preferiti
        </p>
    </div>

    {{-- Statistiche --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $stats['plan_to_watch'] }}</div>
            <div class="stat-label">📋 Da Vedere</div>
            @if($stats['plan_to_watch'] > 0)
                <a href="{{ route('my-lists.show', 'plan_to_watch') }}" class="btn btn-sm btn-primary" style="margin-top: 1rem;">
                    Vedi Lista
                </a>
            @endif
        </div>

        <div class="stat-card">
            <div class="stat-number">{{ $stats['watching'] }}</div>
            <div class="stat-label">▶️ Sto Guardando</div>
            @if($stats['watching'] > 0)
                <a href="{{ route('my-lists.show', 'watching') }}" class="btn btn-sm btn-primary" style="margin-top: 1rem;">
                    Vedi Lista
                </a>
            @endif
        </div>

        <div class="stat-card">
            <div class="stat-number">{{ $stats['completed'] }}</div>
            <div class="stat-label">✅ Completati</div>
            @if($stats['completed'] > 0)
                <a href="{{ route('my-lists.show', 'completed') }}" class="btn btn-sm btn-primary" style="margin-top: 1rem;">
                    Vedi Lista
                </a>
            @endif
        </div>

        <div class="stat-card">
            <div class="stat-number">{{ $stats['dropped'] }}</div>
            <div class="stat-label">❌ Abbandonati</div>
            @if($stats['dropped'] > 0)
                <a href="{{ route('my-lists.show', 'dropped') }}" class="btn btn-sm btn-primary" style="margin-top: 1rem;">
                    Vedi Lista
                </a>
            @endif
        </div>
    </div>

    {{-- Lista Completa --}}
    @if($allItems->count() > 0)
        <h2 style="color: #e50914; margin-bottom: 1.5rem;">📚 Tutti i Film e Serie</h2>
        <div class="news-grid">
            @foreach($allItems as $item)
                @include('my-lists.partials.list-card', ['item' => $item])
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <h2>📋 Nessun Film nella Lista</h2>
            <p>Inizia ad aggiungere film e serie TV alle tue liste!</p>
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

<script src="{{ asset('js/mylist.js') }}"></script>
    
    @stack('scripts')
@endsection