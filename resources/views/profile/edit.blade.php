@extends('layouts.app')

@section('title', 'Modifica Profilo')

@section('content')
<div class="container">
    <div class="form-container" style="max-width: 48rem; margin-top: 2rem;">
        <h2>👤 Modifica Profilo</h2>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group" style="text-align: center;">
                <label>Foto Profilo Corrente</label>
                <img src="{{ $user->avatar_url ? Storage::url($user->avatar_url) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=e50914&color=fff&size=128' }}" 
                     alt="Avatar" 
                     style="width: 128px; height: 128px; border-radius: 50%; object-fit: cover; margin: 10px auto; display: block; border: 3px solid #333;">
            </div>

            <div class="form-group">
                <label for="avatar">Carica nuova foto profilo</label>
                <input 
                    type="file" 
                    id="avatar" 
                    name="avatar" 
                    class="form-control @error('avatar') error @enderror"
                >
                <small style="color: #666; font-size: 0.9rem;">
                    Consigliato: 1:1 (quadrato). Max 2MB. Tipi: jpg, png, webp.
                </small>
                @error('avatar')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            {{-- Nome --}}
            <div class="form-group">
                <label for="name">Nome</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control @error('name') error @enderror" 
                    value="{{ old('name', $user->name) }}" 
                    required 
                    autofocus
                    maxlength="255"
                >
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control @error('email') error @enderror" 
                    value="{{ old('email', $user->email) }}" 
                    required
                    maxlength="255"
                >
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="bio">Biografia</label>
                <textarea 
                    id="bio" 
                    name="bio" 
                    class="form-control @error('bio') error @enderror" 
                    rows="4"
                    maxlength="1000"
                    placeholder="Racconta qualcosa di te..."
                >{{ old('bio', $user->bio) }}</textarea>
                @error('bio')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Salva Modifiche</button>
        </form>
    </div>
</div>
@endsection