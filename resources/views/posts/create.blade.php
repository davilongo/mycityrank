@extends('layouts.app')

@section('contenido')

<style>
  body, .main-container { background: #070d18 !important; }
</style>

<div class="post-form-page">

    <div class="post-form-header">
        <div class="post-form-glow"></div>
        <h1 class="post-form-title">Nueva publicación</h1>
        <p class="post-form-subtitle">Comparte un lugar increíble con la comunidad</p>
    </div>

    <form class="post-form" action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- 01 El lugar --}}
        <div class="pf-section">
            <div class="pf-section-head">
                <span class="pf-num">01</span>
                <span class="pf-section-name">El lugar</span>
            </div>
            <div class="pf-row-2">
                <div class="pf-field">
                    <label class="pf-label" for="title">Título</label>
                    <input class="pf-input" type="text" name="title" id="title"
                           value="{{ old('title') }}" placeholder="Ej: La mejor pizzería de Nápoles" required>
                    @error('title') <span class="pf-error">{{ $message }}</span> @enderror
                </div>
                <div class="pf-field">
                    <label class="pf-label" for="ciudad_nombre">Ciudad</label>
                    <input class="pf-input" type="text" name="ciudad_nombre" id="ciudad_nombre"
                           value="{{ old('ciudad_nombre') }}" placeholder="París, Roma, Tokio..." required>
                    @error('ciudad_nombre') <span class="pf-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- 02 Categoría --}}
        <div class="pf-section">
            <div class="pf-section-head">
                <span class="pf-num">02</span>
                <span class="pf-section-name">Categoría</span>
            </div>
            <div class="pf-cats" x-data="{ sel: @json(old('category', '')) }">
                <input type="hidden" name="category" :value="sel">
                @foreach(\App\Models\Post::CATEGORIES as $cat)
                    <button type="button"
                            @click="sel = @json($cat)"
                            :class="sel === @json($cat) ? 'pf-cat pf-cat--on' : 'pf-cat'">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>
            @error('category') <span class="pf-error">{{ $message }}</span> @enderror
        </div>

        {{-- 03 Descripción --}}
        <div class="pf-section">
            <div class="pf-section-head">
                <span class="pf-num">03</span>
                <span class="pf-section-name">Descripción</span>
            </div>
            <textarea class="pf-input pf-textarea" name="content" id="content"
                      placeholder="Cuéntanos qué hace especial este lugar, cómo llegar, qué pedir...&#10;Usa #hashtags para que te encuentren."
                      required>{{ old('content') }}</textarea>
            @error('content') <span class="pf-error">{{ $message }}</span> @enderror
        </div>

        {{-- 04 Fotos --}}
        <div class="pf-section">
            <div class="pf-section-head">
                <span class="pf-num">04</span>
                <span class="pf-section-name">Fotos</span>
            </div>
            <div class="pf-upload" x-data="{ count: 0, previews: [] }"
                 @click="$refs.fi.click()" @dragover.prevent @drop.prevent="handleDrop($event, $refs.fi)">
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
                    <div class="pf-upload-idle">
                        <div class="pf-upload-icon">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                        </div>
                        <p class="pf-upload-title">Arrastra o haz clic para subir</p>
                        <p class="pf-upload-hint">JPG, PNG &middot; máx. 8MB por foto &middot; hasta 6 fotos</p>
                    </div>
                </template>

                <template x-if="count > 0">
                    <div class="pf-upload-done" @click.stop>
                        <div class="pf-preview-grid">
                            <template x-for="(src, i) in previews" :key="i">
                                <div class="pf-preview-thumb" :class="i === 0 ? 'pf-preview-cover' : ''">
                                    <img :src="src" alt="">
                                    <span x-if="i === 0" class="pf-cover-badge">portada</span>
                                </div>
                            </template>
                        </div>
                        <p class="pf-upload-hint" style="margin-top:10px;" x-text="count + (count === 1 ? ' foto seleccionada' : ' fotos seleccionadas') + ' · haz clic para cambiar'"></p>
                        <button type="button" class="pf-upload-change" @click="$refs.fi.click()">Cambiar selección</button>
                    </div>
                </template>
            </div>
            @error('images') <span class="pf-error">{{ $message }}</span> @enderror
            @error('images.*') <span class="pf-error">{{ $message }}</span> @enderror
        </div>

        {{-- 05 Ubicación --}}
        <div class="pf-section">
            <div class="pf-section-head">
                <span class="pf-num">05</span>
                <span class="pf-section-name">Ubicación <span class="pf-optional">opcional</span></span>
            </div>
            <div id="picker-map" class="map-picker"></div>
            <input type="hidden" name="lat" id="lat">
            <input type="hidden" name="lng" id="lng">
            <p class="pf-map-hint" id="picker-hint">Haz clic en el mapa para marcar la ubicación exacta</p>
        </div>

        <button type="submit" class="pf-submit">
            <span>Publicar</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
            </svg>
        </button>
    </form>

    <div class="pf-back">
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
