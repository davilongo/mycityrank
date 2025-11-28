<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XploreFree | Descubre el mundo</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #0d6efd, #00c897);
            color: white;
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .welcome-box {
            background-color: rgba(0, 0, 0, 0.4);
            padding: 2rem;
            border-radius: 1rem;
        }
        .welcome-box h1 {
            font-size: 3rem;
        }
        .welcome-box p {
            font-size: 1.2rem;
        }
        .btn {
            margin-top: 1.5rem;
            display: inline-block;
            background: white;
            color: #0d6efd;
            padding: 0.8rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="welcome-box">
        <h1>Bienvenido a XploreFree</h1>
        <p>Descubre y comparte los mejores lugares para visitar, comer o explorar.</p>
        <a href="{{ route('posts.index') }}" class="btn">Ver posts</a>
    </div>
</body>
</html>
