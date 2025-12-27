@extends('layouts.app')

@section('contenido')
<div style="max-width: 900px; margin: 0 auto; padding: 20px;">
    <!-- Imagen grande -->
    <img src="{{ asset($post->image) }}" alt="{{ $post->title }}" 
         style="width: 100%; height: 400px; object-fit: cover; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); display: block; margin-bottom: 25px;">
    
    <h1 style="font-size: 2.2rem; color: #333; margin-bottom: 20px;">{{ $post->title }}</h1>
    
    <!-- Info ciudad/categoría -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 25px; display: flex; gap: 20px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 1.5rem;">📍</span>
            <strong>{{ $post->ciudad->nombre ?? 'Explora' }}</strong>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 1.5rem;">🏷️</span>
            <span style="background: #0d6efd; color: white; padding: 4px 12px; border-radius: 20px;">{{ $post->category }}</span>
        </div>
    </div>
    
    <!-- Contenido -->
    <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); line-height: 1.7; font-size: 1.1rem; margin-bottom: 30px;">
        {!! nl2br(e($post->content)) !!}
    </div>
    
    <!-- Botones -->
    <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
        <a href="{{ route('posts.index') }}" 
           style="padding: 12px 30px; background: #6c757d; color: white; text-decoration: none; border-radius: 25px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
           ← Volver al feed
        </a>
        <a href="{{ route('posts.edit', $post) }}" 
           style="padding: 12px 30px; background: #0d6efd; color: white; text-decoration: none; border-radius: 25px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
           ✏️ Editar
        </a>
    </div>

    <!-- Comentarios -->
    <div style="margin-top: 40px;">
        <h3 style="color: #333; margin-bottom: 20px;">💬 Comentarios ({{ $post->comments->count() }})</h3>
        
        @auth
        <!-- Formulario nuevo comentario -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 25px;">
            <form method="POST" action="{{ route('posts.comment', $post) }}">
                @csrf
                <textarea name="body" placeholder="¡Comparte tu experiencia!" 
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical; min-height: 80px;"
                        required></textarea>
                <button type="submit" style="margin-top: 10px; padding: 10px 20px; background: #0d6efd; color: white; border: none; border-radius: 25px; font-weight: 600;">
                    Comentar
                </button>
            </form>
        </div>
        @endauth

        <!-- Lista comentarios -->
        @forelse($post->comments as $comment)
        <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span style="font-weight: 700; color: #0d6efd;">{{ $comment->user->name }}</span>
                <span style="color: #666; font-size: 0.9rem;">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <p style="margin: 0; line-height: 1.6;">{{ $comment->body }}</p>
        </div>
        @empty
        <p style="color: #666; text-align: center; padding: 40px; background: #f8f9fa; border-radius: 12px;">¡Sé el primero en comentar!</p>
        @endforelse
    </div>

@endsection
