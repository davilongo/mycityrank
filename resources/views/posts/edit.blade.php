<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>XploreFree | Editar Post</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-blue-600 mb-6">
            Edita el Post #{{ $post->id }}
        </h1>

        <div class="mb-4">
            <a href="{{ route('posts.index') }}" class="text-blue-500 hover:text-blue-700">Volver a posts</a>
        </div>

        <form action="{{ route('posts.update', $post) }}" 
            method="POST" 
            enctype="multipart/form-data" 
            class="bg-white shadow-lg rounded-lg p-6">

            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" name="title" id="title" 
                    value="{{ old('title', $post->title) }}" required/>
            </div>

            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" name="slug" id="slug" 
                    value="{{ old('slug', $post->slug) }}" required/>
            </div>

            <div class="form-group">
                <label for="category">Categoría</label>
                <input type="text" name="category" id="category" 
                    value="{{ old('category', $post->category) }}" required/>
            </div>

            <div class="form-group">
                <label for="content">Contenido</label>
                <textarea name="content" id="content" rows="5" required>{{ old('content', $post->content) }}</textarea>
            </div>

            <div class="form-group">
                <label for="ciudad_nombre">Ciudad</label>
                <input type="text" name="ciudad_nombre" id="ciudad_nombre"
                    value="{{ old('ciudad_nombre', $post->ciudad->nombre ?? '') }}" required>
            </div>

            <div class="form-group">
                <label for="image">Actualizar imagen (opcional)</label>
                <input type="file" name="image" id="image" accept="image/*">
            </div>

            <div class="form-group">
                <button type="submit" class="submit-btn">Guardar cambios</button>
            </div>

        </form>

    </div>

</body>
</html>
