@extends('layouts.app')

@section('contenido')

<style>
  body, .main-container {
    background: linear-gradient(135deg, #0f172a 0%, #0c1a3a 50%, #0f172a 100%) !important;
  }
</style>

<div class="post-form-wrap">

    <h2 class="auth-title">Nueva publicación</h2>

    <div class="auth-card auth-card--wide">
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Título + Ciudad --}}
            <div class="auth-row-2">
                <div class="form-group">
                    <label for="title">Título</label>
                    <input type="text" name="title" id="title"
                           value="{{ old('title') }}" placeholder="Ej: La mejor pizzería de Nápoles" required>
                    @error('title') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="ciudad_nombre">Ciudad</label>
                    <input type="text" name="ciudad_nombre" id="ciudad_nombre"
                           value="{{ old('ciudad_nombre') }}" placeholder="París, Roma, Tokio..." required>
                    @error('ciudad_nombre') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Categoría --}}
            <div class="form-group">
                <label>Categoría</label>
                <div class="auth-cats" x-data="{ sel: @json(old('category', '')) }">
                    <input type="hidden" name="category" :value="sel">
                    @foreach(\App\Models\Post::CATEGORIES as $cat)
                        <button type="button"
                                @click="sel = @json($cat)"
                                :class="sel === @json($cat) ? 'auth-cat auth-cat--on' : 'auth-cat'">
                            {{ $cat }}
                        </button>
                    @endforeach
                </div>
                @error('category') <span class="error">{{ $message }}</span> @enderror
            </div>

            {{-- Descripción --}}
            <div class="form-group">
                <label for="content">Descripción</label>
                <textarea name="content" id="content"
                          placeholder="Cuéntanos qué hace especial este lugar, cómo llegar, qué pedir... Usa #hashtags para que te encuentren."
                          required>{{ old('content') }}</textarea>
                @error('content') <span class="error">{{ $message }}</span> @enderror
            </div>

            {{-- Fotos --}}
            <div class="form-group">
                <label>Fotos <span class="auth-hint-label">(la primera será la portada · máx. 6)</span></label>
                <div class="auth-upload" x-data="{ count: 0, previews: [] }" @click="$refs.fi.click()">
                    <input x-ref="fi" type="file" name="images[]" accept="image/*" multiple required
                           style="display:none"
                           @change="
                               count = $event.target.files.length;
                               previews = [];
                               Array.from($event.target.files).slice(0,6).forEach(f => {
                                   const r = new FileReader();
                                   r.onload = e => previews.push(e.target.result);
                                   r.readAsDataURL(f);
                               });
                           ">
                    <template x-if="count === 0">
                        <div>
                            <p class="auth-upload-text">Haz clic para seleccionar fotos</p>
                            <p class="auth-upload-hint">JPG o PNG · máx. 8 MB · hasta 6 fotos</p>
                        </div>
                    </template>
                    <template x-if="count > 0">
                        <div @click.stop>
                            <div class="auth-upload-previews">
                                <template x-for="(src, i) in previews" :key="i">
                                    <div class="auth-thumb" :class="i === 0 ? 'auth-thumb--cover' : ''">
                                        <img :src="src" alt="">
                                        <span x-show="i === 0" class="auth-thumb-badge">portada</span>
                                    </div>
                                </template>
                            </div>
                            <p class="auth-upload-hint" style="margin-top:10px;"
                               x-text="count + (count === 1 ? ' foto seleccionada' : ' fotos seleccionadas')"></p>
                            <button type="button" class="auth-upload-change" @click="$refs.fi.click()">
                                Cambiar selección
                            </button>
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
                <input type="hidden" name="lat" id="lat">
                <input type="hidden" name="lng" id="lng">
                <p class="map-picker-hint" id="picker-hint">Haz clic en el mapa para marcar la ubicación</p>
            </div>

            <button type="submit" class="btn-primary" style="margin-top:8px;">Publicar</button>
        </form>
    </div>

    <p class="post-form-back"><a href="{{ route('posts.index') }}">← Volver al feed</a></p>

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
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');
    const hint     = document.getElementById('picker-hint');
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
