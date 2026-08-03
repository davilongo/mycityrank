@extends('layouts.app')

@section('title', 'Gestión de agencias — XploreFree')

@section('contenido')

<div style="max-width:900px;margin:0 auto;padding:32px 16px 80px;">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0 0 4px;">🏢 Gestión de agencias</h1>
            <p style="color:var(--text-muted);margin:0;font-size:14px;">Declara agencias entre los usuarios registrados o da de alta una nueva.</p>
        </div>
        <a href="{{ route('admin.usuarios.create-agencia') }}" class="btn-nav">+ Nueva agencia</a>
    </div>

    <form method="GET" action="{{ route('admin.usuarios.index') }}" style="margin-bottom:20px;">
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre o email..." class="form-input">
    </form>

    @if($usuarios->isEmpty())
        <div class="empty-state">
            <p>No se encontraron usuarios.</p>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:10px;">
            @foreach($usuarios as $usuario)
                <div style="display:flex;align-items:center;gap:14px;padding:14px 18px;background:var(--card-bg);border-radius:12px;box-shadow:var(--shadow-sm);flex-wrap:wrap;">
                    @if($usuario->avatar)
                        <img src="{{ asset($usuario->avatar) }}" alt="" style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                    @else
                        <div style="width:40px;height:40px;border-radius:50%;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0;">
                            {{ mb_strtoupper(mb_substr($usuario->name, 0, 1)) }}
                        </div>
                    @endif

                    <div style="flex:1;min-width:180px;">
                        <div style="font-weight:700;font-size:14px;">
                            {{ $usuario->name }}
                            @if($usuario->isAdmin())
                                <span class="badge-admin">Admin</span>
                            @endif
                            @if($usuario->isAgencia())
                                <span class="badge-agencia">Agencia</span>
                            @endif
                        </div>
                        <div style="font-size:12px;color:var(--text-muted);">{{ $usuario->email }}</div>
                    </div>

                    <form method="POST" action="{{ route('admin.usuarios.toggle-agencia', $usuario) }}">
                        @csrf
                        <button type="submit" class="{{ $usuario->isAgencia() ? 'btn-ghost' : 'btn-nav' }}">
                            {{ $usuario->isAgencia() ? 'Quitar agencia' : 'Declarar agencia' }}
                        </button>
                    </form>

                    @if($usuario->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}"
                              onsubmit="return confirm('¿Seguro que quieres eliminar a {{ $usuario->name }}? Esta acción no se puede deshacer.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-ghost" style="color:#dc2626;">Eliminar</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
        <div style="margin-top:24px;">{{ $usuarios->links() }}</div>
    @endif

</div>

@endsection
