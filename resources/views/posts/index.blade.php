@extends('layouts.app')

@section('contenido')

    <a href="{{ route('posts.create') }}" class="button">Nuevo post</a>

    <ul class="posts-grid">
        @foreach ($posts as $post)
            <li class="post-card">
                <a href="{{ route('posts.show', $post) }}">
                    <img src="{{ asset($post->image) }}" alt="Imagen del post" />
                    <div class="post-card-title">{{ $post->title }}</div>
                </a>
            </li>
        @endforeach
    </ul>

    <!-- Paginación -->
    <div class="pagination">
        {{ $posts->links() }}
    </div>
@endsection
