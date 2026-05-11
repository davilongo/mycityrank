@extends('layouts.app')

@section('contenido')
<div class="mapa-page">
    <div class="mapa-header">
        <h2 class="mapa-title">🗺️ Mapa de posts</h2>
        <p class="mapa-subtitle">{{ $posts->count() }} {{ $posts->count() === 1 ? 'lugar marcado' : 'lugares marcados' }}</p>
    </div>
    <div id="world-map" class="world-map"></div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const map = L.map('world-map').setView([20, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const posts = @json($posts->map(fn($p) => [
        'lat'   => $p->lat,
        'lng'   => $p->lng,
        'title' => $p->title,
        'url'   => route('posts.show', $p),
        'image' => asset($p->image),
        'user'  => $p->user->name ?? '',
        'city'  => $p->ciudad->nombre ?? '',
    ]));

    posts.forEach(function (p) {
        const popup = `
            <div class="map-popup">
                <img src="${p.image}" alt="${p.title}">
                <div class="map-popup-body">
                    <a href="${p.url}" class="map-popup-title">${p.title}</a>
                    <span class="map-popup-meta">📍 ${p.city} · ${p.user}</span>
                </div>
            </div>`;
        L.marker([p.lat, p.lng])
            .addTo(map)
            .bindPopup(popup, { maxWidth: 220 });
    });
})();
</script>
@endpush
@endsection
