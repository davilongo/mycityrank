@extends('layouts.app')

@section('contenido')

{{-- ===== HERO ===== --}}
<section class="hero">
    <div class="hero-inner">
        <h1 class="hero-title">Descubre el mundo</h1>
        <p class="hero-subtitle">Busca una ciudad y explora todo lo que tiene para ofrecer</p>

        <div x-data="citySearch()" class="search-wrap" @click.outside="open = false">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input
                    type="text"
                    x-model="query"
                    @input.debounce.300ms="search()"
                    @keydown.enter.prevent="goFirst()"
                    @keydown.escape="open = false"
                    @focus="query.length >= 2 && search()"
                    placeholder="¿A dónde vas? Ej: Algeciras, Madrid, Sevilla..."
                    class="search-input"
                    autocomplete="off"
                >
                <button @click="goFirst()" class="search-btn">Buscar</button>
            </div>

            <div x-show="open" x-transition.opacity class="search-dropdown">
                <template x-for="ciudad in results" :key="ciudad.id">
                    <button @click="go(ciudad.nombre)" class="search-item">
                        <span class="search-item-name" x-text="ciudad.nombre"></span>
                        <span class="search-item-count" x-text="ciudad.posts_count + ' publicaciones'"></span>
                    </button>
                </template>
                <p x-show="results.length === 0 && query.length >= 2" class="search-empty">
                    No encontramos esa ciudad
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ===== TRENDING ESTA SEMANA ===== --}}
@if($trending->isNotEmpty())
<section class="section">
    <div class="section-header">
        <div>
            <h2 class="section-title">🔥 Tendencia esta semana</h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px;">Los lugares más populares de los últimos 7 días</p>
        </div>
    </div>
    <div class="trending-scroll">
        @foreach($trending as $post)
            <a href="{{ route('posts.show', $post) }}" class="trending-card">
                <img src="{{ asset($post->image) }}" alt="{{ $post->title }}" loading="lazy">
                <div class="trending-card-overlay">
                    @if($post->ciudad)
                        <div class="trending-card-city">📍 {{ $post->ciudad->nombre }}</div>
                    @endif
                    <div class="trending-card-title">{{ Str::limit($post->title, 40) }}</div>
                    <div class="trending-card-stats">❤️ {{ $post->likes_count }}</div>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ===== RANKING DE CIUDADES ===== --}}
@if($ciudadesPopulares->isNotEmpty())
<section class="section" id="destinos">
    <div class="section-header">
        <div>
            <h2 class="section-title">🏆 Ranking de ciudades</h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px;">Las ciudades con más contenido de la comunidad</p>
        </div>
    </div>
    <div class="cities-grid">
        @foreach($ciudadesPopulares as $i => $ciudad)
            <a href="{{ route('ciudades.show', $ciudad) }}" class="city-card">
                @if($ciudad->posts->first()?->image)
                    <img src="{{ asset($ciudad->posts->first()->image) }}" alt="{{ $ciudad->nombre }}">
                @else
                    <div class="city-card-placeholder"></div>
                @endif
                @if($i < 3)
                    <span class="city-rank-badge">{{ ['🥇','🥈','🥉'][$i] }}</span>
                @endif
                <div class="city-card-overlay">
                    <span class="city-card-name">{{ $ciudad->nombre }}</span>
                    <span class="city-card-count">{{ $ciudad->posts_count }} {{ $ciudad->posts_count === 1 ? 'lugar' : 'lugares' }}</span>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ===== HASHTAGS POPULARES ===== --}}
@if($popularHashtags->isNotEmpty())
<section class="section">
    <div class="section-header">
        <div>
            <h2 class="section-title">🏷️ Hashtags populares</h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px;">Explora por temática</p>
        </div>
    </div>
    <div class="hashtag-strip">
        @foreach($popularHashtags as $tag)
            <a href="{{ route('hashtag.show', $tag->name) }}" class="hashtag-strip-pill">
                #{{ $tag->name }}
                <span class="hashtag-strip-count">{{ $tag->posts_count }}</span>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ===== ÚLTIMAS PUBLICACIONES ===== --}}
<section class="section">
    <div class="section-header">
        <div>
            <h2 class="section-title">🌍 Últimas publicaciones</h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px;">{{ $posts->total() }} publicaciones en total</p>
        </div>
        @auth
            <a href="{{ route('posts.create') }}" class="btn-nav">+ Nuevo post</a>
        @endauth
    </div>

    @if($posts->isEmpty())
        <div class="empty-state">
            <p>No hay publicaciones todavía.</p>
            @auth
                <a href="{{ route('posts.create') }}" class="btn-nav" style="display:inline-block;">Crear el primero</a>
            @else
                <a href="{{ route('login') }}" class="btn-nav" style="display:inline-block;">Inicia sesión para publicar</a>
            @endauth
        </div>
    @else
        <ul class="posts-grid">
            @foreach ($posts as $post)
                <li class="post-card">
                    <a href="{{ route('posts.show', $post) }}">
                        <div class="card-image-wrap">
                            <img src="{{ asset($post->image) }}" alt="{{ $post->title }}" loading="lazy">
                            @if($post->ciudad)
                                <span class="card-city-badge">📍 {{ $post->ciudad->nombre }}</span>
                            @endif
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
                            <a href="{{ $post->user ? route('users.show', $post->user) : '#' }}" class="card-author">
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
</section>

<script>
function citySearch() {
    return {
        query: '',
        results: [],
        open: false,
        search() {
            if (this.query.length < 2) { this.results = []; this.open = false; return; }
            fetch('/ciudades/buscar?q=' + encodeURIComponent(this.query))
                .then(r => r.json())
                .then(data => { this.results = data; this.open = true; });
        },
        go(nombre) {
            window.location.href = '/ciudades/' + encodeURIComponent(nombre);
        },
        goFirst() {
            if (this.results.length > 0) this.go(this.results[0].nombre);
        }
    }
}
</script>

@endsection
