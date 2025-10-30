<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Film & Serie TV News')</title>
    
    {{-- CSS Esterno --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @stack('styles')
</head>
<body>
    {{-- Navbar --}}
    @include('components.navbar')
    
    {{-- Alerts Container --}}
    <div class="container">
        <div id="alerts-container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            
            @if(session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif
        </div>
    </div>
    
    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>
    
    {{-- Footer --}}
    @include('components.footer')
    
    {{-- JavaScript Esterno --}}
    <script src="{{ asset('js/app.js') }}"></script>
    
    @stack('scripts')
</body>
</html>