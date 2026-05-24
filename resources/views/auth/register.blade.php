<x-guest-layout>
    <h2 class="auth-title">{{ __('auth.register_title') }}</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">{{ __('auth.name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="{{ __('auth.name') }}">
            @error('name') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="email">{{ __('auth.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="tu@email.com">
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="password">{{ __('auth.password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="{{ __('auth.min_8_chars') }}">
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">{{ __('auth.confirm_password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('auth.repeat_password') }}">
            @error('password_confirmation') <span class="error">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn-primary" style="width:100%;margin-top:8px;">{{ __('auth.register_btn') }}</button>

        <p class="auth-footer">
            {{ __('auth.already_account') }} <a href="{{ route('login') }}">{{ __('auth.sign_in') }}</a>
        </p>
    </form>
</x-guest-layout>
