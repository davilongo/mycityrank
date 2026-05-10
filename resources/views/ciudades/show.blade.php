@extends('layouts.app')

@section('contenido')

{{-- ===== CABECERA DE CIUDAD ===== --}}
<div class="city-hero">
    @if($posts->first()?->image)
        <div class="city-hero-bg" style="background-image:url('{{ asset($posts->first()->image) }}')"></div>
    @endif
    <div class="city-hero-overlay"></div>
    <div class="city-hero-content">
        <a href="{{ route('posts.index') }}" class="city-hero-back">← Volver</a>
        <h1 class="city-hero-title">📍 {{ $ciudad->nombre }}</h1>
        <p class="city-hero-meta">{{ $posts->total() }} {{ $posts->total() === 1 ? 'publicación' : 'publicaciones' }}</p>
    </div>
</div>

{{-- ===== FILTRO POR CATEGORÍA ===== --}}
@if($categorias->isNotEmpty())
    <div class="cat-filter-wrap">
        <a href="{{ route('ciudades.show', $ciudad) }}"
           class="cat-pill {{ !request('categoria') ? 'active' : '' }}">
            Todos
        </a>
        @foreach($categorias as $cat)
            <a href="{{ route('ciudades.show', $ciudad) }}?categoria={{ urlencode($cat) }}"
               class="cat-pill {{ request('categoria') === $cat ? 'active' : '' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div>
@endif

{{-- ===== GRID DE POSTS ===== --}}
@if($posts->isEmpty())
    <div class="empty-state">
        <p>No hay publicaciones en esta categoría todavía.</p>
        @auth
            <a href="{{ route('posts.create') }}" class="btn-nav" style="display:inline-block;">Publicar sobre {{ $ciudad->nombre }}</a>
        @endauth
    </div>
@else
    <ul class="posts-grid" style="margin-top:24px;">
        @foreach($posts as $post)
            <li class="post-card">
                <a href="{{ route('posts.show', $post) }}">
                    <div class="card-image-wrap">
                        <img src="{{ asset($post->image) }}" alt="{{ $post->title }}" loading="lazy">
                    </div>
                </a>
                <div class="card-body">
                    <a href="{{ route('posts.show', $post) }}">
                        <h3 class="card-title">{{ $post->title }}</h3>
                    </a>
                    <div class="card-row">
                        <span class="card-category">{{ $post->category }}</span>
                        <div class="card-stats">
                            <span>❤️ {{ $post->likes_count }}</span>
                            <span>💬 {{ $post->comments_count }}</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('users.show', $post->user) }}" class="card-author">
                            <span class="card-author-avatar">{{ mb_substr($post->user->name ?? 'A', 0, 1) }}</span>
                            {{ $post->user->name ?? 'Anónimo' }}
                        </a>
                        <span class="card-date">{{ $post->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
@endif

<div class="pagination">{{ $posts->links() }}</div>

@endsection
