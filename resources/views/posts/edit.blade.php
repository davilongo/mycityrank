@extends('layouts.app')

@section('contenido')

<style>
  body { background: linear-gradient(135deg, #0f172a 0%, #0c1a3a 50%, #0f172a 100%) !important; }
  .main-container { background: transparent !important; }
</style>

<div style="width:100%; display:flex; justify-content:center; padding:0 16px 60px;">
<div style="width:100%; max-width:620px;">

    <h2 class="auth-title">Editar publicación</h2>

    <div class="auth-card auth-card--wide">
        <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Título + Ciudad --}}
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

            {{-- Categoría --}}
            <div class="form-group" x-data="{ sel: @json(old('category', $post->category)), open: false }">
                <label>Categoría</label>
                <input type="hidden" name="category" :value="sel">
                <button type="button" @click="open = true"
                        class="cat-btn" :class="{ 'cat-btn--set': sel !== '' }">
                    <span x-text="sel || 'Elige una categoría...'"></span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <template x-teleport="body">
                    <div x-show="open" x-transition.opacity class="cat-overlay" @click.self="open = false" @keydown.escape.window="open = false">
                        <div class="cat-modal">
                            <div class="cat-modal-top">
                                <span class="cat-modal-title">Elige una categoría</span>
                                <button type="button" @click="open = false" class="cat-modal-close">✕</button>
                            </div>
                            <div class="cat-modal-grid">
                                @foreach(\App\Models\Post::CATEGORIES as $cat)
                                    <button type="button" class="cat-modal-item"
                                            :class="{ 'cat-modal-item--on': sel === @json($cat) }"
                                            @click="sel = @json($cat); open = false">
                                        {{ $cat }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </template>
                @error('category') <span class="error">{{ $message }}</span> @enderror
            </div>

            {{-- Descripción --}}
            <div class="form-group">
                <label for="content">Descripción</label>
                <textarea name="content" id="content"
                          placeholder="Cuéntanos qué hace especial este lugar..."
                          required>{{ old('content', $post->content) }}</textarea>
                @error('content') <span class="error">{{ $message }}</span> @enderror
            </div>

            {{-- Fotos --}}
            <div class="form-group">
                <label>Fotos actuales</label>
                @php $allImgs = $post->allImages(); @endphp
                @if($allImgs)
                    <div class="auth-upload-previews" style="margin-bottom:12px;">
                        @foreach($allImgs as $i => $img)
                            <div class="auth-thumb {{ $i === 0 ? 'auth-thumb--cover' : '' }}">
                                <img src="{{ asset($img) }}" alt="">
                                @if($i === 0) <span class="auth-thumb-badge">portada</span> @endif
                            </div>
                        @endforeach
                    </div>
                @endif
                <label style="margin-top:2px;">Nuevas fotos <span class="auth-hint-label">(opcional · reemplaza todas · máx. 6)</span></label>
                <div class="auth-upload" x-data="{ count: 0 }" @click="$refs.fi.click()">
                    <input x-ref="fi" type="file" name="images[]" accept="image/*" multiple
                           style="display:none" @change="count = $event.target.files.length">
                    <template x-if="count === 0">
                        <div>
                            <p class="auth-upload-text">Haz clic para seleccionar nuevas fotos</p>
                            <p class="auth-upload-hint">JPG o PNG · máx. 8 MB</p>
                        </div>
                    </template>
                    <template x-if="count > 0">
                        <div>
                            <p class="auth-upload-text"
                               x-text="count + (count === 1 ? ' foto seleccionada' : ' fotos seleccionadas')"></p>
                            <p class="auth-upload-hint">Haz clic para cambiar la selección</p>
                        </div>
                    </template>
                </div>
                @error('images') <span class="error">{{ $message }}</span> @enderror
                @error('images.*') <span class="error">{{ $message }}</span> @enderror
            </div>

            {{-- Mapa --}}
            <div class="form-group">
                <label>Ubicación en el mapa <span class="auth-hint-label">(opcional)</span></label>
                <div id="picker-map" class="map-picker"></div>
                <input type="hidden" name="lat" id="lat" value="{{ old('lat', $post->lat) }}">
                <input type="hidden" name="lng" id="lng" value="{{ old('lng', $post->lng) }}">
                <p class="map-picker-hint" id="picker-hint">
                    {{ $post->lat ? '📍 ' . number_format($post->lat, 5) . ', ' . number_format($post->lng, 5) : 'Haz clic en el mapa para marcar la ubicación' }}
                </p>
            </div>

            <button type="submit" class="btn-primary" style="margin-top:8px;">Guardar cambios</button>
        </form>
    </div>

    <p class="post-form-back"><a href="{{ route('posts.show', $post) }}">← Volver al post</a></p>

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
