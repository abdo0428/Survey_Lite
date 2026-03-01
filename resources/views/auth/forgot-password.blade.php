<x-guest-layout>
    <div class="auth-panel surface-card">
        <span class="eyebrow">{{ __('app.auth.forgot_password.eyebrow') }}</span>
        <h1 class="display-title auth-title">{{ __('app.auth.forgot_password.title') }}</h1>
        <p class="section-copy auth-copy">
            {{ __('app.auth.forgot_password.description') }}
        </p>

        <x-auth-session-status class="mb-4 text-sm text-emerald-600" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="vstack gap-4">
            @csrf

            <div>
                <x-input-label for="email" :value="__('app.auth.fields.email')" />
                <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="d-flex justify-content-end">
                <x-primary-button>
                    {{ __('app.auth.forgot_password.submit') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
