@extends('layouts.app')

@section('contenido')

<div class="form-page" style="max-width:600px;">
    <div class="form-card">
        <h1 class="form-title">✏️ Editar perfil</h1>

        @if($errors->any())
            <div class="alert-error">
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="post-form">
            @csrf @method('PUT')

            <div class="profile-avatar-edit">
                @if($user->avatar)
                    <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="profile-avatar">
                @else
                    <div class="profile-avatar-placeholder">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</div>
                @endif
                <div class="form-group" style="margin:0;flex:1;">
                    <label>Foto de perfil</label>
                    <input type="file" name="avatar" accept="image/*" class="form-input">
                </div>
            </div>

            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input">
            </div>

            <div class="form-group">
                <label>Biografía <span style="font-weight:400;text-transform:none;">(máx. 200 caracteres)</span></label>
                <textarea name="bio" rows="3" maxlength="200" class="form-input" placeholder="Cuéntanos algo sobre ti...">{{ old('bio', $user->bio) }}</textarea>
            </div>

            <div class="form-actions">
                <a href="{{ route('users.show', $user) }}" class="btn-ghost">Cancelar</a>
                <button type="submit" class="btn-nav">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

@endsection
