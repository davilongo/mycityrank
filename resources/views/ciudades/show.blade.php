@extends('layouts.app')

@section('contenido')

{{-- ===== CABECERA ===== --}}
<div class="city-hero">
    @if($top3->first()?->image ?? $posts->first()?->image)
        <div class="city-hero-bg" style="background-image:url('{{ asset(($top3->first() ?? $posts->first())->image) }}')"></div>
    @endif
    <div class="city-hero-overlay"></div>
    <div class="city-hero-content">
        <a href="{{ route('posts.index') }}" class="city-hero-back">← Volver</a>
        <h1 class="city-hero-title">📍 {{ $ciudad->nombre }}</h1>
        <p class="city-hero-meta">
            {{ $posts->total() }} {{ $posts->total() === 1 ? 'publicación' : 'publicaciones' }}
            · {{ $followersCount }} {{ $followersCount === 1 ? 'seguidor' : 'seguidores' }}
        </p>
        @auth
            <form method="POST" action="{{ route('ciudades.follow', $ciudad) }}" style="margin-top:12px;">
                @csrf
                <button type="submit" class="btn-follow-city {{ $isFollowing ? 'following' : '' }}">
                    {{ $isFollowing ? '✓ Siguiendo' : '+ Seguir ciudad' }}
                </button>
            </form>
        @endauth
    </div>
</div>

{{-- ===== TOP 3 ===== --}}
@if($top3->isNotEmpty() && !request('categoria'))
<section class="city-top-section">
    <div class="city-top-header">
        <h2 class="city-top-title">⭐ Lo mejor de {{ $ciudad->nombre }}</h2>
        <span class="city-top-subtitle">Los lugares más votados por la comunidad</span>
    </div>
    <div class="city-top-grid">
        @foreach($top3 as $i => $post)
            <a href="{{ route('posts.show', $post) }}" class="city-top-card city-top-rank-{{ $i + 1 }}">
                <div class="city-top-img-wrap">
                    <img src="{{ asset($post->image) }}" alt="{{ $post->title }}">
                    <span class="city-top-rank">{{ ['🥇','🥈','🥉'][$i] }}</span>
                </div>
                <div class="city-top-body">
                    <span class="city-top-cat">{{ $post->category }}</span>
                    <h3 class="city-top-name">{{ $post->title }}</h3>
                    <div class="city-top-stats">
                        <span>❤️ {{ $post->likes_count }}</span>
                        <span>💬 {{ $post->comments_count }}</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ===== FILTRO POR CATEGORÍA ===== --}}
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

{{-- ===== GRID DE POSTS ===== --}}
@if($posts->isEmpty())
    <div class="empty-state">
        <p>No hay publicaciones en esta categoría todavía.</p>
        @auth
            <a href="{{ route('posts.create') }}" class="btn-nav" style="display:inline-block;">Publicar sobre {{ $ciudad->nombre }}</a>
        @endauth
    </div>
@else
    <ul class="city-posts-grid">
        @foreach($posts as $post)
            <li>
                <a href="{{ route('posts.show', $post) }}" class="city-post-card">
                    <img src="{{ asset($post->image) }}" alt="{{ $post->title }}" loading="lazy">
                    <div class="city-post-card-overlay">
                        <div class="city-post-card-cat">{{ $post->category }}</div>
                        <div class="city-post-card-title">{{ $post->title }}</div>
                        <div class="city-post-card-stats">
                            <span>❤️ {{ $post->likes_count }}</span>
                            <span>💬 {{ $post->comments_count }}</span>
                        </div>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
@endif

<div class="pagination">{{ $posts->links() }}</div>

@endsection
