@extends('layouts.app')

@section('contenido')

<style>
  body { background: linear-gradient(135deg, #0f172a 0%, #0c1a3a 50%, #0f172a 100%) !important; }
  .main-container { background: transparent !important; }
</style>

@php
$catDesc = [
    '🍽️ Restaurante'           => 'Comida y cocina local',
    '🍺 Bar & Copas'            => 'Bares y vida nocturna',
    '☕ Café'                    => 'Cafeterías y pastelerías',
    '🏛️ Monumento & Cultura'    => 'Historia y arquitectura',
    '🌿 Parque & Naturaleza'    => 'Parques y al aire libre',
    '🛍️ Tienda & Mercado'       => 'Mercados y comercios',
    '🏖️ Playa'                  => 'Playas y costas',
    '🎭 Ocio & Entretenimiento' => 'Cine y actividades',
    '🎉 Fiestas & Tradiciones'  => 'Eventos y celebraciones',
    '🏨 Alojamiento'            => 'Hoteles y hospedaje',
    '💡 Joya Oculta'            => 'Lugares secretos',
];
@endphp

<div style="width:100%; display:flex; justify-content:center; padding:0 16px 60px;">
<div class="create-form-wrap">

    <div class="create-form-header">
        <a href="{{ route('posts.show', $post) }}" class="create-form-back">&#8592;</a>
        <div>
            <h1 class="create-form-title">Editar publicación</h1>
            <p class="create-form-subtitle">Actualiza los detalles de tu publicación</p>
        </div>
    </div>

    <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data"
          x-data='{ "sel": {!! json_encode(old("category") ?? $post->category) !!} }'>
        @csrf
        @method('PUT')

        {{-- 1. Categoría --}}
        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">1</span>
                <span class="form-section-title">Categoría</span>
                <p class="form-section-sub">Cambia la categoría si es necesario.</p>
            </div>
            <input type="hidden" name="category" :value="sel">
            <div class="cat-cards-grid">
                @foreach(\App\Models\Post::CATEGORIES as $cat)
                    @php [$icon, $name] = array_pad(explode(' ', $cat, 2), 2, ''); @endphp
                    <button type="button" class="cat-card" data-cat="{{ $cat }}"
                            :class="{ 'cat-card--on': sel === $el.dataset.cat }"
                            @click="sel = $el.dataset.cat">
                        <span class="cat-card-icon">{{ $icon }}</span>
                        <span class="cat-card-name">{{ $name }}</span>
                        <span class="cat-card-desc">{{ $catDesc[$cat] ?? '' }}</span>
                    </button>
                @endforeach
            </div>
            @error('category') <span class="error" style="display:block;margin-top:8px;">{{ $message }}</span> @enderror
        </div>

        {{-- 2. Detalles --}}
        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">2</span>
                <span class="form-section-title">Detalles</span>
                <p class="form-section-sub">Título, ciudad y descripción de tu publicación.</p>
            </div>
            <div class="form-section-body">
                <div class="auth-row-2">
                    <div class="form-group">
                        <label for="title">Título</label>
                        <input type="text" name="title" id="title"
                               value="{{ old('title', $post->title) }}" required>
                        @error('title') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="ciudad_nombre">Ciudad</label>
                        <input type="text" name="ciudad_nombre" id="ciudad_nombre"
                               value="{{ old('ciudad_nombre', $post->ciudad->nombre ?? '') }}" required>
                        @error('ciudad_nombre') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label for="content">Descripción</label>
                    <textarea name="content" id="content" required>{{ old('content', $post->content) }}</textarea>
                    @error('content') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- 3. Fotos --}}
        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">3</span>
                <span class="form-section-title">Fotos</span>
                <p class="form-section-sub">Sube nuevas fotos para reemplazar las actuales (opcional · máx. 6).</p>
            </div>
            <div class="form-section-body">
                {{-- Fotos actuales --}}
                @php $allImgs = $post->allImages(); @endphp
                @if($allImgs)
                    <div class="auth-upload-previews" style="margin-bottom:16px;">
                        @foreach($allImgs as $i => $img)
                            <div class="auth-thumb {{ $i === 0 ? 'auth-thumb--cover' : '' }}">
                                <img src="{{ asset($img) }}" alt="">
                                @if($i === 0) <span class="auth-thumb-badge">portada</span> @endif
                            </div>
                        @endforeach
                    </div>
                @endif
                {{-- Nueva selección --}}
                <div class="auth-upload" x-data="{
                        count: 0,
                        busy: false,
                        async pick(e) {
                            const files = Array.from(e.target.files).slice(0, 6);
                            if (!files.length) return;
                            this.busy = true; this.count = 0;
                            const done = await Promise.all(files.map(f => compressImg(f)));
                            const dt = new DataTransfer();
                            done.forEach(f => dt.items.add(f));
                            this.$refs.fi.files = dt.files;
                            this.count = done.length;
                            this.busy = false;
                        }
                    }" @click="!busy && $refs.fi.click()">
                    <input x-ref="fi" type="file" name="images[]" accept="image/*" multiple
                           style="display:none" @change="pick($event)">
                    <template x-if="busy">
                        <div>
                            <div class="auth-upload-icon">⏳</div>
                            <p class="auth-upload-text">Optimizando imágenes...</p>
                        </div>
                    </template>
                    <template x-if="!busy && count === 0">
                        <div>
                            <div class="auth-upload-icon">📷</div>
                            <p class="auth-upload-text">Haz clic para seleccionar nuevas fotos</p>
                            <p class="auth-upload-hint">JPG o PNG · hasta 6 fotos</p>
                        </div>
                    </template>
                    <template x-if="!busy && count > 0">
                        <div>
                            <p class="auth-upload-text"
                               x-text="count + (count === 1 ? ' foto lista ✓' : ' fotos listas ✓')"></p>
                            <p class="auth-upload-hint">Haz clic para cambiar la selección</p>
                        </div>
                    </template>
                </div>
                @error('images') <span class="error">{{ $message }}</span> @enderror
                @error('images.*') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- 4. Mapa --}}
        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">4</span>
                <span class="form-section-title">Ubicación en el mapa <span class="auth-hint-label">(opcional)</span></span>
                <p class="form-section-sub">Ajusta la ubicación si es necesario.</p>
            </div>
            <div class="form-section-body">
                <div class="form-group" style="margin-bottom:12px;">
                    <label for="place_name">Nombre del lugar <span class="auth-hint-label">(opcional)</span></label>
                    <div class="map-search-wrap" style="margin-bottom:0;">
                        <input type="text" name="place_name" id="place_name" class="map-search-input"
                               value="{{ old('place_name', $post->place_name) }}"
                               placeholder="Ej: Catedral de Sevilla, Bar El Copo...">
                        <button type="button" class="map-search-btn" id="map-search-btn">Buscar</button>
                    </div>
                    @error('place_name') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div id="map-search-results" class="map-search-results"></div>
                <div id="picker-map" class="map-picker"></div>
                <input type="hidden" name="lat" id="lat" value="{{ old('lat', $post->lat) }}">
                <input type="hidden" name="lng" id="lng" value="{{ old('lng', $post->lng) }}">
                <p class="map-picker-hint" id="picker-hint">
                    {{ $post->lat ? '📍 ' . number_format($post->lat, 5) . ', ' . number_format($post->lng, 5) : 'Busca un lugar arriba o haz clic en el mapa' }}
                </p>
            </div>
        </div>

        {{-- 5. Características --}}
        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">5</span>
                <span class="form-section-title">Características del lugar <span class="auth-hint-label">(opcional)</span></span>
                <p class="form-section-sub">Selecciona las que mejor describen este sitio.</p>
            </div>
            <div class="form-section-body">
                <div class="tags-picker-grid">
                    @foreach(\App\Models\Post::TAGS as $tag)
                        <label class="tag-chip-pick">
                            <input type="checkbox" name="tags[]" value="{{ $tag }}"
                                   {{ in_array($tag, old('tags', $post->tags ?? [])) ? 'checked' : '' }}>
                            <span>{{ $tag }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <button type="submit" class="btn-primary" style="width:100%;padding:15px;font-size:16px;margin-top:8px;">
            Guardar cambios
        </button>
    </form>

    <p class="post-form-back" style="text-align:center;margin-top:20px;">
        <a href="{{ route('posts.show', $post) }}">← Volver al post</a>
    </p>

</div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const existingLat = {{ $post->lat ?? 'null' }};
    const existingLng = {{ $post->lng ?? 'null' }};
    const center = existingLat ? [existingLat, existingLng] : [20, 0];
    const zoom   = existingLat ? 10 : 2;
    const map = L.map('picker-map').setView(center, zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');
    const hint     = document.getElementById('picker-hint');
    let marker = existingLat
        ? L.marker([existingLat, existingLng], { draggable: true }).addTo(map)
        : null;

    function placeMarker(lat, lng) {
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
        hint.textContent = `📍 ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', function () {
                const p = marker.getLatLng();
                latInput.value = p.lat.toFixed(7);
                lngInput.value = p.lng.toFixed(7);
                hint.textContent = `📍 ${p.lat.toFixed(5)}, ${p.lng.toFixed(5)}`;
            });
        }
    }

    if (marker) {
        marker.on('dragend', function () {
            const p = marker.getLatLng();
            latInput.value = p.lat.toFixed(7);
            lngInput.value = p.lng.toFixed(7);
            hint.textContent = `📍 ${p.lat.toFixed(5)}, ${p.lng.toFixed(5)}`;
        });
    }

    map.on('click', function (e) {
        placeMarker(e.latlng.lat, e.latlng.lng);
    });

    // Geocoding con Nominatim
    const searchInput   = document.getElementById('place_name');
    const searchBtn     = document.getElementById('map-search-btn');
    const searchResults = document.getElementById('map-search-results');
    let geoResults = [];

    async function geocode(q) {
        if (!q) {
            const ciudad = document.getElementById('ciudad_nombre')?.value.trim() || '';
            const place  = searchInput.value.trim();
            q = ciudad ? `${place}, ${ciudad}` : place;
        }
        if (!q) return;
        searchBtn.textContent = '...';
        searchResults.style.display = 'none';
        try {
            const res = await fetch(
                `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=5&accept-language=es`,
                { headers: { 'Accept-Language': 'es' } }
            );
            const data = await res.json();
            searchBtn.textContent = 'Buscar';
            if (!data.length) {
                searchResults.innerHTML = '<div class="map-search-empty">Sin resultados — prueba con otro nombre</div>';
                searchResults.style.display = 'block';
                return;
            }
            if (data.length === 1) {
                applyResult(data[0]);
                return;
            }
            geoResults = data;
            searchResults.innerHTML = data.map((r, i) =>
                `<div class="map-search-item" data-i="${i}">${r.display_name}</div>`
            ).join('');
            searchResults.style.display = 'block';
        } catch {
            searchBtn.textContent = 'Buscar';
        }
    }

    function applyResult(r) {
        const lat = parseFloat(r.lat), lng = parseFloat(r.lon);
        map.flyTo([lat, lng], 16);
        placeMarker(lat, lng);
        searchResults.style.display = 'none';
        searchInput.value = r.display_name.split(',')[0];
    }

    searchResults.addEventListener('click', function (e) {
        const item = e.target.closest('.map-search-item');
        if (item) applyResult(geoResults[parseInt(item.dataset.i)]);
    });
    searchBtn.addEventListener('click', () => geocode());
    searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); geocode(); } });
    searchInput.addEventListener('blur', () => { if (searchInput.value.trim()) geocode(); });
    document.addEventListener('click', e => {
        if (!searchResults.contains(e.target) && e.target !== searchInput && e.target !== searchBtn)
            searchResults.style.display = 'none';
    });
})();
</script>
@endpush

@endsection
