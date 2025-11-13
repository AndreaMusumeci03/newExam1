@extends('layouts.app')

@section('title', 'Home - Film & Serie TV News')

@section('content')
<div class="container">
    <div class="hero">
        <h1>🎬 Benvenuto su FilmNews</h1>
        <p>Scopri le ultime notizie su film e serie TV</p>
        <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem;">
            @guest
                <a href="{{ route('register') }}" class="btn btn-outline">Registrati Ora</a>
            @endguest
        </div>
    </div>

    <div style="margin-top: 3rem;">
        <h2 style="text-align: center; margin-bottom: 2rem; color: #e50914;">Cosa Troverai</h2>
        
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div class="card">
                <div class="card-body">
                    <h3 style="color: #e50914; margin-bottom: 1rem;">📰 News Aggiornate</h3>
                    <p>Rimani aggiornato con le ultime notizie su film e serie TV da tutto il mondo.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3 style="color: #e50914; margin-bottom: 1rem;">💬 Commenti</h3>
                    <p>Condividi le tue opinioni e discuti con altri appassionati di cinema e serie TV.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3 style="color: #e50914; margin-bottom: 1rem;">❤️ Preferiti</h3>
                    <p>Salva le tue notizie preferite per leggerle quando vuoi.</p>
                </div>
            </div>
        </div>
    </div>

    @guest
    <div style="text-align: center; margin-top: 3rem; padding: 2rem; background: #1a1a1a; border-radius: 10px;">
        <h3 style="color: #e50914; margin-bottom: 1rem;">Unisciti alla Community</h3>
        <p style="color: #999; margin-bottom: 1.5rem;">Registrati per commentare le notizie e salvare i tuoi preferiti!</p>
        <a href="{{ route('register') }}" class="btn btn-primary">Registrati Gratuitamente</a>
    </div>
    @endguest
</div>
@endsection