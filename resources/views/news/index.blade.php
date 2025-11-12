@extends('layouts.app')

@section('title', 'News - Film e Serie TV')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">🎬 Ultime News</h1>
    </div>

    @if($news->count() > 0)
        <div class="news-grid">
            @foreach($news as $item)
                <div class="news-card" data-item-card>
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
                                    onclick="showAddToListModal({{ $item->id }}, @json($item->title), 'news')" 
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
    @else
        <div class="empty-state">
            <h2>🎬 Nessuna News Disponibile</h2>
            <p>Le ultime notizie su film e serie TV verranno caricate a breve.</p>
        </div>
    @endif
</div>

@auth
    @include('components.modal')
    <script src="{{ asset('js/news.js') }}"></script>
    <script src="{{ asset('js/recommended-film-modal.js') }}"></script>
    @stack('scripts')
@endauth
@endsection