@extends('layouts.app')

@section('contenido')

<div class="section" style="padding-top:32px;">
    <div class="section-header">
        <h2 class="section-title">#{{ $hashtag->name }}</h2>
        <p style="font-size:13px;color:var(--text-muted);">{{ $posts->total() }} publicaciones</p>
    </div>

    @if($posts->isEmpty())
        <div class="empty-state"><p>No hay publicaciones con este hashtag.</p></div>
    @else
        <ul class="posts-grid">
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
        <div class="pagination">{{ $posts->links() }}</div>
    @endif
</div>

@endsection
