<x-guest-layout>
    <div class="auth-panel surface-card">
        <span class="eyebrow">{{ __('app.auth.reset_password.eyebrow') }}</span>
        <h1 class="display-title auth-title">{{ __('app.auth.reset_password.title') }}</h1>
        <p class="section-copy auth-copy">{{ __('app.auth.reset_password.description') }}</p>

        <form method="POST" action="{{ route('password.store') }}" class="vstack gap-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-input-label for="email" :value="__('app.auth.fields.email')" />
                <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
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

            <div class="d-flex justify-content-end">
                <x-primary-button>
                    {{ __('app.auth.reset_password.submit') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
