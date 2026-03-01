@php
    $locales = ['en', 'ar', 'tr'];
@endphp

<nav x-data="{ open: false, menu: false }" class="site-header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between gap-3 py-3">
            <a class="brand-mark text-decoration-none" href="{{ route('dashboard') }}">
                <span class="brand-dot"></span>
                <span>
                    <strong>{{ __('app.brand.name') }}</strong>
                    <small>{{ __('app.brand.tagline') }}</small>
                </span>
            </a>

            <button class="btn btn-sm btn-outline-secondary d-lg-none" @click="open = ! open" type="button">
                Menu
            </button>

            <div class="d-none d-lg-flex align-items-center gap-2">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard') }}">{{ __('app.nav.dashboard') }}</a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.surveys.index') }}">{{ __('app.nav.surveys') }}</a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('landing') }}" target="_blank">{{ __('app.nav.home') }}</a>
                <div class="position-relative" @click.outside="menu = false">
                    <button class="btn btn-sm btn-accent" type="button" @click="menu = ! menu">
                        {{ Auth::user()->name }}
                    </button>
                    <div class="account-menu" x-show="menu" x-transition style="display: none;">
                        <a href="{{ route('profile.edit') }}">{{ __('app.nav.profile') }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">{{ __('app.nav.logout') }}</button>
                        </form>
                    </div>
                </div>
                <div class="locale-switcher">
                    @foreach($locales as $locale)
                        <a class="locale-pill {{ app()->getLocale() === $locale ? 'active' : '' }}" href="{{ route('locale.switch', $locale) }}">
                            {{ strtoupper($locale) }}
                        </a>
                    @endforeach
                </div>

                <button class="btn btn-sm btn-outline-secondary" type="button" data-theme-toggle>{{ __('app.nav.theme') }}</button>


            </div>
        </div>

        <div class="d-lg-none pb-3" x-show="open" x-transition style="display: none;">
            <div class="mobile-panel">
                <div class="d-grid gap-2">
                    <a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">{{ __('app.nav.dashboard') }}</a>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.surveys.index') }}">{{ __('app.nav.surveys') }}</a>
                    <a class="btn btn-outline-secondary" href="{{ route('profile.edit') }}">{{ __('app.nav.profile') }}</a>
                    <a class="btn btn-outline-secondary" href="{{ route('landing') }}" target="_blank">{{ __('app.nav.home') }}</a>
                </div>

                <div class="locale-switcher mt-3">
                    @foreach($locales as $locale)
                        <a class="locale-pill {{ app()->getLocale() === $locale ? 'active' : '' }}" href="{{ route('locale.switch', $locale) }}">
                            {{ strtoupper($locale) }}
                        </a>
                    @endforeach
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-outline-secondary btn-sm" type="button" data-theme-toggle>{{ __('app.nav.theme') }}</button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-accent btn-sm" type="submit">{{ __('app.nav.logout') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
