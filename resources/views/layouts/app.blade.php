<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XploreFree</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=5">
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
            </div>

            <div class="nav-actions">
                @auth
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

</body>
</html>
