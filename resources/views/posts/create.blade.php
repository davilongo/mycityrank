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
        <a href="{{ route('posts.index') }}" class="create-form-back">&#8592;</a>
        <div>
            <h1 class="create-form-title">Crear publicación</h1>
            <p class="create-form-subtitle">Comparte tu experiencia con la comunidad</p>
        </div>
    </div>

    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data"
          x-data='{ "sel": {!! json_encode(old("category", "")) !!} }'>
        @csrf

        {{-- 1. Categoría --}}
        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">1</span>
                <span class="form-section-title">Elige una categoría</span>
                <p class="form-section-sub">Selecciona la categoría que mejor se adapte a tu publicación.</p>
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
                <span class="form-section-title">Cuéntanos los detalles</span>
                <p class="form-section-sub">Un buen título y descripción ayudan a que otros descubran este lugar.</p>
            </div>
            <div class="form-section-body">
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
                <div class="form-group" style="margin-bottom:0;">
                    <label for="content">Descripción</label>
                    <textarea name="content" id="content"
                              placeholder="Cuéntanos qué hace especial este lugar, cómo llegar, qué pedir... Usa #hashtags para que te encuentren."
                              required>{{ old('content') }}</textarea>
                    @error('content') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- 3. Fotos --}}
        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">3</span>
                <span class="form-section-title">Añade imágenes</span>
                <p class="form-section-sub">Las imágenes hacen tu publicación más atractiva. La primera será la portada · máx. 6.</p>
            </div>
            <div class="form-section-body">
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
                            <div class="auth-upload-icon">📷</div>
                            <p class="auth-upload-text">Arrastra tus imágenes aquí o haz clic para seleccionar</p>
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
        </div>

        {{-- 4. Mapa --}}
        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">4</span>
                <span class="form-section-title">Ubicación en el mapa <span class="auth-hint-label">(opcional)</span></span>
                <p class="form-section-sub">Marca la ubicación exacta para que otros puedan encontrarla fácilmente.</p>
            </div>
            <div class="form-section-body">
                <div id="picker-map" class="map-picker"></div>
                <input type="hidden" name="lat" id="lat">
                <input type="hidden" name="lng" id="lng">
                <p class="map-picker-hint" id="picker-hint">Haz clic en el mapa para marcar la ubicación</p>
            </div>
        </div>

        <button type="submit" class="btn-primary" style="width:100%;padding:15px;font-size:16px;margin-top:8px;">
            Publicar
        </button>
    </form>

    <p class="post-form-back" style="text-align:center;margin-top:20px;">
        <a href="{{ route('posts.index') }}">← Volver al feed</a>
    </p>

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
