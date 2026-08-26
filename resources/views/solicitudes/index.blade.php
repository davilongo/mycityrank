@extends('layouts.app')

@section('title', 'Demanda de destinos — XploreFree')
@section('meta_description', 'Destinos que los usuarios piden, agrupados por ciudad.')

@section('contenido')

<div style="max-width:900px;margin:0 auto;padding:32px 16px 80px;">

    <div style="margin-bottom:28px;">
        <h1 style="font-size:26px;font-weight:800;margin:0 0 4px;">📊 Demanda de destinos</h1>
        <p style="color:var(--text-muted);margin:0;font-size:14px;">Destinos que los usuarios quieren visitar, para que organices el viaje que ya tiene demanda.</p>
    </div>

    @if($ciudades->isEmpty())
        <div class="empty-state">
            <p>Todavía no hay ninguna petición de destino.</p>
        </div>
    @else
        <div style="display:grid;gap:14px;">
            @foreach($ciudades as $ciudad)
                <details class="form-section" style="margin:0;">
                    <summary style="list-style:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span style="font-size:20px;font-weight:800;color:var(--accent);min-width:2.2ch;">{{ $ciudad->solicitudes_viaje_count }}</span>
                            <div>
                                <div style="font-weight:700;">{{ $ciudad->nombre }}</div>
                                <div style="font-size:12px;color:var(--text-muted);">{{ $ciudad->pais }}</div>
                            </div>
                        </div>
                        <a href="{{ route('viajes.create') }}" class="btn-nav" onclick="event.stopPropagation()" style="font-size:12px;">+ Crear viaje aquí</a>
                    </summary>
                    <div style="padding:0 16px 16px;display:grid;gap:10px;border-top:1px solid var(--border);">
                        @foreach($ciudad->solicitudesViaje as $solicitud)
                            <div style="padding-top:12px;font-size:13px;">
                                <div style="font-weight:600;">
                                    @if($solicitud->user)
                                        {{ $solicitud->user->name }}
                                    @else
                                        Invitado <span style="font-weight:400;color:var(--text-muted);">· {{ $solicitud->contacto }}</span>
                                    @endif
                                    @if($solicitud->fecha_aprox)
                                        <span style="font-weight:400;color:var(--text-muted);"> · {{ $solicitud->fecha_aprox }}</span>
                                    @endif
                                </div>
                                @if($solicitud->user && $solicitud->contacto)
                                    <p style="margin:4px 0 0;color:var(--text-muted);">📞 {{ $solicitud->contacto }}</p>
                                @endif
                                @if($solicitud->nota)
                                    <p style="margin:4px 0 0;color:var(--text-muted);">{{ $solicitud->nota }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </details>
            @endforeach
        </div>
    @endif

</div>

@endsection
