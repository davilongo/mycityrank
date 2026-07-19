@extends('layouts.app')

@section('title', $ciudad->nombre . ' — XploreFree')
@section('meta_description', 'Descubre los mejores lugares en ' . $ciudad->nombre . '. ' . $posts->total() . ' publicaciones de la comunidad.')
@if($top3->first()?->image ?? $posts->first()?->image)
@section('og_image', url(($top3->first() ?? $posts->first())->image))
@endif

@section('contenido')

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
                <a href="{{ route('posts.index') }}" class="ciudad-hero-back">{{ __('cities.back') }}</a>
                <h1 class="ciudad-hero-title">📍 {{ $ciudad->nombre }}</h1>
                <p class="ciudad-hero-meta">
                    {{ $posts->total() }} {{ $posts->total() === 1 ? __('cities.publication') : __('cities.publications') }}
                    &nbsp;·&nbsp;
                    {{ $followersCount }} {{ $followersCount === 1 ? __('cities.follower') : __('cities.followers') }}
                </p>
                @auth
                    <form method="POST" action="{{ route('ciudades.follow', $ciudad) }}" style="margin-top:14px;">
                        @csrf
                        <button type="submit" class="btn-follow-city {{ $isFollowing ? 'following' : '' }}">
                            {{ $isFollowing ? __('cities.following_btn') : __('cities.follow_btn') }}
                        </button>
                    </form>
                @endauth
            </div>
        </div>

        {{-- DESTACADOS --}}
        @if($top3->isNotEmpty() && !request('categoria'))
        <section class="ciudad-section">
            <div class="ciudad-section-hd">
                <h2 class="ciudad-section-title">{{ __('cities.featured_title') }}</h2>
                <span class="ciudad-section-sub">{{ __('cities.featured_sub') }}</span>
            </div>
            <div class="city-top-grid">
                @foreach($top3 as $i => $post)
                    <a href="{{ route('posts.show', $post) }}" class="city-top-card city-top-rank-{{ $i + 1 }}">
                        <div class="city-top-img-wrap">
                            <img src="{{ asset($post->image) }}" alt="{{ $post->title }}">
                            <span class="city-top-rank">{{ ['🥇','🥈','🥉'][$i] }}</span>
                        </div>
                        <div class="city-top-body">
                            <span class="city-top-cat">{{ t_cat($post->category) }}</span>
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
               class="cat-pill {{ !request('categoria') ? 'active' : '' }}">{{ __('cities.all_categories') }}</a>
            @foreach($categorias as $cat)
                <a href="{{ route('ciudades.show', $ciudad) }}?categoria={{ urlencode($cat) }}"
                   class="cat-pill {{ request('categoria') === $cat ? 'active' : '' }}">{{ t_cat($cat) }}</a>
            @endforeach
        </div>

        {{-- MÁS LUGARES --}}
        <section class="ciudad-section">
            <div class="ciudad-section-hd">
                <h2 class="ciudad-section-title">
                    {{ request('categoria') ? t_cat(request('categoria')) : __('cities.more_places') . ' ' . $ciudad->nombre }}
                </h2>
            </div>

            @if($posts->isEmpty())
                <div class="empty-state">
                    <p>{{ __('cities.no_posts') }}</p>
                    @auth
                        <a href="{{ route('posts.create') }}" class="btn-nav" style="display:inline-block;margin-top:10px;">
                            {{ __('cities.publish_about') }} {{ $ciudad->nombre }}
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
                                    <div class="city-post-card-cat">{{ $post->place_name ?? t_cat($post->category) }}</div>
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

        @if($mapPosts->isNotEmpty())
        <div class="ciudad-sidebar-card">
            <div class="ciudad-sidebar-card-hd">{{ __('cities.map_title') }}</div>
            <div id="ciudad-map" class="ciudad-map"></div>
            <a href="{{ route('mapa') }}" target="_blank" class="ciudad-map-link">
                {{ __('cities.see_full_map') }}
            </a>
        </div>
        @endif

        @if($topCategorias->isNotEmpty())
        <div class="ciudad-sidebar-card">
            <div class="ciudad-sidebar-card-hd">{{ __('cities.top_categories') }}</div>
            <div class="ciudad-top-cats">
                @foreach($topCategorias as $cat)
                    <a href="{{ route('ciudades.show', $ciudad) }}?categoria={{ urlencode($cat->category) }}"
                       class="ciudad-top-cat-item {{ request('categoria') === $cat->category ? 'ciudad-top-cat-item--on' : '' }}">
                        <span class="ciudad-top-cat-name">{{ t_cat($cat->category) }}</span>
                        <span class="ciudad-top-cat-count">{{ $cat->total }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        @if($viajes->isNotEmpty())
        <div class="ciudad-sidebar-card">
            <div class="ciudad-sidebar-card-hd">✈️ Viajes organizados</div>
            <div style="display:flex;flex-direction:column;gap:12px;margin-top:4px;">
                @foreach($viajes as $viaje)
                    <a href="{{ route('viajes.show', $viaje) }}"
                       style="display:flex;gap:10px;align-items:flex-start;text-decoration:none;color:var(--text);">
                        @if($viaje->imagen)
                            <img src="{{ asset('storage/' . $viaje->imagen) }}"
                                 style="width:56px;height:56px;border-radius:10px;object-fit:cover;flex-shrink:0;" alt="">
                        @else
                            <div style="width:56px;height:56px;border-radius:10px;background:linear-gradient(135deg,var(--accent),#6366f1);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">✈️</div>
                        @endif
                        <div>
                            <div style="font-size:13px;font-weight:600;line-height:1.3;margin-bottom:3px;">{{ $viaje->titulo }}</div>
                            <div style="font-size:12px;color:var(--text-muted);">{{ $viaje->fecha_salida->format('d M Y') }} · {{ number_format($viaje->precio, 0, ',', '.') }} €</div>
                        </div>
                    </a>
                @endforeach
            </div>
            <a href="{{ route('viajes.index') }}"
               style="display:block;text-align:center;margin-top:14px;font-size:13px;color:var(--accent);text-decoration:none;font-weight:600;">
                Ver todos los viajes →
            </a>
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
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/attributions">CARTO</a>'
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
