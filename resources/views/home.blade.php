@extends('layouts.app')

@section('title', 'Home - Traccia i tuoi Film e Serie TV')



@section('content')
<div class="container">
    <div class="hero">
        <h1>🎬 Benvenuto su FilmLists</h1>
        <p>Il posto migliore per tracciare, organizzare e scoprire film.</p>
        
        <div class="hero-actions">
            @guest
                <a href="{{ route('register') }}" class="btn btn-primary" style="font-size: 1.1rem; padding: 0.8rem 2rem;">Registrati Ora</a>
                <a href="{{ route('recommended-films.index') }}" class="btn btn-secondary" style="font-size: 1.1rem; padding: 0.8rem 2rem;">Esplora i Film</a>
            @endguest
            
            @auth
                <a href="{{ route('my-lists.index') }}" class="btn btn-primary" style="font-size: 1.1rem; padding: 0.8rem 2rem;">Vai alle Tue Liste</a>
                <a href="{{ route('recommended-films.index') }}" class="btn btn-secondary" style="font-size: 1.1rem; padding: 0.8rem 2rem;">Scopri Nuovi Film</a>
            @endauth
        </div>
    </div>

    <div style="margin-top: 3rem;">
        <h2 style="text-align: center; margin-bottom: 2rem; color: #e50914; font-size: 2.2rem;">Cosa Puoi Fare</h2>
        
        <div class="features-grid">
            {{-- Feature 1: Esplora --}}
            <div class="feature-card">
                <h3>🎥 Esplora</h3>
                <p>Scopri i film più votati e consigliati, aggiornati quotidianamente da TMDb, pronti per essere aggiunti alla tua watchlist.</p>
            </div>

            {{-- Feature 2: Organizza --}}
            <div class="feature-card">
                <h3>📋 Organizza</h3>
                <p>Non perdere mai il segno. Traccia i film dividendoli in liste comode: 'Da Vedere', 'Sto Guardando', 'Completati' e 'Abbandonati'.</p>
            </div>

            {{-- Feature 3: Interagisci --}}
            <div class="feature-card">
                <h3>❤️ Interagisci</h3>
                <p>Salva i tuoi film preferiti con un solo click per ritrovarli tutti in un unico posto e condividi le tue opinioni lasciando commenti.</p>
            </div>

            
        </div>
    </div>

    @guest
    <div style="text-align: center; margin-top: 4rem; padding: 2.5rem; background: #1a1a1a; border-radius: 10px; border: 1px solid #333;">
        <h3 style="color: #fff; font-size: 1.8rem; margin-bottom: 1rem;">Unisciti alla Community</h3>
        <p style="color: #999; margin-bottom: 1.5rem; font-size: 1.1rem;">Registrati gratuitamente per iniziare a costruire le tue liste e salvare i tuoi preferiti!</p>
        <a href="{{ route('register') }}" class="btn btn-primary" style="font-size: 1.1rem; padding: 0.8rem 2rem;">Inizia Ora, è Gratis</a>
    </div>
    @endguest
</div>
@endsection