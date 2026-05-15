@extends('layouts.app')

@section('contenido')

<style>
  body { background: linear-gradient(135deg, #0f172a 0%, #0c1a3a 50%, #0f172a 100%) !important; }
  .main-container { background: transparent !important; }
</style>

@php $allImgs = $post->allImages(); @endphp

<div class="pd-page">

    {{-- Nav bar --}}
    <div class="pd-back-bar">
        <a href="{{ route('posts.index') }}" class="btn-back">← Volver</a>
    </div>

    <div class="pd-wrap">

        {{-- ===== LEFT COLUMN ===== --}}
        <div class="pd-main">

            {{-- Gallery --}}
            <div class="pd-gallery"
                 x-data='{ idx: 0, imgs: @json($allImgs) }'>
                <div class="pd-gallery-main">
                    <span class="pd-counter">
                        📷 <span x-text="(idx + 1) + '/' + imgs.length"></span>
                    </span>
                    <img :src="imgs[idx]" alt="{{ $post->title }}">
                    <template x-if="imgs.length > 1">
                        <div>
                            <button class="pd-nav-btn pd-nav-prev"
                                    @click="idx = (idx - 1 + imgs.length) % imgs.length">&#8249;</button>
                            <button class="pd-nav-btn pd-nav-next"
                                    @click="idx = (idx + 1) % imgs.length">&#8250;</button>
                        </div>
                    </template>
                </div>
                <template x-if="imgs.length > 1">
                    <div class="pd-thumbs">
                        <template x-for="(src, i) in imgs" :key="i">
                            <div class="pd-thumb" :class="{ 'pd-thumb--on': i === idx }" @click="idx = i">
                                <img :src="src" alt="">
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Content card --}}
            <div class="pd-content">
                <h1 class="pd-title">{{ $post->title }}</h1>
                <div class="pd-meta">
                    @if($post->ciudad)
                        <a href="{{ route('ciudades.show', $post->ciudad) }}" class="pd-badge">
                            📍 {{ $post->ciudad->nombre }}
                        </a>
                    @endif
                    <a href="{{ route('posts.index', ['categoria' => $post->category]) }}" class="pd-badge">
                        {{ $post->category }}
                    </a>
                    <span class="pd-badge pd-badge-neutral">🕐 {{ $post->created_at->diffForHumans() }}</span>
                </div>
                <div class="pd-body">{!! preg_replace_callback('/#(\w+)/u', fn($m) =>
                    '<a href="' . route('hashtag.show', $m[1]) . '" class="hashtag-link">#' . e($m[1]) . '</a>',
                    nl2br(e($post->content))
                ) !!}</div>
                @if($post->hashtags->isNotEmpty())
                    <div class="hashtag-list" style="margin-top:14px;">
                        @foreach($post->hashtags as $tag)
                            <a href="{{ route('hashtag.show', $tag->name) }}" class="hashtag-pill">#{{ $tag->name }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Actions bar --}}
            <div class="pd-actions-bar">
                <form style="display:contents;" action="{{ route('posts.like', $post) }}" method="POST">
                    @csrf
                    @auth
                        @php $liked = $post->likes->contains('user_id', Auth::id()); @endphp
                        <button type="submit" class="pd-action-btn {{ $liked ? 'pd-action-btn--like-on' : '' }}">
                            {{ $liked ? '❤️' : '🤍' }} {{ $post->likes->count() }} Me gusta
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="pd-action-btn">🤍 {{ $post->likes->count() }} Me gusta</a>
                    @endauth
                </form>

                <span class="pd-action-btn" style="cursor:default;">
                    💬 {{ $post->comments->count() }} Comentarios
                </span>

                <div class="pd-action-sep"></div>

                @auth
                    <form style="display:contents;" action="{{ route('posts.bookmark', $post) }}" method="POST">
                        @csrf
                        @php $saved = Auth::user()->bookmarks()->where('post_id', $post->id)->exists(); @endphp
                        <button type="submit" class="pd-action-btn" title="{{ $saved ? 'Quitar de guardados' : 'Guardar' }}">
                            {{ $saved ? '🔖' : '🏷️' }} {{ $saved ? 'Guardado' : 'Guardar' }}
                        </button>
                    </form>
                @endauth
            </div>

            {{-- Comments --}}
            <div class="pd-comments">
                <div class="pd-comments-title">Comentarios ({{ $post->comments->count() }})</div>

                @auth
                    <form class="pd-comment-form" method="POST" action="{{ route('posts.comment', $post) }}">
                        @csrf
                        <div class="pd-comment-avatar">{{ mb_substr(Auth::user()->name, 0, 1) }}</div>
                        <textarea name="body" placeholder="Escribe un comentario..." required rows="1"
                                  oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"></textarea>
                        <button type="submit">Publicar</button>
                    </form>
                @else
                    <div style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">
                        <a href="{{ route('login') }}" style="color:var(--accent);font-weight:600;">Inicia sesión</a> para comentar.
                    </div>
                @endauth

                @forelse($post->comments as $comment)
                    <div class="pd-comment-item">
                        <div class="pd-comment-av">
                            @if($comment->user->avatar ?? false)
                                <img src="{{ asset($comment->user->avatar) }}" alt="">
                            @else
                                {{ mb_substr($comment->user->name ?? 'A', 0, 1) }}
                            @endif
                        </div>
                        <div class="pd-comment-body">
                            <span class="pd-comment-name">{{ $comment->user->name ?? 'Anónimo' }}</span>
                            <span class="pd-comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                            <div class="pd-comment-text">{{ $comment->body }}</div>
                        </div>
                    </div>
                @empty
                    <p class="pd-no-comments">Aún no hay comentarios. ¡Sé el primero!</p>
                @endforelse
            </div>

        </div>{{-- /pd-main --}}

        {{-- ===== RIGHT SIDEBAR ===== --}}
        <div class="pd-sidebar">

            {{-- User card --}}
            <div class="pd-user-card">
                <div class="pd-user-row">
                    <a href="{{ route('users.show', $post->user) }}" class="pd-user-av">
                        @if($post->user->avatar ?? false)
                            <img src="{{ asset($post->user->avatar) }}" alt="">
                        @else
                            {{ mb_substr($post->user->name ?? 'A', 0, 1) }}
                        @endif
                    </a>
                    <div class="pd-user-info">
                        <a href="{{ route('users.show', $post->user) }}" class="pd-user-name">{{ $post->user->name ?? 'Anónimo' }}</a>
                        <div class="pd-user-since">Miembro desde {{ $post->user->created_at->translatedFormat('F Y') }}</div>
                    </div>
                    @auth
                        @if(Auth::id() !== $post->user_id)
                            <a href="{{ route('users.show', $post->user) }}" class="pd-follow-btn">Seguir</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="pd-follow-btn">Seguir</a>
                    @endauth
                </div>

                @auth
                    @if(Auth::id() === $post->user_id || Auth::user()->isAdmin())
                        <div class="pd-owner-actions">
                            <a href="{{ route('posts.edit', $post) }}" class="pd-edit-btn">✏️ Editar</a>
                            <form method="POST" action="{{ route('posts.destroy', $post) }}" style="display:contents;"
                                  onsubmit="return confirm('¿Seguro que quieres borrar este post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="pd-delete-btn">🗑️ Borrar</button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>

            {{-- Map card --}}
            @if($post->lat && $post->lng)
                <div class="pd-map-card">
                    <div class="pd-map-header">📍 Ubicación</div>
                    @if($post->place_name)
                        <div class="pd-place-label">📌 {{ $post->place_name }}</div>
                    @elseif($post->ciudad)
                        <div class="pd-place-label">📌 {{ $post->ciudad->nombre }}</div>
                    @endif
                    <div id="post-map" class="pd-map-inner"></div>
                    <div class="pd-map-btns">
                        <a href="https://www.openstreetmap.org/?mlat={{ $post->lat }}&mlon={{ $post->lng }}&zoom=16"
                           target="_blank" rel="noopener" class="pd-map-btn pd-map-btn--outline">
                            ⛶ Ver mapa
                        </a>
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $post->lat }},{{ $post->lng }}"
                           target="_blank" rel="noopener" class="pd-map-btn pd-map-btn--primary">
                            🧭 Cómo llegar
                        </a>
                    </div>
                </div>
            @endif

            {{-- Info card (tags) --}}
            @if($post->tags && count($post->tags) > 0)
                <div class="pd-info-card">
                    <div class="pd-info-header">ℹ️ Información del lugar</div>
                    @if($post->ciudad)
                        <div class="pd-info-city">📍 {{ $post->ciudad->nombre }}{{ $post->place_name ? ', ' . $post->place_name : '' }}</div>
                    @endif
                    <div class="pd-info-tags">
                        @foreach($post->tags as $tag)
                            @php [$icon, $label] = array_pad(explode(' ', $tag, 2), 2, ''); @endphp
                            <div class="pd-info-tag">
                                <div class="pd-info-tag-icon">{{ $icon }}</div>
                                <span>{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>{{-- /pd-sidebar --}}

    </div>{{-- /pd-wrap --}}
</div>{{-- /pd-page --}}

@if($post->lat && $post->lng)
@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const map = L.map('post-map', { zoomControl: false, scrollWheelZoom: false })
        .setView([{{ $post->lat }}, {{ $post->lng }}], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);
    const marker = L.marker([{{ $post->lat }}, {{ $post->lng }}]).addTo(map);
    @if($post->place_name)
        marker.bindPopup('{{ e($post->place_name) }}').openPopup();
    @elseif($post->ciudad)
        marker.bindPopup('{{ e($post->ciudad->nombre ?? '') }}').openPopup();
    @endif
})();
</script>
@endpush
@endif

@endsection
