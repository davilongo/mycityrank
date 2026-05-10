@extends('layouts.app')

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
                @auth
                    @if(Auth::id() === $user->id)
                        <a href="{{ route('profile.edit') }}" class="btn-ghost" style="margin-left:8px;">Editar perfil</a>
                    @endif
                @endauth
            </div>
            @if($user->bio)
                <p class="profile-bio">{{ $user->bio }}</p>
            @endif
            <p class="profile-stats">{{ $posts->total() }} {{ $posts->total() === 1 ? 'publicación' : 'publicaciones' }}</p>
        </div>
    </div>

    {{-- ===== GRID DE POSTS ===== --}}
    @if($posts->isEmpty())
        <div class="empty-state">
            <p>{{ $user->name }} aún no ha publicado nada.</p>
        </div>
    @else
        <ul class="posts-grid profile-grid">
            @foreach($posts as $post)
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
                            <span class="card-date">{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="pagination">{{ $posts->links() }}</div>
    @endif

</div>

@endsection
