@extends('layouts.app')

@section('contenido')

@php
$catDesc = trans('catdesc');
@endphp

<div style="width:100%; display:flex; justify-content:center; padding:0 16px 60px;">
<div class="create-form-wrap">

    <div class="create-form-header">
        <a href="{{ route('posts.index') }}" class="create-form-back">&#8592;</a>
        <div>
            <h1 class="create-form-title">{{ __('posts.create_title') }}</h1>
            <p class="create-form-subtitle">{{ __('posts.create_subtitle') }}</p>
        </div>
    </div>

    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data"
          x-data='{ "sel": {!! json_encode(old("category", "")) !!} }'>
        @csrf

        {{-- 1. Categoría --}}
        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">1</span>
                <span class="form-section-title">{{ __('posts.choose_category') }}</span>
                <p class="form-section-sub">{{ __('posts.choose_category_sub') }}</p>
            </div>
            <input type="hidden" name="category" :value="sel">
            <div class="cat-cards-grid">
                @foreach(\App\Models\Post::CATEGORIES as $cat)
                    @php [$icon, $name] = array_pad(explode(' ', $cat, 2), 2, ''); @endphp
                    <button type="button" class="cat-card" data-cat="{{ $cat }}"
                            :class="{ 'cat-card--on': sel === $el.dataset.cat }"
                            @click="sel = $el.dataset.cat">
                        <span class="cat-card-icon">{{ $icon }}</span>
                        <span class="cat-card-name">{{ t_cat_name($cat) }}</span>
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
                <span class="form-section-title">{{ __('posts.details_title') }}</span>
                <p class="form-section-sub">{{ __('posts.details_sub') }}</p>
            </div>
            <div class="form-section-body">
                <div class="auth-row-2">
                    <div class="form-group">
                        <label for="title">{{ __('posts.title_label') }}</label>
                        <input type="text" name="title" id="title"
                               value="{{ old('title') }}" placeholder="{{ __('posts.title_placeholder') }}" required>
                        @error('title') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="ciudad_nombre">{{ __('posts.city_label') }}</label>
                        <input type="text" name="ciudad_nombre" id="ciudad_nombre"
                               value="{{ old('ciudad_nombre') }}" placeholder="{{ __('posts.city_placeholder') }}" required>
                        @error('ciudad_nombre') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label for="content">{{ __('posts.description_label') }}</label>
                    <textarea name="content" id="content"
                              placeholder="{{ __('posts.description_placeholder') }}"
                              required>{{ old('content') }}</textarea>
                    @error('content') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- 3. Fotos --}}
        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">3</span>
                <span class="form-section-title">{{ __('posts.photos_title') }}</span>
                <p class="form-section-sub">{{ __('posts.photos_sub') }}</p>
            </div>
            <div class="form-section-body">
                <div class="auth-upload" x-data="{
                        count: 0,
                        previews: [],
                        busy: false,
                        async pick(e) {
                            const files = Array.from(e.target.files).slice(0, 6);
                            if (!files.length) return;
                            this.busy = true; this.count = 0; this.previews = [];
                            const done = await Promise.all(files.map(f => compressImg(f)));
                            const dt = new DataTransfer();
                            done.forEach(f => dt.items.add(f));
                            this.$refs.fi.files = dt.files;
                            this.previews = done.map(f => URL.createObjectURL(f));
                            this.count = done.length;
                            this.busy = false;
                        }
                    }" @click="!busy && $refs.fi.click()">
                    <input x-ref="fi" type="file" name="images[]" accept="image/*" multiple required
                           style="display:none" @change="pick($event)">
                    <template x-if="busy">
                        <div>
                            <div class="auth-upload-icon">⏳</div>
                            <p class="auth-upload-text">{{ __('posts.optimizing') }}</p>
                            <p class="auth-upload-hint">{{ __('posts.compressing') }}</p>
                        </div>
                    </template>
                    <template x-if="!busy && count === 0">
                        <div>
                            <div class="auth-upload-icon">📷</div>
                            <p class="auth-upload-text">{{ __('posts.drag_images') }}</p>
                            <p class="auth-upload-hint">{{ __('posts.jpg_png_hint') }}</p>
                        </div>
                    </template>
                    <template x-if="!busy && count > 0">
                        <div @click.stop>
                            <div class="auth-upload-previews">
                                <template x-for="(src, i) in previews" :key="i">
                                    <div class="auth-thumb" :class="i === 0 ? 'auth-thumb--cover' : ''">
                                        <img :src="src" alt="">
                                        <span x-show="i === 0" class="auth-thumb-badge">{{ __('posts.cover_badge') }}</span>
                                    </div>
                                </template>
                            </div>
                            <p class="auth-upload-hint" style="margin-top:10px;"
                               x-text="count + (count === 1 ? ' {{ __('posts.photo_ready') }}' : ' {{ __('posts.photos_ready') }}')"></p>
                            <button type="button" class="auth-upload-change" @click="$refs.fi.click()">
                                {{ __('posts.change_selection') }}
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
                <span class="form-section-title">{{ __('posts.map_title') }} <span class="auth-hint-label">({{ __('posts.optional') }})</span></span>
                <p class="form-section-sub">{{ __('posts.map_subtitle') }}</p>
            </div>
            <div class="form-section-body">
                <div class="form-group" style="margin-bottom:12px;">
                    <label for="place_name">{{ __('posts.place_name_label') }} <span class="auth-hint-label">({{ __('posts.optional') }})</span></label>
                    <div class="map-search-wrap" style="margin-bottom:0;">
                        <input type="text" name="place_name" id="place_name" class="map-search-input"
                               value="{{ old('place_name') }}"
                               placeholder="{{ __('posts.place_name_placeholder') }}">
                        <button type="button" class="map-search-btn" id="map-search-btn">{{ __('posts.map_search_btn') }}</button>
                    </div>
                    @error('place_name') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div id="map-search-results" class="map-search-results"></div>
                <div id="picker-map" class="map-picker"></div>
                <input type="hidden" name="lat" id="lat">
                <input type="hidden" name="lng" id="lng">
                <p class="map-picker-hint" id="picker-hint">{{ __('posts.map_hint') }}</p>
            </div>
        </div>

        {{-- 5. Características --}}
        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">5</span>
                <span class="form-section-title">{{ __('posts.features_title') }} <span class="auth-hint-label">({{ __('posts.optional') }})</span></span>
                <p class="form-section-sub">{{ __('posts.features_sub') }}</p>
            </div>
            <div class="form-section-body">
                <div class="tags-picker-grid">
                    @foreach(\App\Models\Post::TAGS as $tag)
                        <label class="tag-chip-pick">
                            <input type="checkbox" name="tags[]" value="{{ $tag }}"
                                   {{ in_array($tag, old('tags', [])) ? 'checked' : '' }}>
                            <span>{{ t_tag($tag) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <button type="submit" class="btn-primary" style="width:100%;padding:15px;font-size:16px;margin-top:8px;">
            {{ __('posts.publish_btn') }}
        </button>
    </form>

    <p class="post-form-back" style="text-align:center;margin-top:20px;">
        <a href="{{ route('posts.index') }}">{{ __('posts.back_to_feed') }}</a>
    </p>

</div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const map = L.map('picker-map').setView([20, 0], 2);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/attributions">CARTO</a>'
    }).addTo(map);
    let marker = null;
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');
    const hint     = document.getElementById('picker-hint');

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

    map.on('click', function (e) {
        placeMarker(e.latlng.lat, e.latlng.lng);
    });

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
                `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=5&accept-language={{ app()->getLocale() }}`,
                { headers: { 'Accept-Language': '{{ app()->getLocale() }}' } }
            );
            const data = await res.json();
            searchBtn.textContent = '{{ __('posts.map_search_btn') }}';
            if (!data.length) {
                searchResults.innerHTML = '<div class="map-search-empty">{{ __('posts.map_no_results') }}</div>';
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
            searchBtn.textContent = '{{ __('posts.map_search_btn') }}';
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
