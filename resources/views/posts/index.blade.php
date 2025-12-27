@extends('layouts.app')

@section('contenido')

    <a href="{{ route('posts.create') }}" class="button">Nuevo post</a>

    <ul class="posts-grid">
    @foreach ($posts as $post)
        <li class="post-card">
            <a href="{{ route('posts.show', $post) }}">
                <img src="{{ asset($post->image) }}" alt="{{ $post->title }}" />
                
                <!-- Overlay hover -->
                <div class="post-overlay">
                    <h3>{{ Str::limit($post->title, 40) }}</h3>
                    <p style="font-size: 0.9rem; opacity: 0.9;">{{ $post->ciudad->nombre ?? 'Explora' }}</p>
                </div>
            </a>
            
            <!-- Stats sociales -->
            <div class="post-meta">
                <div class="post-stats">
                    <form action="{{ route('posts.like', $post) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="post-stat" style="background:none;border:none;padding:0;cursor:pointer;">
                            ❤️ {{ $post->likes->count() }}
                        </button>
                    </form>

                    <span class="post-stat">💬 5</span>
                    <span class="post-stat">👁️ 1.2k</span>
                </div>
                <div class="post-user">
                    {{ $post->user->name ?? 'Usuario' }} • {{ $post->created_at->diffForHumans() }}
                </div>
            </div>
        </li>
    @endforeach
</ul>


    <!-- Paginación -->
    <div class="pagination">
        {{ $posts->links() }}
    </div>
@endsection
