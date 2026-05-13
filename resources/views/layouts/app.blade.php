<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XploreFree</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=14">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</head>
<body>

    <nav class="navbar">
        <div class="nav-inner">
            <a href="{{ route('posts.index') }}" class="nav-logo">
                <img src="{{ asset('images/logo.png') }}" alt="XploreFree">
            </a>

            <div class="nav-menu">
                <a href="{{ route('posts.index') }}"
                   class="nav-link {{ request()->routeIs('posts.index') && !request('seccion') ? 'active' : '' }}">
                    🌍 Explorar
                </a>
                <a href="{{ route('mapa') }}"
                   class="nav-link {{ request()->routeIs('mapa') ? 'active' : '' }}">
                    🗺️ Mapa
                </a>
            </div>

            {{-- Buscador de usuarios --}}
            <div x-data="userSearch()" class="nav-user-search" @click.outside="open = false">
                <div class="nav-search-box">
                    <span class="nav-search-icon">🔍</span>
                    <input
                        type="text"
                        x-model="query"
                        @input.debounce.250ms="search()"
                        @keydown.escape="open = false"
                        @focus="query.length >= 2 && search()"
                        placeholder="Buscar personas..."
                        class="nav-search-input"
                        autocomplete="off"
                    >
                </div>
                <div x-show="open" x-transition.opacity class="nav-search-dropdown">
                    <template x-for="u in results" :key="u.id">
                        <a :href="u.url" class="nav-search-item">
                            <span class="nav-search-avatar" x-text="u.initial"></span>
                            <span class="nav-search-name" x-text="u.name"></span>
                            <span class="nav-search-count" x-text="u.posts_count + ' posts'"></span>
                        </a>
                    </template>
                    <p x-show="results.length === 0 && query.length >= 2" class="nav-search-empty">
                        Sin resultados
                    </p>
                </div>
            </div>

            <div class="nav-actions">
                @auth
                    <a href="{{ route('feed') }}" class="nav-link {{ request()->routeIs('feed') ? 'active' : '' }}">🏠 Feed</a>
                    <a href="{{ route('users.discover') }}" class="nav-link {{ request()->routeIs('users.discover') ? 'active' : '' }}">👥 Descubrir</a>
                    <a href="{{ route('bookmarks.index') }}" class="nav-link {{ request()->routeIs('bookmarks.*') ? 'active' : '' }}">🔖 Guardados</a>
                    <a href="{{ route('notifications.index') }}" class="nav-link nav-bell {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                        🔔
                        @php $unread = Auth::user()->unreadNotifications()->count(); @endphp
                        @if($unread > 0)
                            <span class="bell-badge">{{ $unread > 9 ? '9+' : $unread }}</span>
                        @endif
                    </a>
                    <a href="{{ route('posts.create') }}" class="btn-nav">+ Nuevo post</a>
                    <a href="{{ route('users.show', Auth::user()) }}" class="nav-user">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset(Auth::user()->avatar) }}" class="nav-avatar" alt="">
                        @else
                            <span class="nav-avatar-initial">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                        @endif
                        {{ Auth::user()->name }}
                        @if(Auth::user()->isAdmin())
                            <span class="badge-admin">ADMIN</span>
                        @endif
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-ghost">Salir</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost">Entrar</a>
                    <a href="{{ route('register') }}" class="btn-nav">Registrarse</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="main-container">
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @yield('contenido')
    </div>

    @stack('scripts')

    <script>
    function userSearch() {
        return {
            query: '',
            results: [],
            open: false,
            search() {
                if (this.query.length < 2) { this.results = []; this.open = false; return; }
                fetch('/users/buscar?q=' + encodeURIComponent(this.query))
                    .then(r => r.json())
                    .then(data => { this.results = data; this.open = data.length > 0; });
            }
        }
    }
    </script>
</body>
</html>
