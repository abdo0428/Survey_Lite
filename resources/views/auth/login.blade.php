<x-guest-layout>
    <div class="auth-panel surface-card">
        <span class="eyebrow">{{ __('app.nav.login') }}</span>
        <h1 class="display-title auth-title">{{ __('app.auth.login.title') }}</h1>
        <p class="section-copy auth-copy">{{ __('app.brand.tagline') }}</p>

        <x-auth-session-status class="mb-4 text-sm text-emerald-600" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="vstack gap-4">
            @csrf

            <div>
                <x-input-label for="email" :value="__('app.auth.fields.email')" />
                <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('app.auth.fields.password')" />
                <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <label class="auth-check">
                <input id="remember_me" type="checkbox" name="remember">
                <span>{{ __('app.auth.fields.remember_me') }}</span>
            </label>

            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 pt-2">
                @if (Route::has('password.request'))
                    <a class="auth-link" href="{{ route('password.request') }}">
                        {{ __('app.auth.login.forgot_password') }}
                    </a>
                @endif

                <x-primary-button>
                    {{ __('app.auth.login.submit') }}
                </x-primary-button>
            </div>

            <div class="text-center small text-muted">
                {{ __('app.auth.login.need_account') }}
                <a class="auth-link" href="{{ route('register') }}">{{ __('app.auth.register.title') }}</a>
            </div>
        </form>
    </div>
</x-guest-layout>
