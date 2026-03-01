<x-guest-layout>
    <div class="auth-panel surface-card">
        <span class="eyebrow">{{ __('app.auth.verify_email.eyebrow') }}</span>
        <h1 class="display-title auth-title">{{ __('app.auth.verify_email.title') }}</h1>
        <p class="section-copy auth-copy">
            {{ __('app.auth.verify_email.description') }}
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="small text-emerald-600 mb-4">
                {{ __('app.auth.verify_email.sent') }}
            </div>
        @endif

        <div class="d-flex flex-column flex-sm-row justify-content-between gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-primary-button>
                    {{ __('app.auth.verify_email.resend') }}
                </x-primary-button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sl-btn sl-btn-secondary">
                    {{ __('app.auth.verify_email.logout') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
