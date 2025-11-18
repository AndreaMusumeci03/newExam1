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

    <div class="stat-grid">
        <div class="stat-card" style="margin-left: 2.3vw;">
            <div class="stat-left">
                <div class="stat-number">{{ $stats['plan_to_watch'] }}</div>
                <div class="stat-label">📋 Da Vedere</div>
            </div>
            @if($stats['plan_to_watch'] > 0)
                <a href="{{ route('my-lists.show', 'plan_to_watch') }}" class="btn btn-sm btn-primary stat-btn">
                    Vedi Lista
                </a>
            @endif
        </div>

        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-number">{{ $stats['watching'] }}</div>
                <div class="stat-label">▶️ Sto Guardando</div>
            </div>
            @if($stats['watching'] > 0)
                <a href="{{ route('my-lists.show', 'watching') }}" class="btn btn-sm btn-primary stat-btn">
                    Vedi Lista
                </a>
            @endif
        </div>

        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-number">{{ $stats['completed'] }}</div>
                <div class="stat-label">✅ Completati</div>
            </div>
            @if($stats['completed'] > 0)
                <a href="{{ route('my-lists.show', 'completed') }}" class="btn btn-sm btn-primary stat-btn">
                    Vedi Lista
                </a>
            @endif
        </div>

        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-number">{{ $stats['dropped'] }}</div>
                <div class="stat-label">❌ Abbandonati</div>
            </div>
            @if($stats['dropped'] > 0)
                <a href="{{ route('my-lists.show', 'dropped') }}" class="btn btn-sm btn-primary stat-btn">
                    Vedi Lista
                </a>
            @endif
        </div>
    </div>

    {{-- Lista Completa --}}
    @if($allItems->count() > 0)
        <h2 style="color: #e50914; margin: 2rem 0 1.5rem;">📚 Tutti i Film e Serie</h2>
        <div class="news-grid">
            @foreach($allItems as $item)
                @include('my-lists.partials.list-card', ['item' => $item])
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <h2>📋 Nessun Film nella Lista</h2>
            <p>Inizia ad aggiungere film e serie TV alle tue liste!</p>
            <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                
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