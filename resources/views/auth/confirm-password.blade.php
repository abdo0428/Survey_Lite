<x-guest-layout>
    <div class="auth-panel surface-card">
        <span class="eyebrow">{{ __('app.auth.confirm_password.eyebrow') }}</span>
        <h1 class="display-title auth-title">{{ __('app.auth.confirm_password.title') }}</h1>
        <p class="section-copy auth-copy">
            {{ __('app.auth.confirm_password.description') }}
        </p>

        <form method="POST" action="{{ route('password.confirm') }}" class="vstack gap-4">
            @csrf

            <div>
                <x-input-label for="password" :value="__('app.auth.fields.password')" />
                <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="d-flex justify-content-end">
                <x-primary-button>
                    {{ __('app.auth.confirm_password.submit') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
