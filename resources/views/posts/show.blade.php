<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>XploreFree | Post</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/scripts.js') }}" defer></script> <!-- Incluimos el script -->
</head>
<body>

    <div class="container">
        <div class="post-container">
            <h1>{{ $post->title }}</h1>

            <div>
                <a href="{{ route('posts.index') }}" class="back-link">← Volver a posts</a>
            </div>

            <div>
                <p><strong>Título:</strong> {{ $post->title }}</p>
            </div>

            <div>
                <p><strong>Contenido:</strong> {{ $post->content }}</p>
            </div>

            <div>
                <p><strong>Imagen:</strong></p>
                <img src="{{ asset($post->image) }}" alt="Imagen del post" class="post-image">
            </div>

            <div>
                <a href="{{ route('posts.edit', $post) }}" class="edit-link">✏️ Editar Post</a>
            </div>

            <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="confirmDelete(event)">
                @csrf
                @method('DELETE')
                <button type="submit" class="delete-button">🗑️ Eliminar Post</button>
            </form>
        </div>
    </div>

</body>
</html>
