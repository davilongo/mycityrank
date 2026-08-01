@extends('layouts.app')

@section('title', 'Nuevo viaje — XploreFree')

@section('contenido')

<div style="width:100%;display:flex;justify-content:center;padding:0 16px 80px;">
<div class="create-form-wrap">

    <div class="create-form-header">
        <a href="{{ route('viajes.index') }}" class="create-form-back">&#8592;</a>
        <div>
            <h1 class="create-form-title">✈️ Nuevo viaje organizado</h1>
            <p class="create-form-subtitle">Rellena los datos del viaje para publicarlo</p>
        </div>
    </div>

    <form action="{{ route('viajes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Destino --}}
        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">1</span>
                <span class="form-section-title">Destino</span>
            </div>
            <div class="form-section-body">
                <div class="auth-row-2">
                    <div class="form-group">
                        <label for="ciudad_nombre">Ciudad destino</label>
                        <input type="text" name="ciudad_nombre" id="ciudad_nombre"
                               value="{{ old('ciudad_nombre') }}"
                               placeholder="Ej: Marrakech, Roma, Nueva York..." required>
                        @error('ciudad_nombre') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="pais">País</label>
                        <input type="text" name="pais" id="pais"
                               value="{{ old('pais') }}"
                               placeholder="Ej: Marruecos, Italia, Estados Unidos..." required>
                        @error('pais') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Información general --}}
        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">2</span>
                <span class="form-section-title">Información del viaje</span>
            </div>
            <div class="form-section-body">
                <div class="form-group">
                    <label for="titulo">Título del viaje</label>
                    <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}"
                           placeholder="Ej: Ruta por el norte de Marruecos" required>
                    @error('titulo') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label for="descripcion">Descripción e itinerario</label>
                    <textarea name="descripcion" id="descripcion" rows="6"
                              placeholder="Describe el viaje, qué incluye, el itinerario día a día..." required>{{ old('descripcion') }}</textarea>
                    @error('descripcion') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Fecha, duración y precio --}}
        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">3</span>
                <span class="form-section-title">Fechas y precio</span>
            </div>
            <div class="form-section-body">
                <div class="auth-row-2">
                    <div class="form-group">
                        <label for="fecha_salida">Fecha de salida</label>
                        <input type="date" name="fecha_salida" id="fecha_salida"
                               value="{{ old('fecha_salida') }}" min="{{ date('Y-m-d') }}" required>
                        @error('fecha_salida') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="duracion_dias">Duración (días)</label>
                        <input type="number" name="duracion_dias" id="duracion_dias"
                               value="{{ old('duracion_dias') }}" min="1" max="365"
                               placeholder="Ej: 7" required>
                        @error('duracion_dias') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="auth-row-2">
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="precio">Precio por persona (€)</label>
                        <input type="number" name="precio" id="precio"
                               value="{{ old('precio') }}" min="0" step="0.01"
                               placeholder="Ej: 850" required>
                        @error('precio') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="plazas">Plazas disponibles <span class="auth-hint-label">(opcional)</span></label>
                        <input type="number" name="plazas" id="plazas"
                               value="{{ old('plazas') }}" min="1"
                               placeholder="Ej: 20">
                        @error('plazas') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Contacto --}}
        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">4</span>
                <span class="form-section-title">Contacto</span>
                <p class="form-section-sub">WhatsApp, email o enlace de reserva</p>
            </div>
            <div class="form-section-body">
                <div class="form-group" style="margin-bottom:0;">
                    <label for="contacto">Cómo contactar</label>
                    <input type="text" name="contacto" id="contacto"
                           value="{{ old('contacto') }}"
                           placeholder="Ej: +34 600 000 000 o reservas@miagencia.com" required>
                    @error('contacto') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Imagen --}}
        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">5</span>
                <span class="form-section-title">Imagen <span class="auth-hint-label">(opcional)</span></span>
            </div>
            <div class="form-section-body">
                <div class="auth-upload" x-data="{
                        preview: null,
                        busy: false,
                        async pick(e) {
                            const file = e.target.files[0];
                            if (!file) return;
                            this.busy = true;
                            const done = await compressImg(file);
                            const dt = new DataTransfer();
                            dt.items.add(done);
                            this.$refs.fi.files = dt.files;
                            this.preview = URL.createObjectURL(done);
                            this.busy = false;
                        }
                    }" @click="!busy && $refs.fi.click()">
                    <input x-ref="fi" type="file" name="imagen" accept="image/*"
                           style="display:none" @change="pick($event)">
                    <template x-if="busy">
                        <div><div class="auth-upload-icon">⏳</div><p class="auth-upload-text">Optimizando...</p></div>
                    </template>
                    <template x-if="!busy && !preview">
                        <div>
                            <div class="auth-upload-icon">🖼️</div>
                            <p class="auth-upload-text">Sube una imagen del destino</p>
                            <p class="auth-upload-hint">JPG o PNG, máx 4 MB</p>
                        </div>
                    </template>
                    <template x-if="!busy && preview">
                        <div @click.stop>
                            <img :src="preview" style="max-height:160px;border-radius:10px;object-fit:cover;">
                            <button type="button" class="auth-upload-change" @click="$refs.fi.click()">Cambiar imagen</button>
                        </div>
                    </template>
                </div>
                @error('imagen') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>

        <button type="submit" class="btn-primary" style="width:100%;padding:15px;font-size:16px;margin-top:8px;">
            Publicar viaje
        </button>
    </form>

</div>
</div>

@endsection
