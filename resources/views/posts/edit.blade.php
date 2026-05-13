@extends('layouts.app')

@section('contenido')

<style>
  body, .main-container { background: #070d18 !important; }
</style>

<div class="post-form-page">

    <div class="post-form-header">
        <div class="post-form-glow"></div>
        <h1 class="post-form-title">Editar publicación</h1>
        <p class="post-form-subtitle">Actualiza los datos de tu lugar</p>
    </div>

    <form class="post-form" action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                           value="{{ old('title', $post->title) }}" required>
                    @error('title') <span class="pf-error">{{ $message }}</span> @enderror
                </div>
                <div class="pf-field">
                    <label class="pf-label" for="ciudad_nombre">Ciudad</label>
                    <input class="pf-input" type="text" name="ciudad_nombre" id="ciudad_nombre"
                           value="{{ old('ciudad_nombre', $post->ciudad->nombre ?? '') }}" required>
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
            <div class="pf-cats" x-data="{ sel: @json(old('category', $post->category)) }">
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
                      placeholder="Cuéntanos qué hace especial este lugar..." required>{{ old('content', $post->content) }}</textarea>
            @error('content') <span class="pf-error">{{ $message }}</span> @enderror
        </div>

        {{-- 04 Fotos --}}
        <div class="pf-section">
            <div class="pf-section-head">
                <span class="pf-num">04</span>
                <span class="pf-section-name">Fotos <span class="pf-optional">reemplaza todas si seleccionas nuevas</span></span>
            </div>
            @php $allImgs = $post->allImages(); @endphp
            @if($allImgs)
                <div class="pf-preview-grid" style="justify-content:flex-start; margin-bottom:14px;">
                    @foreach($allImgs as $i => $img)
                        <div class="pf-preview-thumb {{ $i === 0 ? 'pf-preview-cover' : '' }}">
                            <img src="{{ asset($img) }}" alt="">
                            @if($i === 0) <span class="pf-cover-badge">portada</span> @endif
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="pf-upload" x-data="{ count: 0 }" @click="$refs.fi.click()">
                <input x-ref="fi" type="file" name="images[]" accept="image/*" multiple
                       style="display:none" @change="count = $event.target.files.length">
                <template x-if="count === 0">
                    <div class="pf-upload-idle">
                        <div class="pf-upload-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                        </div>
                        <p class="pf-upload-title">Subir nuevas fotos</p>
                        <p class="pf-upload-hint">Opcional · reemplazará las actuales · máx. 6</p>
                    </div>
                </template>
                <template x-if="count > 0">
                    <div class="pf-upload-done">
                        <p class="pf-upload-title" x-text="count + (count === 1 ? ' foto lista' : ' fotos listas')"></p>
                        <p class="pf-upload-hint">Haz clic para cambiar la selección</p>
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
            <input type="hidden" name="lat" id="lat" value="{{ old('lat', $post->lat) }}">
            <input type="hidden" name="lng" id="lng" value="{{ old('lng', $post->lng) }}">
            <p class="pf-map-hint" id="picker-hint">
                {{ $post->lat ? '📍 ' . number_format($post->lat, 5) . ', ' . number_format($post->lng, 5) : 'Haz clic en el mapa para marcar la ubicación exacta' }}
            </p>
        </div>

        <button type="submit" class="pf-submit">
            <span>Guardar cambios</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
            </svg>
        </button>
    </form>

    <div class="pf-back">
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
