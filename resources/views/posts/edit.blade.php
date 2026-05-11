@extends('layouts.app')

@section('contenido')

<div class="form-page">
    <h1 class="form-page-title">✏️ Editar post</h1>

    <div class="form-card">
        <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" name="title" id="title"
                       value="{{ old('title', $post->title) }}" required>
                @error('title') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="slug">Slug (URL)</label>
                <input type="text" name="slug" id="slug"
                       value="{{ old('slug', $post->slug) }}" required>
                @error('slug') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="category">Categoría</label>
                <select name="category" id="category" required>
                    <option value="">— Elige una categoría —</option>
                    @foreach(\App\Models\Post::CATEGORIES as $cat)
                        <option value="{{ $cat }}" {{ old('category', $post->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                @error('category') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="ciudad_nombre">Ciudad</label>
                <input type="text" name="ciudad_nombre" id="ciudad_nombre"
                       value="{{ old('ciudad_nombre', $post->ciudad->nombre ?? '') }}" required>
                @error('ciudad_nombre') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="content">Descripción</label>
                <textarea name="content" id="content" rows="5" required>{{ old('content', $post->content) }}</textarea>
                @error('content') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Ubicación en el mapa <span class="label-optional">(opcional — haz clic para marcar)</span></label>
                <div id="picker-map" class="map-picker"></div>
                <input type="hidden" name="lat" id="lat" value="{{ old('lat', $post->lat) }}">
                <input type="hidden" name="lng" id="lng" value="{{ old('lng', $post->lng) }}">
                <p class="map-picker-hint" id="picker-hint">
                    {{ $post->lat ? '📍 ' . number_format($post->lat, 5) . ', ' . number_format($post->lng, 5) : 'Sin ubicación seleccionada' }}
                </p>
            </div>

            <div class="form-group">
                <label>Nueva imagen (opcional)</label>
                @if($post->image)
                    <img src="{{ asset($post->image) }}" alt="Imagen actual"
                         style="width:100%; height:160px; object-fit:cover; border-radius:8px; margin-bottom:10px;">
                @endif
                <input type="file" name="image" id="image" accept="image/*">
                @error('image') <span class="error">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn-primary">Guardar cambios</button>
        </form>
    </div>

    <div class="form-back-link">
        <a href="{{ route('posts.show', $post) }}">← Volver al post</a>
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

    if (marker) {
        marker.on('dragend', function () {
            const p = marker.getLatLng();
            latInput.value = p.lat.toFixed(7);
            lngInput.value = p.lng.toFixed(7);
            hint.textContent = `📍 ${p.lat.toFixed(5)}, ${p.lng.toFixed(5)}`;
        });
    }

    map.on('click', function (e) {
        const { lat, lng } = e.latlng;
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
        hint.textContent = `📍 ${lat.toFixed(5)}, ${lng.toFixed(5)}`;

        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng, { draggable: true }).addTo(map);
            marker.on('dragend', function () {
                const p = marker.getLatLng();
                latInput.value = p.lat.toFixed(7);
                lngInput.value = p.lng.toFixed(7);
                hint.textContent = `📍 ${p.lat.toFixed(5)}, ${p.lng.toFixed(5)}`;
            });
        }
    });
})();
</script>
@endpush

@endsection
