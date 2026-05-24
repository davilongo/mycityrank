<x-guest-layout>
    <h2 class="auth-title">{{ __('auth.login_title') }}</h2>

    @if(session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">{{ __('auth.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="tu@email.com">
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="password">{{ __('auth.password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="{{ __('auth.password') }}">
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="auth-remember">
            <label>
                <input type="checkbox" name="remember"> {{ __('auth.remember_me') }}
            </label>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}">{{ __('auth.forgot_password') }}</a>
            @endif
        </div>

        <button type="submit" class="btn-primary" style="width:100%;margin-top:8px;">{{ __('auth.enter_btn') }}</button>

        <p class="auth-footer">
            {{ __('auth.no_account') }} <a href="{{ route('register') }}">{{ __('auth.sign_up_free') }}</a>
        </p>
    </form>
</x-guest-layout>
