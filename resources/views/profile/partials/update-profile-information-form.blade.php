<section class="profile-section">
    <header class="profile-section-header">
        <div class="section-label">{{ __('app.profile.info.eyebrow') }}</div>
        <h2>{{ __('app.profile.info.title') }}</h2>
        <p>{{ __('app.profile.info.description') }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="profile-form">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('app.auth.fields.name')" />
            <x-text-input id="name" name="name" type="text" class="mt-2 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('app.auth.fields.email')" />
            <x-text-input id="email" name="email" type="email" class="mt-2 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="verification-panel">
                    <p>
                        {{ __('app.profile.info.unverified') }}
                        <button form="send-verification" class="auth-link" type="submit">
                            {{ __('app.profile.info.resend') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="verification-success">
                            {{ __('app.profile.info.sent') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="profile-actions">
            <x-primary-button>{{ __('app.common.save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="profile-note"
                >{{ __('app.common.saved') }}</p>
            @endif
        </div>
    </form>
</section>
