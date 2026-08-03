@extends('layouts.app')

@section('title', 'Nueva agencia — XploreFree')

@section('contenido')

<div class="form-page">
    <div class="form-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:24px;">
            <a href="{{ route('admin.usuarios.index') }}" class="btn-ghost" style="padding:6px 10px;">&#8592;</a>
            <h1 class="form-title" style="margin:0;">🏢 Nueva agencia</h1>
        </div>

        @if($errors->any())
            <div class="alert-error">
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.usuarios.store-agencia') }}">
            @csrf

            <div class="form-group">
                <label for="name">Nombre de la agencia</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="username">
            </div>

            <p style="font-size:13px;color:var(--text-muted);margin:0 0 18px;">
                No hace falta que le pongas contraseña: le enviaremos un email para que elija la suya.
            </p>

            <div class="form-actions">
                <a href="{{ route('admin.usuarios.index') }}" class="btn-ghost">Cancelar</a>
                <button type="submit" class="btn-nav">Crear agencia</button>
            </div>
        </form>
    </div>
</div>

@endsection
