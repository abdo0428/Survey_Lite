<x-guest-layout>
    <div class="auth-panel surface-card">
        <span class="eyebrow">{{ __('app.nav.register') }}</span>
        <h1 class="display-title auth-title">{{ __('app.auth.register.title') }}</h1>
        <p class="section-copy auth-copy">{{ __('app.landing.features_title') }}</p>

        <form method="POST" action="{{ route('register') }}" class="vstack gap-4">
            @csrf

            <div>
                <x-input-label for="name" :value="__('app.auth.fields.name')" />
                <x-text-input id="name" class="mt-2 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('app.auth.fields.email')" />
                <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('app.auth.fields.password')" />
                <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('app.auth.fields.confirm_password')" />
                <x-text-input id="password_confirmation" class="mt-2 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 pt-2">
                <a class="auth-link" href="{{ route('login') }}">
                    {{ __('app.auth.register.already_registered') }}
                </a>

                <x-primary-button>
                    {{ __('app.auth.register.submit') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
