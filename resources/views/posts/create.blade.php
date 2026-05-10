@extends('layouts.app')

@section('contenido')

<div class="form-page">
    <h1 class="form-page-title">✈️ Crear nuevo post</h1>

    <div class="form-card">
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}"
                       placeholder="Ej: Una semana en Tokio" required>
                @error('title') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="slug">Slug (URL)</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                       placeholder="una-semana-en-tokio" required>
                @error('slug') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="category">Categoría</label>
                <input type="text" name="category" id="category" value="{{ old('category') }}"
                       placeholder="Playa, Montaña, Ciudad..." required>
                @error('category') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="ciudad_nombre">Ciudad</label>
                <input type="text" name="ciudad_nombre" id="ciudad_nombre" value="{{ old('ciudad_nombre') }}"
                       placeholder="París, Roma, Tokio..." required>
                @error('ciudad_nombre') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="content">Descripción</label>
                <textarea name="content" id="content" rows="5"
                          placeholder="Cuéntanos tu experiencia..." required>{{ old('content') }}</textarea>
                @error('content') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="image">Imagen</label>
                <input type="file" name="image" id="image" accept="image/*" required>
                @error('image') <span class="error">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn-primary">Publicar post</button>
        </form>
    </div>

    <div class="form-back-link">
        <a href="{{ route('posts.index') }}">← Volver al feed</a>
    </div>
</div>

@endsection
