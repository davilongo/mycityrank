@extends('layouts.app')

@section('title', 'Quiero viajar a... — MyCityRank')

@section('contenido')

<div style="width:100%;display:flex;justify-content:center;padding:0 16px 80px;">
<div class="create-form-wrap">

    <div class="create-form-header">
        <a href="{{ route('posts.index') }}" class="create-form-back">&#8592;</a>
        <div>
            <h1 class="create-form-title">🧭 ¿A dónde quieres viajar?</h1>
            <p class="create-form-subtitle">Cuéntanos tu destino soñado. Las agencias lo ven y pueden organizar el viaje.</p>
        </div>
    </div>

    <form action="{{ route('solicitudes.store') }}" method="POST">
        @csrf
        @php $step = 1; @endphp

        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">{{ $step++ }}</span>
                <span class="form-section-title">Destino</span>
            </div>
            <div class="form-section-body">
                <div class="auth-row-2">
                    <div class="form-group">
                        <label for="ciudad_nombre">Ciudad</label>
                        <input type="text" name="ciudad_nombre" id="ciudad_nombre"
                               value="{{ old('ciudad_nombre') }}"
                               placeholder="Ej: Lisboa, Ronda, Marrakech..." required>
                        @error('ciudad_nombre') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="pais">País</label>
                        <input type="text" name="pais" id="pais"
                               value="{{ old('pais') }}"
                               placeholder="Ej: Portugal, España, Marruecos..." required>
                        @error('pais') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        @guest
            <div class="form-section">
                <div class="form-section-hd">
                    <span class="form-section-num">{{ $step++ }}</span>
                    <span class="form-section-title">Contacto</span>
                    <p class="form-section-sub">No tienes cuenta, así que necesitamos cómo avisarte</p>
                </div>
                <div class="form-section-body">
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="contacto">Email o WhatsApp</label>
                        <input type="text" name="contacto" id="contacto"
                               value="{{ old('contacto') }}"
                               placeholder="Ej: tucorreo@ejemplo.com o +34 600 000 000" required>
                        @error('contacto') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        @endguest

        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">{{ $step++ }}</span>
                <span class="form-section-title">Cuándo <span class="auth-hint-label">(opcional)</span></span>
            </div>
            <div class="form-section-body">
                <div class="form-group" style="margin-bottom:0;">
                    <label for="fecha_aprox">Fecha aproximada</label>
                    <input type="text" name="fecha_aprox" id="fecha_aprox"
                           value="{{ old('fecha_aprox') }}"
                           placeholder="Ej: cualquier finde de octubre, verano 2027...">
                    @error('fecha_aprox') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="form-section-hd">
                <span class="form-section-num">{{ $step++ }}</span>
                <span class="form-section-title">Detalles <span class="auth-hint-label">(opcional)</span></span>
            </div>
            <div class="form-section-body">
                <div class="form-group" style="margin-bottom:0;">
                    <label for="nota">Nota para la agencia</label>
                    <textarea name="nota" id="nota" rows="4"
                              placeholder="Ej: somos 4 amigos, presupuesto ajustado, nos gustaría algo de senderismo...">{{ old('nota') }}</textarea>
                    @error('nota') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <button type="submit" class="btn-primary" style="width:100%;padding:15px;font-size:16px;margin-top:8px;">
            Enviar destino
        </button>
    </form>

</div>
</div>

@endsection
