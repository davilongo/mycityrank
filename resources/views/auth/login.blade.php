<x-guest-layout>
    <h2 class="auth-title">Iniciar sesión</h2>

    @if(session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="tu@email.com">
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Tu contraseña">
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="auth-remember">
            <label>
                <input type="checkbox" name="remember"> Recuérdame
            </label>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
            @endif
        </div>

        <button type="submit" class="btn-primary" style="width:100%;margin-top:8px;">Entrar</button>

        <p class="auth-footer">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate gratis</a>
        </p>
    </form>
</x-guest-layout>
