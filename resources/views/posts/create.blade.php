<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>XploraFree | Create</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <div class="container">
        <h1 class="title">¡Crea tu Post!</h1>

        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="form">
            @csrf

            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" name="title" id="title" />
            </div>

            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" name="slug" id="slug" />
            </div>

            <div class="form-group">
                <label for="category">Categoría</label>
                <input type="text" name="category" id="category" />
            </div>

            <div class="form-group">
                <label for="content">Contenido</label>
                <textarea name="content" id="content" rows="5"></textarea>
            </div>

            <div class="form-group">
                <label for="image">Imagen</label>
                <input type="file" name="image" id="image" accept="image/*" />
                @error('image')
                    <small class="error">{{ $message }}</small>
                @enderror
            </div>

           <div class="form-group">
                <label for="ciudad_nombre">Ciudad</label>
                <input type="text" name="ciudad_nombre" id="ciudad_nombre" required>
            </div>



            <div class="form-group">
                <button type="submit" class="submit-btn">Crear post</button>
            </div>
        </form>
    </div>




</body>
</html>
