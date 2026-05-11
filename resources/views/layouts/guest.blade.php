<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CityRank') }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=9">
</head>
<body>
    <div class="auth-wrap">
        <a href="{{ route('posts.index') }}" class="auth-logo">
            <img src="{{ asset('images/logo.png') }}" alt="CityRank">
        </a>
        <div class="auth-card">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
