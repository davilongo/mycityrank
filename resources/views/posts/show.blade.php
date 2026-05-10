@extends('layouts.app')

@section('contenido')

<div class="post-detail-nav">
    <a href="{{ route('posts.index') }}" class="btn-back">← Volver</a>
    @auth
        @if(Auth::id() === $post->user_id || Auth::user()->isAdmin())
            <a href="{{ route('posts.edit', $post) }}" class="btn-edit">✏️ Editar</a>
            <form method="POST" action="{{ route('posts.destroy', $post) }}" style="display:inline;"
                  onsubmit="return confirm('¿Seguro que quieres borrar este post?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete">🗑️ Borrar</button>
            </form>
        @endif
    @endauth
</div>

<div class="post-detail-wrap">

    <!-- Imagen -->
    <div class="post-detail-image">
        <img src="{{ asset($post->image) }}" alt="{{ $post->title }}">
    </div>

    <!-- Panel derecho -->
    <div class="post-detail-side">

        <div class="post-detail-header">
            <div class="post-detail-avatar">{{ mb_substr($post->user->name ?? 'A', 0, 1) }}</div>
            <span class="post-detail-username">{{ $post->user->name ?? 'Anónimo' }}</span>
        </div>

        <div class="post-detail-meta">
            <span class="meta-badge">📍 {{ $post->ciudad->nombre ?? 'Desconocida' }}</span>
            <span class="meta-badge">🏷️ {{ $post->category }}</span>
            <span class="meta-badge">🕐 {{ $post->created_at->diffForHumans() }}</span>
        </div>

        <div class="post-detail-caption">
            <h1>{{ $post->title }}</h1>
            <p>{!! nl2br(e($post->content)) !!}</p>
        </div>

        <div class="post-detail-comments">
            @forelse($post->comments as $comment)
                <div class="comment-item">
                    <div class="comment-avatar">{{ mb_substr($comment->user->name ?? 'A', 0, 1) }}</div>
                    <div class="comment-body">
                        <span class="comment-username">{{ $comment->user->name }}</span>
                        <span class="comment-text">{{ $comment->body }}</span>
                        <div class="comment-time">{{ $comment->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @empty
                <p class="no-comments">Aún no hay comentarios</p>
            @endforelse
        </div>

        <div class="post-detail-actions">
            <form style="display:inline;" action="{{ route('posts.like', $post) }}" method="POST">
                @csrf
                @auth
                    <button type="submit" class="like-btn">
                        {{ $post->likes->contains('user_id', Auth::id()) ? '❤️' : '🤍' }}
                    </button>
                @else
                    <a href="{{ route('login') }}" style="font-size:22px;">🤍</a>
                @endauth
            </form>
            <div class="post-detail-likes">{{ $post->likes->count() }} Me gusta</div>
            <div class="post-detail-date">{{ $post->created_at->format('d M Y') }}</div>
        </div>

        @auth
            <form class="post-detail-comment-form" method="POST" action="{{ route('posts.comment', $post) }}">
                @csrf
                <textarea name="body" placeholder="Añade un comentario..." required rows="1"
                          oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"></textarea>
                <button type="submit">Publicar</button>
            </form>
        @else
            <div class="login-to-comment">
                <a href="{{ route('login') }}">Inicia sesión</a> para comentar.
            </div>
        @endauth

    </div>
</div>

@endsection
