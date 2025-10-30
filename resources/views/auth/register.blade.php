@extends('layouts.app')

@section('title', 'Registrazione')

@section('content')
<div class="container">
    <div class="form-container">
        <h2>📝 Registrazione</h2>

        <form method="POST" action="{{ route('register') }}" onsubmit="validateRegistrationForm(event)">
            @csrf

            <div class="form-group">
                <label for="name">Nome</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control @error('name') error @enderror" 
                    value="{{ old('name') }}" 
                    required 
                    autofocus
                    maxlength="255"
                >
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control @error('email') error @enderror" 
                    value="{{ old('email') }}" 
                    required
                    maxlength="255"
                >
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <small style="color: #666; font-size: 0.9rem;">Inserisci un'email valida e esistente</small>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control @error('password') error @enderror" 
                    required
                    minlength="8"
                >
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <small style="color: #666; font-size: 0.9rem;">
                    Minimo 8 caratteri, una maiuscola, un numero e un carattere speciale
                </small>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Conferma Password</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    class="form-control" 
                    required
                    minlength="8"
                >
            </div>

            <button type="submit" class="btn btn-primary btn-block">Registrati</button>
        </form>

        <div class="form-link">
            Hai già un account? <a href="{{ route('login') }}">Accedi qui</a>
        </div>
    </div>
</div>
@endsection