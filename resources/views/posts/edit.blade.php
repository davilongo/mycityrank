@extends('layouts.app')

@section('contenido')

<div class="form-page">
    <h1 class="form-page-title">✏️ Editar post</h1>

    <div class="form-card">
        <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" name="title" id="title"
                       value="{{ old('title', $post->title) }}" required>
                @error('title') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="slug">Slug (URL)</label>
                <input type="text" name="slug" id="slug"
                       value="{{ old('slug', $post->slug) }}" required>
                @error('slug') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="category">Categoría</label>
                <input type="text" name="category" id="category"
                       value="{{ old('category', $post->category) }}" required>
                @error('category') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="ciudad_nombre">Ciudad</label>
                <input type="text" name="ciudad_nombre" id="ciudad_nombre"
                       value="{{ old('ciudad_nombre', $post->ciudad->nombre ?? '') }}" required>
                @error('ciudad_nombre') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="content">Descripción</label>
                <textarea name="content" id="content" rows="5" required>{{ old('content', $post->content) }}</textarea>
                @error('content') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Nueva imagen (opcional)</label>
                @if($post->image)
                    <img src="{{ asset($post->image) }}" alt="Imagen actual"
                         style="width:100%; height:160px; object-fit:cover; border-radius:8px; margin-bottom:10px;">
                @endif
                <input type="file" name="image" id="image" accept="image/*">
                @error('image') <span class="error">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn-primary">Guardar cambios</button>
        </form>
    </div>

    <div class="form-back-link">
        <a href="{{ route('posts.show', $post) }}">← Volver al post</a>
    </div>
</div>

@endsection
