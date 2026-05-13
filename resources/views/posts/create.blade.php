@extends('layouts.app')

@section('contenido')

<div class="form-page">
    <h1 class="form-page-title">✈️ Crear nuevo post</h1>

    <div class="form-card">
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}"
                       placeholder="Ej: Una semana en Tokio" required>
                @error('title') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="category">Categoría</label>
                <select name="category" id="category" required>
                    <option value="">— Elige una categoría —</option>
                    @foreach(\App\Models\Post::CATEGORIES as $cat)
                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                @error('category') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="ciudad_nombre">Ciudad</label>
                <input type="text" name="ciudad_nombre" id="ciudad_nombre" value="{{ old('ciudad_nombre') }}"
                       placeholder="París, Roma, Tokio..." required>
                @error('ciudad_nombre') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="content">Descripción</label>
                <textarea name="content" id="content" rows="5"
                          placeholder="Cuéntanos tu experiencia..." required>{{ old('content') }}</textarea>
                @error('content') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="images">Fotos <span class="label-optional">(máx. 6, la primera será la portada)</span></label>
                <input type="file" name="images[]" id="images" accept="image/*" multiple required>
                @error('images') <span class="error">{{ $message }}</span> @enderror
                @error('images.*') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Ubicación en el mapa <span class="label-optional">(opcional — haz clic para marcar)</span></label>
                <div id="picker-map" class="map-picker"></div>
                <input type="hidden" name="lat" id="lat">
                <input type="hidden" name="lng" id="lng">
                <p class="map-picker-hint" id="picker-hint">Sin ubicación seleccionada</p>
            </div>

            <button type="submit" class="btn-primary">Publicar post</button>
        </form>
    </div>

    <div class="form-back-link">
        <a href="{{ route('posts.index') }}">← Volver al feed</a>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const map = L.map('picker-map').setView([20, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let marker = null;
    const latInput  = document.getElementById('lat');
    const lngInput  = document.getElementById('lng');
    const hint      = document.getElementById('picker-hint');

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
