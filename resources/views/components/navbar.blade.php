<nav class="navbar">
    <div class="container">
        <a href="{{ route('home') }}" class="navbar-brand">🎬 FilmNews</a>
        
        <ul class="navbar-menu">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('recommended-films.index') }}">🎥 Film Consigliati</a></li>
            
            @auth
                <li><a href="{{ route('favorites.index') }}">❤️ Preferiti</a></li>
                <li><a href="{{ route('my-lists.index') }}">📋 Le Mie Liste</a></li>
                <li class="navbar-user">
                    <span class="user-name">Ciao, {{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary">Logout</button>
                    </form>
                </li>
            @else
                <li><a href="{{ route('login') }}" class="btn btn-secondary">Login</a></li>
                <li><a href="{{ route('register') }}" class="btn btn-primary">Registrati</a></li>
            @endauth
        </ul>
    </div>
</nav>