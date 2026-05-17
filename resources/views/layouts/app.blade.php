<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'XploreFree — Descubre lugares increíbles')</title>
    <meta name="description" content="@yield('meta_description', 'La comunidad para descubrir, conectar y compartir lugares increíbles.')">
    <!-- Open Graph -->
    <meta property="og:site_name" content="XploreFree">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('title', 'XploreFree — Descubre lugares increíbles')">
    <meta property="og:description" content="@yield('meta_description', 'La comunidad para descubrir, conectar y compartir lugares increíbles.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/logo.png'))">
    <meta property="og:locale" content="es_ES">
    <!-- Twitter / X -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'XploreFree')">
    <meta name="twitter:description" content="@yield('meta_description', 'La comunidad para descubrir, conectar y compartir lugares increíbles.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/logo.png'))">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=21">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</head>
<body>

    <nav class="navbar" x-data="{ mobileOpen: false }">
        <div class="nav-inner">
            <a href="{{ route('posts.index') }}" class="nav-logo">
                <img src="{{ asset('images/logo.png') }}" alt="XploreFree">
            </a>

            {{-- Menú desktop --}}
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

            {{-- Buscador desktop --}}
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

            {{-- Acciones desktop --}}
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

            {{-- Botón hamburguesa (solo móvil) --}}
            <button class="nav-hamburger" @click="mobileOpen = !mobileOpen"
                    :class="{ 'nav-hamburger--open': mobileOpen }"
                    aria-label="Abrir menú">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        {{-- Menú móvil desplegable --}}
        <div class="nav-mobile-menu" x-show="mobileOpen" x-cloak x-transition.opacity>

            {{-- Buscador --}}
            <div x-data="userSearch()" class="nav-mobile-search" @click.outside="open = false">
                <div class="nav-search-box">
                    <span class="nav-search-icon">🔍</span>
                    <input type="text" x-model="query"
                        @input.debounce.250ms="search()"
                        @keydown.escape="open = false"
                        @focus="query.length >= 2 && search()"
                        placeholder="Buscar personas..."
                        class="nav-search-input"
                        autocomplete="off">
                </div>
                <div x-show="open" x-transition.opacity class="nav-search-dropdown">
                    <template x-for="u in results" :key="u.id">
                        <a :href="u.url" class="nav-search-item">
                            <span class="nav-search-avatar" x-text="u.initial"></span>
                            <span class="nav-search-name" x-text="u.name"></span>
                            <span class="nav-search-count" x-text="u.posts_count + ' posts'"></span>
                        </a>
                    </template>
                    <p x-show="results.length === 0 && query.length >= 2" class="nav-search-empty">Sin resultados</p>
                </div>
            </div>

            <div class="nav-mobile-divider"></div>

            <a href="{{ route('posts.index') }}"
               class="nav-mobile-link {{ request()->routeIs('posts.index') && !request('seccion') ? 'active' : '' }}">
                🌍 Explorar
            </a>
            <a href="{{ route('mapa') }}"
               class="nav-mobile-link {{ request()->routeIs('mapa') ? 'active' : '' }}">
                🗺️ Mapa
            </a>

            @auth
                <div class="nav-mobile-divider"></div>

                <a href="{{ route('feed') }}"
                   class="nav-mobile-link {{ request()->routeIs('feed') ? 'active' : '' }}">
                    🏠 Feed
                </a>
                <a href="{{ route('users.discover') }}"
                   class="nav-mobile-link {{ request()->routeIs('users.discover') ? 'active' : '' }}">
                    👥 Descubrir
                </a>
                <a href="{{ route('bookmarks.index') }}"
                   class="nav-mobile-link {{ request()->routeIs('bookmarks.*') ? 'active' : '' }}">
                    🔖 Guardados
                </a>
                <a href="{{ route('notifications.index') }}"
                   class="nav-mobile-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    🔔 Notificaciones
                    @if($unread > 0)
                        <span class="bell-badge" style="position:static;display:inline-flex;margin-left:4px;">{{ $unread > 9 ? '9+' : $unread }}</span>
                    @endif
                </a>
                <a href="{{ route('posts.create') }}" class="nav-mobile-link" style="color:var(--accent);font-weight:600;">
                    ✏️ Nuevo post
                </a>

                <div class="nav-mobile-divider"></div>

                <a href="{{ route('users.show', Auth::user()) }}" class="nav-mobile-link">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset(Auth::user()->avatar) }}" style="width:24px;height:24px;border-radius:50%;object-fit:cover;" alt="">
                    @else
                        <span style="width:24px;height:24px;border-radius:50%;background:var(--accent);display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;flex-shrink:0;">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                    @endif
                    {{ Auth::user()->name }}
                    @if(Auth::user()->isAdmin())
                        <span class="badge-admin">ADMIN</span>
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-mobile-link" style="color:#f87171;">
                        🚪 Cerrar sesión
                    </button>
                </form>
            @else
                <div class="nav-mobile-divider"></div>
                <a href="{{ route('login') }}" class="nav-mobile-link">Entrar</a>
                <a href="{{ route('register') }}" class="nav-mobile-link" style="color:var(--accent);font-weight:600;">Registrarse</a>
            @endauth
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
    async function compressImg(file, maxW = 1200, quality = 0.82) {
        return new Promise(resolve => {
            const reader = new FileReader();
            reader.onload = e => {
                const img = new Image();
                img.onload = () => {
                    let w = img.width, h = img.height;
                    if (w > maxW) { h = Math.round(h * maxW / w); w = maxW; }
                    const canvas = document.createElement('canvas');
                    canvas.width = w; canvas.height = h;
                    canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                    canvas.toBlob(blob => {
                        const name = file.name.replace(/\.[^.]+$/, '') + '.jpg';
                        resolve(new File([blob], name, { type: 'image/jpeg' }));
                    }, 'image/jpeg', quality);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

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

    {{-- ===== BOTTOM NAV (móvil) ===== --}}
    <nav class="bottom-nav">
        <a href="{{ route('posts.index') }}"
           class="bn-item {{ request()->routeIs('posts.index') ? 'bn-item--on' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <span>Explorar</span>
        </a>

        <a href="{{ route('mapa') }}"
           class="bn-item {{ request()->routeIs('mapa') ? 'bn-item--on' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
                <line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/>
            </svg>
            <span>Mapa</span>
        </a>

        @auth
            <a href="{{ route('posts.create') }}" class="bn-item bn-item--create">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
            </a>
        @else
            <a href="{{ route('login') }}" class="bn-item bn-item--create">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
            </a>
        @endauth

        @auth
            <a href="{{ route('feed') }}"
               class="bn-item {{ request()->routeIs('feed') ? 'bn-item--on' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
                <span>Feed</span>
            </a>

            <a href="{{ route('users.show', Auth::user()) }}"
               class="bn-item {{ request()->routeIs('users.show') && request()->route('user')?->id === Auth::id() ? 'bn-item--on' : '' }}">
                @if(Auth::user()->avatar)
                    <img src="{{ asset(Auth::user()->avatar) }}" class="bn-avatar" alt="">
                @else
                    <span class="bn-avatar-letter">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                @endif
                <span>Perfil</span>
            </a>
        @else
            <a href="{{ route('users.discover') }}"
               class="bn-item {{ request()->routeIs('users.discover') ? 'bn-item--on' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span>Descubrir</span>
            </a>

            <a href="{{ route('login') }}"
               class="bn-item {{ request()->routeIs('login') ? 'bn-item--on' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <span>Entrar</span>
            </a>
        @endauth
    </nav>

</body>
</html>
