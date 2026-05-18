@extends('layouts.app')

@section('title', 'Mapa — XploreFree')

@section('contenido')

<style>
  body { background: #0f172a !important; }
  .main-container { padding: 0 !important; background: transparent !important; }
</style>

<div class="map-page">

    {{-- Stats overlay --}}
    <div class="map-stats-overlay">
        <div class="map-stats-badge">
            🗺️ <strong>{{ $total }}</strong> {{ $total === 1 ? 'lugar' : 'lugares' }}
            @if($ciudades > 0)
                · <strong>{{ $ciudades }}</strong> {{ $ciudades === 1 ? 'ciudad' : 'ciudades' }}
            @endif
        </div>
        <a href="{{ route('posts.index') }}" class="map-back-btn">← Explorar</a>
    </div>

    {{-- Map container --}}
    <div id="world-map" class="world-map-full"></div>

</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<style>
  /* Cluster icons */
  .marker-cluster-small,
  .marker-cluster-medium,
  .marker-cluster-large {
    background-clip: padding-box;
  }
  .marker-cluster-small div  { background-color: rgba(14,165,233,.8); }
  .marker-cluster-medium div { background-color: rgba(99,102,241,.85); }
  .marker-cluster-large div  { background-color: rgba(244,63,94,.85); }
  .marker-cluster span {
    color: #fff;
    font-weight: 700;
    font-size: 13px;
    line-height: 30px;
  }
  /* Popup */
  .leaflet-popup-content-wrapper {
    background: #1e293b;
    border: 1px solid #334155;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,.5);
    padding: 0;
    overflow: hidden;
  }
  .leaflet-popup-tip { background: #1e293b; }
  .leaflet-popup-content { margin: 0; width: 220px !important; }
  .map-popup img {
    width: 100%;
    height: 130px;
    object-fit: cover;
    display: block;
  }
  .map-popup-body { padding: 10px 12px 12px; }
  .map-popup-title {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #f1f5f9;
    text-decoration: none;
    margin-bottom: 4px;
    line-height: 1.3;
  }
  .map-popup-title:hover { color: #0ea5e9; }
  .map-popup-place {
    font-size: 11px;
    color: #94a3b8;
    margin-bottom: 6px;
  }
  .map-popup-cat {
    display: inline-block;
    background: rgba(14,165,233,.15);
    color: #38bdf8;
    border-radius: 6px;
    font-size: 11px;
    padding: 2px 7px;
  }
  .leaflet-popup-close-button { color: #94a3b8 !important; font-size: 18px !important; top: 6px !important; right: 8px !important; }
</style>
<script>
(function () {
    const map = L.map('world-map', { zoomControl: true, scrollWheelZoom: true });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/attributions">CARTO</a>'
    }).addTo(map);

    @php
        $mapData = $posts->map(fn($p) => [
            'lat'      => $p->lat,
            'lng'      => $p->lng,
            'title'    => $p->title,
            'url'      => route('posts.show', $p),
            'image'    => asset($p->image),
            'place'    => $p->place_name ?? ($p->ciudad->nombre ?? ''),
            'category' => $p->category ?? '',
        ]);
    @endphp
    const posts = @json($mapData);

    const clusters = L.markerClusterGroup({
        maxClusterRadius: 60,
        showCoverageOnHover: false,
        iconCreateFunction: function (cluster) {
            const count = cluster.getChildCount();
            const size  = count < 10 ? 'small' : count < 50 ? 'medium' : 'large';
            return L.divIcon({
                html: '<div><span>' + count + '</span></div>',
                className: 'marker-cluster marker-cluster-' + size,
                iconSize: L.point(40, 40)
            });
        }
    });

    posts.forEach(function (p) {
        const popup = `
            <div class="map-popup">
                <img src="${p.image}" alt="${p.title}">
                <div class="map-popup-body">
                    <a href="${p.url}" class="map-popup-title">${p.title}</a>
                    ${p.place ? `<div class="map-popup-place">📍 ${p.place}</div>` : ''}
                    ${p.category ? `<span class="map-popup-cat">${p.category}</span>` : ''}
                </div>
            </div>`;
        clusters.addLayer(
            L.marker([p.lat, p.lng]).bindPopup(popup, { maxWidth: 240 })
        );
    });

    map.addLayer(clusters);

    if (posts.length > 0) {
        map.fitBounds(clusters.getBounds(), { padding: [40, 40], maxZoom: 13 });
    } else {
        map.setView([37.5, -4.0], 6); // Andalucía por defecto
    }
})();
</script>
@endpush

@endsection
