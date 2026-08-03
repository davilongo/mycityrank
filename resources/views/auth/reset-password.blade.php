<x-guest-layout>
    <h2 class="auth-title">{{ __('auth.reset_password_title') }}</h2>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group">
            <label for="email">{{ __('auth.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="tu@email.com">
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

        <button type="submit" class="btn-primary" style="width:100%;margin-top:8px;">{{ __('auth.reset_password_title') }}</button>
    </form>
</x-guest-layout>
