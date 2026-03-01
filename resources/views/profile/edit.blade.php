<x-app-layout>
    <x-slot name="header">
        <span class="eyebrow">{{ __('app.nav.profile') }}</span>
        <h1 class="display-title mb-2">{{ __('app.profile.title') }}</h1>
        <p class="section-copy mb-0">{{ __('app.profile.subtitle') }}</p>
    </x-slot>

    <div class="profile-grid">
        <div class="surface-card profile-card">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="surface-card profile-card">
            @include('profile.partials.update-password-form')
        </div>

        <div class="surface-card profile-card profile-card-danger">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
