<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>XploraFree</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @yield('extra-head')
    <style>
        nav {
            background-color: white;
            padding: 20px 15px;
            border-bottom: 2px solid #ddd;
        }

       

        nav a {
            color: #0d6efd;
            font-weight: bold;
            margin-left: 15px;
            text-decoration: none;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .main-container {
            padding-top: 90px;
            padding-bottom: 30px;
        }
        /* Logo principal */
        nav .navbar-brand img {
            height: 20vh; /* 8% de la altura de la ventana, se adapta al tamaño de pantalla */
            width: auto; /* Mantiene la proporción original */
            transition: height 0.3s ease; /* Efecto suave al cambiar tamaño */
        }

        /* Ajustes para pantallas muy pequeñas */
        @media (max-width: 576px) {
            nav .navbar-brand img {
                height: 6vh; /* Más pequeño en móviles */
            }
        }
    </style>
</head>
<body>

    <!-- Menú superior -->
    <nav class="d-flex justify-content-between align-items-center">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('images/logo-xplorefree.png') }}" alt="Logo XploraFree">
        </a>
        <div>
            <a href="{{ url('/posts') }}">¿Que ver?</a>
            <a href="{{ url('/registro') }}">Registrarse</a>
            <a href="{{ url('/login') }}">Loguearse</a>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container main-container">
        @yield('contenido')
    </div>

    @yield('scripts')
</body>
</html>
