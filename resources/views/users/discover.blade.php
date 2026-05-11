@extends('layouts.app')

@section('contenido')
<div class="discover-page">

    <div class="discover-header">
        <div>
            <h2 class="discover-title">👥 Descubrir gente</h2>
            <p class="discover-subtitle">
                @if($myCityIds->isNotEmpty())
                    Personas que publican en las mismas ciudades que tú
                @else
                    Los usuarios más activos de la comunidad
                @endif
            </p>
        </div>
    </div>

    @if($suggested->isEmpty())
        <div class="empty-state">
            <p>No hay sugerencias por ahora. ¡Sube tu primer post para que podamos sugerirte gente!</p>
            <a href="{{ route('posts.create') }}" class="btn-nav" style="display:inline-block;margin-top:12px;">+ Crear post</a>
        </div>
    @else
        <div class="discover-grid">
            @foreach($suggested as $person)
                <div class="discover-card">
                    <a href="{{ route('users.show', $person) }}" class="discover-avatar-link">
                        @if($person->avatar)
                            <img src="{{ asset($person->avatar) }}" class="discover-avatar-img" alt="">
                        @else
                            <span class="discover-avatar-initial">{{ mb_strtoupper(mb_substr($person->name, 0, 1)) }}</span>
                        @endif
                    </a>
                    <div class="discover-info">
                        <a href="{{ route('users.show', $person) }}" class="discover-name">{{ $person->name }}</a>
                        <div class="discover-stats">
                            <span>📸 {{ $person->posts_count }} posts</span>
                            <span>👥 {{ $person->followers_count }} seguidores</span>
                        </div>
                        @if(isset($person->shared_posts_count) && $person->shared_posts_count > 0)
                            <span class="discover-shared">
                                {{ $person->shared_posts_count }} {{ $person->shared_posts_count === 1 ? 'post' : 'posts' }} en ciudades comunes
                            </span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('users.follow', $person) }}">
                        @csrf
                        <button type="submit" class="btn-follow-discover">Seguir</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
