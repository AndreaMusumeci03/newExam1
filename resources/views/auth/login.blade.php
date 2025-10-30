@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="form-container">
        <h2>🔐 Login</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control @error('email') error @enderror" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                >
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control @error('password') error @enderror" 
                    required
                >
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Ricordami</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Accedi</button>
        </form>

        <div class="form-link">
            Non hai un account? <a href="{{ route('register') }}">Registrati qui</a>
        </div>
    </div>
</div>
@endsection