@extends('layouts.app')

@section('contenido')
<div class="discover-page">

    <div class="discover-header">
        <div>
            <h2 class="discover-title">{{ __('users.discover_title') }}</h2>
            <p class="discover-subtitle">
                @if($myCityIds->isNotEmpty())
                    {{ __('users.from_same_cities') }}
                @else
                    {{ __('users.most_active') }}
                @endif
            </p>
        </div>
    </div>

    @if($suggested->isEmpty())
        <div class="empty-state">
            <p>{{ __('users.no_suggestions') }}</p>
            <a href="{{ route('posts.create') }}" class="btn-nav" style="display:inline-block;margin-top:12px;">{{ __('users.create_post_btn') }}</a>
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
                            <span>👥 {{ $person->followers_count }} {{ __('users.seguidores_label') }}</span>
                        </div>
                        @if(isset($person->shared_posts_count) && $person->shared_posts_count > 0)
                            <span class="discover-shared">
                                {{ $person->shared_posts_count }} {{ $person->shared_posts_count === 1 ? __('users.posts_in_common_single') : __('users.posts_in_common_plural') }}
                            </span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('users.follow', $person) }}">
                        @csrf
                        <button type="submit" class="btn-follow-discover">{{ __('users.follow_btn') }}</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
