@extends('layouts.app')

@section('title', 'Viajes organizados — XploreFree')
@section('meta_description', 'Descubre viajes organizados a los mejores destinos.')

@section('contenido')

<div style="max-width:1100px;margin:0 auto;padding:32px 16px 80px;">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:28px;">
        <div>
            <h1 style="font-size:26px;font-weight:800;margin:0 0 4px;">✈️ Viajes organizados</h1>
            <p style="color:var(--text-muted);margin:0;font-size:14px;">Próximas salidas disponibles</p>
        </div>
        @auth
            @if(auth()->user()->isAgencia() || auth()->user()->isAdmin())
                <a href="{{ route('viajes.create') }}" class="btn-nav">+ Nuevo viaje</a>
            @endif
        @endauth
    </div>

    @if($viajes->isEmpty())
        <div class="empty-state">
            <p>No hay viajes programados por el momento.</p>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;">
            @foreach($viajes as $viaje)
                <a href="{{ route('viajes.show', $viaje) }}" class="city-post-card" style="display:block;border-radius:16px;overflow:hidden;text-decoration:none;background:var(--card-bg);box-shadow:var(--shadow-sm);transition:transform .2s,box-shadow .2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.transform='';this.style.boxShadow='var(--shadow-sm)'">
                    @if($viaje->imagen)
                        <div style="height:180px;overflow:hidden;">
                            <img src="{{ asset('storage/' . $viaje->imagen) }}" alt="{{ $viaje->titulo }}"
                                 style="width:100%;height:100%;object-fit:cover;">
                        </div>
                    @else
                        <div style="height:180px;background:linear-gradient(135deg,var(--accent),#6366f1);display:flex;align-items:center;justify-content:center;font-size:48px;">✈️</div>
                    @endif
                    <div style="padding:16px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;flex-wrap:wrap;">
                            <a href="{{ route('ciudades.show', $viaje->ciudad) }}"
                               style="font-size:12px;font-weight:600;color:var(--accent);background:var(--accent-soft);padding:2px 10px;border-radius:20px;text-decoration:none;"
                               onclick="event.stopPropagation()">
                                📍 {{ $viaje->ciudad->nombre }}
                            </a>
                            <span style="font-size:12px;color:var(--text-muted);">{{ $viaje->duracion_dias }} día{{ $viaje->duracion_dias > 1 ? 's' : '' }}</span>
                        </div>
                        <h2 style="font-size:16px;font-weight:700;margin:0 0 8px;line-height:1.3;color:var(--text);">{{ $viaje->titulo }}</h2>
                        <p style="font-size:13px;color:var(--text-muted);margin:0 0 12px;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $viaje->descripcion }}</p>
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <div>
                                <span style="font-size:20px;font-weight:800;color:var(--accent);">{{ number_format($viaje->precio, 0, ',', '.') }} €</span>
                                <span style="font-size:11px;color:var(--text-muted);"> /persona</span>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:12px;font-weight:600;color:var(--text);">{{ $viaje->fecha_salida->format('d M Y') }}</div>
                                @if($viaje->plazas)
                                    <div style="font-size:11px;color:var(--text-muted);">{{ $viaje->plazas }} plazas</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div style="margin-top:24px;">{{ $viajes->links() }}</div>
    @endif

</div>

@endsection
