<x-guest-layout>
    <h2 class="auth-title">{{ __('auth.forgot_password') }}</h2>

    <p class="auth-footer" style="margin-bottom:20px;">
        {{ __('auth.forgot_password_text') }}
    </p>

    @if(session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email">{{ __('auth.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="tu@email.com">
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn-primary" style="width:100%;margin-top:8px;">{{ __('auth.email_reset_link') }}</button>
    </form>
</x-guest-layout>
