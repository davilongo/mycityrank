@extends('layouts.app')

@section('title', 'Tu feed — XploreFree')

@section('contenido')

<div class="feed-page">

    <div class="feed-layout">

        {{-- ===== MAIN FEED ===== --}}
        <div class="feed-main">

            <div class="feed-hd">
                <h1 class="feed-title">
                    @if($hasSources)
                        🏠 Tu feed
                    @else
                        🔍 Descubre lugares
                    @endif
                </h1>
                @if(!$hasSources)
                    <p class="feed-subtitle">Sigue a usuarios o ciudades para ver su contenido aquí.</p>
                @endif
            </div>

            @if($posts->isEmpty())
                <div class="empty-state">
                    <p>Las personas y ciudades que sigues aún no han publicado nada.</p>
                    <a href="{{ route('posts.index') }}" class="btn-nav" style="display:inline-block;margin-top:10px;">Explorar posts</a>
                </div>
            @else
                <ul class="feed-grid">
                    @foreach($posts as $post)
                        <li>
                            <a href="{{ route('posts.show', $post) }}" class="city-post-card">
                                <img src="{{ asset($post->image) }}" alt="{{ $post->title }}" loading="lazy">
                                <div class="city-post-card-overlay">
                                    <div class="city-post-card-cat">
                                        @if($post->feed_source === 'user')
                                            👤 {{ $post->user->name ?? 'Anónimo' }}
                                        @elseif($post->feed_source === 'ciudad')
                                            📍 {{ $post->place_name ?? $post->ciudad->nombre ?? '' }}
                                        @else
                                            🔥 Tendencia
                                        @endif
                                    </div>
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

        </div>

        {{-- ===== SIDEBAR ===== --}}
        <div class="feed-sidebar">

            {{-- Followed cities --}}
            @if($followedCities->isNotEmpty())
            <div class="feed-sidebar-card">
                <div class="feed-sidebar-hd">📍 Ciudades que sigues</div>
                <div class="feed-cities-list">
                    @foreach($followedCities as $city)
                        <a href="{{ route('ciudades.show', $city) }}" class="feed-city-item">
                            <span class="feed-city-name">{{ $city->nombre }}</span>
                            <span class="feed-city-count">{{ $city->posts_count }} posts</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Trending this week --}}
            @if($trending->isNotEmpty())
            <div class="feed-sidebar-card">
                <div class="feed-sidebar-hd">🔥 Tendencias esta semana</div>
                <div class="feed-trending-list">
                    @foreach($trending as $post)
                        <a href="{{ route('posts.show', $post) }}" class="feed-trending-item">
                            <img src="{{ asset($post->image) }}" alt="{{ $post->title }}" loading="lazy">
                            <div class="feed-trending-body">
                                <div class="feed-trending-title">{{ Str::limit($post->title, 45) }}</div>
                                <div class="feed-trending-stats">❤️ {{ $post->likes_count }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Invite to explore --}}
            @if(!$hasSources)
            <div class="feed-sidebar-card feed-sidebar-card--cta">
                <div class="feed-sidebar-hd">✨ Personaliza tu feed</div>
                <p class="feed-cta-text">Sigue usuarios o ciudades y verás solo lo que te interesa.</p>
                <a href="{{ route('posts.index') }}" class="btn-nav" style="display:block;text-align:center;margin-top:10px;">
                    Explorar y seguir
                </a>
            </div>
            @endif

        </div>

    </div>

</div>

@endsection
