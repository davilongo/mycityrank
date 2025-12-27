<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XploraFree</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- TU CSS del grid + logo -->
    <style>
        /* TODO tu CSS anterior del grid, logo, etc. */
        nav { background-color: white; padding: 20px 15px; border-bottom: 2px solid #ddd; }
        nav a { color: #0d6efd; font-weight: bold; margin-left: 15px; text-decoration: none; }
        nav a:hover { text-decoration: underline; }
        .main-container { padding-top: 90px; padding-bottom: 30px; }
        
        /* PEGA AQUÍ todo tu CSS del grid que teníamos */
        .posts-grid { /* ... tu CSS del grid ... */ }
        
        /* LOGIN NAVBAR */
        nav .login-links a { margin-left: 15px !important; }
    </style>
</head>
<body>
    <!-- TU NAVBAR CON LOGIN -->
    <nav class="d-flex align-items-center justify-content-between">
        <a href="{{ route('posts.index') }}" class="navbar-brand">
            <img src="{{ asset('images/logo.png') }}" alt="XploraFree">
        </a>
        
        <div class="login-links">
            @auth
                <span style="color: #0d6efd; font-weight: bold;">Hola, {{ Auth::user()->name }}!</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" 
                       style="color: #dc3545; font-weight: bold;">Cerrar sesión</a>
                </form>
            @else
                <a href="{{ route('login') }}">Entrar</a>
                <a href="{{ route('register') }}">Registrarse</a>
            @endauth
        </div>
    </nav>

    <div class="main-container">
        @yield('contenido')
    </div>
</body>
</html>
