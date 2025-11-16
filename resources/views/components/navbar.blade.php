<nav class="navbar">
    <div class="container">
        <a href="{{ route('home') }}" class="navbar-brand">🎬 FilmLists</a>
        
        <ul class="navbar-menu">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('recommended-films.index') }}">🎥 Film Consigliati</a></li>
            
            @auth
                <li><a href="{{ route('favorites.index') }}">❤️ Preferiti</a></li>
                <li><a href="{{ route('my-lists.index') }}">📋 Le Mie Liste</a></li>
                <li class="navbar-user">
                    <a href="{{ route('profile.edit') }}" class="user-name" title="Modifica Profilo" style="color: #ddd; text-decoration: none; transition: color 0.3s ease; display: flex; align-items: center; gap: 0.75rem;">
                        <img src="{{ auth()->user()->avatar_url ? Storage::url(auth()->user()->avatar_url) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=e50914&color=fff&size=32' }}" 
                             alt="Avatar" 
                             style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                        Ciao, {{ auth()->user()->name }}
                    </a>
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