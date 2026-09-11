@extends('layouts.app')

@section('title', 'Editar carta — MyCityRank')

@section('contenido')

<link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">

<div class="form-page">
    <div class="form-card" style="max-width:720px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:24px;">
            <a href="{{ route('admin.newsletters.index') }}" class="btn-ghost" style="padding:6px 10px;">&#8592;</a>
            <h1 class="form-title" style="margin:0;">✉️ Editar carta</h1>
        </div>

        @if($errors->any())
            <div class="alert-error">
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.newsletters.update', $newsletter) }}" id="newsletter-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" name="title" id="title" value="{{ old('title', $newsletter->title) }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="excerpt">Resumen corto</label>
                <textarea name="excerpt" id="excerpt" rows="2" maxlength="500" placeholder="Se muestra como vista previa en la landing">{{ old('excerpt', $newsletter->excerpt) }}</textarea>
            </div>

            <div class="form-group">
                <label for="editor">Contenido de la carta</label>
                <div id="editor" style="background:#fff;min-height:280px;">{!! old('body', $newsletter->body) !!}</div>
                <textarea name="body" id="body" style="display:none;">{{ old('body', $newsletter->body) }}</textarea>
            </div>

            <div class="form-group" style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', $newsletter->is_published) ? 'checked' : '' }} style="width:auto;">
                <label for="is_published" style="margin:0;">Publicada en la landing de YuNomad</label>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.newsletters.index') }}" class="btn-ghost">Cancelar</a>
                <button type="submit" class="btn-nav">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
<script>
    const quill = new Quill('#editor', { theme: 'snow' });
    document.getElementById('newsletter-form').addEventListener('submit', function () {
        document.getElementById('body').value = quill.root.innerHTML;
    });
</script>

@endsection
