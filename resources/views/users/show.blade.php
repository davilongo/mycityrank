@extends('layouts.app')

@section('title', $user->name . ' — XploreFree')
@section('meta_description', $user->bio ?? ($user->name . ' comparte lugares increíbles en XploreFree.'))
@if($posts->first()?->image)
@section('og_image', url($posts->first()->image))
@endif

@section('contenido')

<div class="profile-page">

    {{-- ===== CABECERA DE PERFIL ===== --}}
    <div class="profile-header">
        <div class="profile-avatar-wrap">
            @if($user->avatar)
                <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="profile-avatar">
            @else
                <div class="profile-avatar-placeholder">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</div>
            @endif
        </div>
        <div class="profile-info">
            <div class="profile-name-row">
                <h1 class="profile-name">{{ $user->name }}</h1>
                @if($user->isAdmin())
                    <span class="badge-admin">ADMIN</span>
                @endif
                @php $rank = $user->rankBadge(); @endphp
                <span class="rank-badge rank-badge--{{ $rank['tier'] }}">{{ $rank['emoji'] }} {{ $rank['label'] }}</span>
                @auth
                    @if(Auth::id() === $user->id)
                        <a href="{{ route('profile.edit') }}" class="btn-ghost" style="margin-left:8px;">Editar perfil</a>
                    @else
                        <form method="POST" action="{{ route('users.follow', $user) }}" style="margin-left:8px;">
                            @csrf
                            <button type="submit" class="{{ $isFollowing ? 'btn-ghost' : 'btn-nav' }}">
                                {{ $isFollowing ? 'Siguiendo' : 'Seguir' }}
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
            @if($user->bio)
                <p class="profile-bio">{{ $user->bio }}</p>
            @endif
            <div class="profile-stats">
                <span><strong>{{ $posts->total() }}</strong> publicaciones</span>
                <span><strong>{{ $followersCount }}</strong> seguidores</span>
                <span><strong>{{ $followingCount }}</strong> siguiendo</span>
            </div>
            @if($expertCities->isNotEmpty())
                <div class="profile-expertise">
                    @foreach($expertCities as $item)
                        <span class="expertise-badge">📍 Experto en {{ $item['ciudad']->nombre }} <span class="expertise-count">{{ $item['total'] }} posts</span></span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ===== GRID DE POSTS ===== --}}
    @if($posts->isEmpty())
        <div class="empty-state">
            <p>{{ $user->name }} aún no ha publicado nada.</p>
        </div>
    @else
        <ul class="city-posts-grid">
            @foreach($posts as $post)
                <li>
                    <a href="{{ route('posts.show', $post) }}" class="city-post-card">
                        <img src="{{ asset($post->image) }}" alt="{{ $post->title }}" loading="lazy">
                        <div class="city-post-card-overlay">
                            @if($post->place_name || $post->ciudad)
                                <div class="city-post-card-cat">📍 {{ $post->place_name ?? $post->ciudad->nombre }}</div>
                            @endif
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

@endsection
