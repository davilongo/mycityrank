@extends('layouts.app')

@section('title', $viaje->titulo . ' — XploreFree')
@section('meta_description', Str::limit($viaje->descripcion, 160))
@if($viaje->imagen)
@section('og_image', asset('storage/' . $viaje->imagen))
@endif

@section('contenido')

<div style="max-width:800px;margin:0 auto;padding:32px 16px 80px;">

    <a href="{{ route('viajes.index') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--text-muted);font-size:14px;text-decoration:none;margin-bottom:20px;">
        ← Todos los viajes
    </a>

    {{-- Imagen --}}
    @if($viaje->imagen)
        <div style="border-radius:20px;overflow:hidden;height:340px;margin-bottom:28px;">
            <img src="{{ asset('storage/' . $viaje->imagen) }}" alt="{{ $viaje->titulo }}"
                 style="width:100%;height:100%;object-fit:cover;">
        </div>
    @endif

    {{-- Cabecera --}}
    <div style="margin-bottom:24px;">
        <a href="{{ route('ciudades.show', $viaje->ciudad) }}"
           style="font-size:13px;font-weight:600;color:var(--accent);background:var(--accent-soft);padding:4px 12px;border-radius:20px;text-decoration:none;">
            📍 {{ $viaje->ciudad->nombre }}
        </a>
        <h1 style="font-size:28px;font-weight:800;margin:14px 0 8px;line-height:1.2;color:var(--text);">{{ $viaje->titulo }}</h1>
        <p style="font-size:14px;color:var(--text-muted);">
            Organizado por <strong>{{ $viaje->user->name }}</strong>
        </p>
    </div>

    {{-- Datos clave --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:14px;margin-bottom:32px;">
        <div style="background:var(--card-bg);border-radius:14px;padding:16px;text-align:center;box-shadow:var(--shadow-sm);">
            <div style="font-size:22px;margin-bottom:4px;">📅</div>
            <div style="font-size:13px;color:var(--text-muted);margin-bottom:2px;">Salida</div>
            <div style="font-weight:700;color:var(--text);">{{ $viaje->fecha_salida->format('d M Y') }}</div>
        </div>
        <div style="background:var(--card-bg);border-radius:14px;padding:16px;text-align:center;box-shadow:var(--shadow-sm);">
            <div style="font-size:22px;margin-bottom:4px;">🌙</div>
            <div style="font-size:13px;color:var(--text-muted);margin-bottom:2px;">Duración</div>
            <div style="font-weight:700;color:var(--text);">{{ $viaje->duracion_dias }} día{{ $viaje->duracion_dias > 1 ? 's' : '' }}</div>
        </div>
        <div style="background:var(--card-bg);border-radius:14px;padding:16px;text-align:center;box-shadow:var(--shadow-sm);">
            <div style="font-size:22px;margin-bottom:4px;">💰</div>
            <div style="font-size:13px;color:var(--text-muted);margin-bottom:2px;">Precio</div>
            <div style="font-weight:700;color:var(--accent);font-size:18px;">{{ number_format($viaje->precio, 0, ',', '.') }} €</div>
        </div>
        @if($viaje->plazas)
        <div style="background:var(--card-bg);border-radius:14px;padding:16px;text-align:center;box-shadow:var(--shadow-sm);">
            <div style="font-size:22px;margin-bottom:4px;">👥</div>
            <div style="font-size:13px;color:var(--text-muted);margin-bottom:2px;">Plazas</div>
            <div style="font-weight:700;color:var(--text);">{{ $viaje->plazas }}</div>
        </div>
        @endif
    </div>

    {{-- Descripción --}}
    <div style="background:var(--card-bg);border-radius:16px;padding:24px;margin-bottom:28px;box-shadow:var(--shadow-sm);">
        <h2 style="font-size:18px;font-weight:700;margin:0 0 14px;color:var(--text);">📋 Descripción del viaje</h2>
        <div style="font-size:15px;line-height:1.7;color:var(--text);white-space:pre-line;">{{ $viaje->descripcion }}</div>
    </div>

    {{-- CTA contacto --}}
    <div style="background:linear-gradient(135deg,var(--accent),#6366f1);border-radius:20px;padding:28px;text-align:center;color:#fff;">
        <h2 style="font-size:20px;font-weight:800;margin:0 0 8px;">¿Te interesa este viaje?</h2>
        <p style="margin:0 0 20px;opacity:.9;font-size:14px;">Contacta con la agencia para reservar tu plaza</p>
        @php
            $contacto = $viaje->contacto;
            $isPhone  = preg_match('/^\+?[\d\s\-]{7,}$/', $contacto);
            $isEmail  = filter_var($contacto, FILTER_VALIDATE_EMAIL);
            $isUrl    = str_starts_with($contacto, 'http');
        @endphp
        @if($isPhone)
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $contacto) }}"
               target="_blank"
               style="display:inline-block;background:#fff;color:var(--accent);font-weight:700;font-size:15px;padding:14px 32px;border-radius:12px;text-decoration:none;">
                💬 Contactar por WhatsApp
            </a>
        @elseif($isEmail)
            <a href="mailto:{{ $contacto }}"
               style="display:inline-block;background:#fff;color:var(--accent);font-weight:700;font-size:15px;padding:14px 32px;border-radius:12px;text-decoration:none;">
                ✉️ Enviar email
            </a>
        @elseif($isUrl)
            <a href="{{ $contacto }}" target="_blank"
               style="display:inline-block;background:#fff;color:var(--accent);font-weight:700;font-size:15px;padding:14px 32px;border-radius:12px;text-decoration:none;">
                🔗 Reservar ahora
            </a>
        @else
            <p style="font-size:18px;font-weight:700;margin:0;">{{ $contacto }}</p>
        @endif
    </div>

    {{-- Acciones agencia/admin --}}
    @auth
        @if(auth()->user()->id === $viaje->user_id || auth()->user()->isAdmin())
            <div style="display:flex;gap:12px;margin-top:24px;justify-content:flex-end;">
                <a href="{{ route('viajes.edit', $viaje) }}" class="btn-ghost">Editar</a>
                <form method="POST" action="{{ route('viajes.destroy', $viaje) }}"
                      onsubmit="return confirm('¿Eliminar este viaje?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="padding:8px 18px;border-radius:10px;border:1.5px solid #f87171;background:transparent;color:#f87171;font-size:14px;cursor:pointer;">Eliminar</button>
                </form>
            </div>
        @endif
    @endauth

</div>

@endsection
