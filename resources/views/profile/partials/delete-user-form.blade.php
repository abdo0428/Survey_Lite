<section class="profile-section">
    <header class="profile-section-header">
        <div class="section-label text-danger">{{ __('app.profile.delete.eyebrow') }}</div>
        <h2>{{ __('app.profile.delete.title') }}</h2>
        <p>{{ __('app.profile.delete.description') }}</p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('app.profile.delete.title') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="auth-modal-body">
            @csrf
            @method('delete')

            <div class="section-label text-danger">{{ __('app.profile.delete.eyebrow') }}</div>
            <h2 class="auth-modal-title">{{ __('app.profile.delete.confirm_title') }}</h2>

            <p class="auth-modal-copy">
                {{ __('app.profile.delete.confirm_description') }}
            </p>

            <div class="mt-4">
                <x-input-label for="password" :value="__('app.auth.fields.password')" />
                <x-text-input id="password" name="password" type="password" class="mt-2 block w-full" placeholder="{{ __('app.auth.fields.password') }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="profile-actions mt-4">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('app.common.cancel') }}
                </x-secondary-button>

                <x-danger-button>
                    {{ __('app.profile.delete.title') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
