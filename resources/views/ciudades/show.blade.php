@extends('layouts.app')

@section('contenido')

<style>
  body { background: linear-gradient(135deg, #0f172a 0%, #0c1a3a 50%, #0f172a 100%) !important; }
  .main-container { background: transparent !important; }
</style>

<div class="ciudad-page">
<div class="ciudad-layout">

    {{-- ===== LEFT COLUMN ===== --}}
    <div class="ciudad-main">

        {{-- HERO --}}
        <div class="ciudad-hero">
            @php $heroImg = $top3->first()?->image ?? $posts->first()?->image; @endphp
            @if($heroImg)
                <div class="ciudad-hero-bg" style="background-image:url('{{ asset($heroImg) }}')"></div>
            @endif
            <div class="ciudad-hero-overlay"></div>
            <div class="ciudad-hero-content">
                <a href="{{ route('posts.index') }}" class="ciudad-hero-back">← Volver</a>
                <h1 class="ciudad-hero-title">📍 {{ $ciudad->nombre }}</h1>
                <p class="ciudad-hero-meta">
                    {{ $posts->total() }} {{ $posts->total() === 1 ? 'publicación' : 'publicaciones' }}
                    &nbsp;·&nbsp;
                    {{ $followersCount }} {{ $followersCount === 1 ? 'seguidor' : 'seguidores' }}
                </p>
                @auth
                    <form method="POST" action="{{ route('ciudades.follow', $ciudad) }}" style="margin-top:14px;">
                        @csrf
                        <button type="submit" class="btn-follow-city {{ $isFollowing ? 'following' : '' }}">
                            {{ $isFollowing ? '✓ Siguiendo' : '+ Seguir ciudad' }}
                        </button>
                    </form>
                @endauth
            </div>
        </div>

        {{-- DESTACADOS --}}
        @if($top3->isNotEmpty() && !request('categoria'))
        <section class="ciudad-section">
            <div class="ciudad-section-hd">
                <h2 class="ciudad-section-title">⭐ Destacados de la comunidad</h2>
                <span class="ciudad-section-sub">Los lugares más votados</span>
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

        {{-- CATEGORÍAS --}}
        <div class="cat-filter-wrap">
            <a href="{{ route('ciudades.show', $ciudad) }}"
               class="cat-pill {{ !request('categoria') ? 'active' : '' }}">Todas</a>
            @foreach($categorias as $cat)
                <a href="{{ route('ciudades.show', $ciudad) }}?categoria={{ urlencode($cat) }}"
                   class="cat-pill {{ request('categoria') === $cat ? 'active' : '' }}">{{ $cat }}</a>
            @endforeach
        </div>

        {{-- MÁS LUGARES --}}
        <section class="ciudad-section">
            <div class="ciudad-section-hd">
                <h2 class="ciudad-section-title">
                    {{ request('categoria') ? request('categoria') : 'Más lugares en ' . $ciudad->nombre }}
                </h2>
            </div>

            @if($posts->isEmpty())
                <div class="empty-state">
                    <p>No hay publicaciones en esta categoría todavía.</p>
                    @auth
                        <a href="{{ route('posts.create') }}" class="btn-nav" style="display:inline-block;margin-top:10px;">
                            Publicar sobre {{ $ciudad->nombre }}
                        </a>
                    @endauth
                </div>
            @else
                <ul class="city-posts-grid" style="margin-top:0;">
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
                <div class="pagination" style="margin-top:20px;">{{ $posts->links() }}</div>
            @endif
        </section>

    </div>{{-- /ciudad-main --}}

    {{-- ===== SIDEBAR ===== --}}
    <div class="ciudad-sidebar">

        {{-- Mapa con posts de la ciudad --}}
        @if($mapPosts->isNotEmpty())
        <div class="ciudad-sidebar-card">
            <div class="ciudad-sidebar-card-hd">🗺️ Mapa de lugares</div>
            <div id="ciudad-map" class="ciudad-map"></div>
            <a href="{{ route('posts.map') }}" target="_blank" class="ciudad-map-link">
                Ver mapa ampliado ↗
            </a>
        </div>
        @endif

        {{-- Top categorías --}}
        @if($topCategorias->isNotEmpty())
        <div class="ciudad-sidebar-card">
            <div class="ciudad-sidebar-card-hd">📊 Top categorías</div>
            <div class="ciudad-top-cats">
                @foreach($topCategorias as $cat)
                    <a href="{{ route('ciudades.show', $ciudad) }}?categoria={{ urlencode($cat->category) }}"
                       class="ciudad-top-cat-item {{ request('categoria') === $cat->category ? 'ciudad-top-cat-item--on' : '' }}">
                        <span class="ciudad-top-cat-name">{{ $cat->category }}</span>
                        <span class="ciudad-top-cat-count">{{ $cat->total }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>{{-- /ciudad-sidebar --}}

</div>{{-- /ciudad-layout --}}
</div>{{-- /ciudad-page --}}

@if($mapPosts->isNotEmpty())
@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const posts = @json($mapPosts);
    const lats = posts.map(p => parseFloat(p.lat));
    const lngs = posts.map(p => parseFloat(p.lng));
    const centerLat = lats.reduce((a,b) => a+b, 0) / lats.length;
    const centerLng = lngs.reduce((a,b) => a+b, 0) / lngs.length;

    const map = L.map('ciudad-map', { zoomControl: true, scrollWheelZoom: false })
        .setView([centerLat, centerLng], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    posts.forEach(p => {
        const marker = L.marker([parseFloat(p.lat), parseFloat(p.lng)]).addTo(map);
        marker.bindPopup(`
            <div class="map-popup">
                <img src="${p.image}" alt="">
                <div class="map-popup-body">
                    <a href="/posts/${p.slug}" class="map-popup-title">${p.title}</a>
                </div>
            </div>
        `);
    });

    if (posts.length > 1) {
        const bounds = L.latLngBounds(posts.map(p => [parseFloat(p.lat), parseFloat(p.lng)]));
        map.fitBounds(bounds, { padding: [30, 30] });
    }
})();
</script>
@endpush
@endif

@endsection
