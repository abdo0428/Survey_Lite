@php
    $isRtl = app()->getLocale() === 'ar';
    $locales = ['en', 'ar', 'tr'];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" data-theme="light" data-bs-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', __('app.brand.name')) }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700|public-sans:400,500,600,700" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/{{ $isRtl ? 'bootstrap.rtl.min.css' : 'bootstrap.min.css' }}" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="app-shell auth-shell">
        <header class="site-header">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light px-0">
                    <a class="navbar-brand brand-mark" href="{{ route('landing') }}">
                        <span class="brand-dot"></span>
                        <span>
                            <strong>{{ __('app.brand.name') }}</strong>
                            <small>{{ __('app.brand.tagline') }}</small>
                        </span>
                    </a>

                    <div class="ms-auto d-flex align-items-center gap-2">
                        <div class="locale-switcher">
                            @foreach($locales as $locale)
                                <a class="locale-pill {{ app()->getLocale() === $locale ? 'active' : '' }}" href="{{ route('locale.switch', $locale) }}">
                                    {{ strtoupper($locale) }}
                                </a>
                            @endforeach
                        </div>

                        <button class="btn btn-sm btn-outline-secondary" type="button" data-theme-toggle>
                            {{ __('app.nav.theme') }}
                        </button>
                    </div>
                </nav>
            </div>
        </header>

        <main class="container py-4 py-lg-5">
            <div class="auth-grid">
                <section class="auth-side surface-card">
                    <span class="eyebrow">{{ __('app.brand.name') }}</span>
                    <h1 class="display-title mb-3">{{ __('app.brand.tagline') }}</h1>
                    <p class="section-copy mb-4">
                        {{ __('app.landing.subtitle') }}
                    </p>

                    <div class="vstack gap-3">
                        <div class="mini-panel">
                            <strong>{{ __('app.landing.features.0.title') }}</strong>
                            <div class="small mt-2">{{ __('app.landing.features.0.body') }}</div>
                        </div>
                        <div class="mini-panel">
                            <strong>{{ __('app.landing.features.1.title') }}</strong>
                            <div class="small mt-2">{{ __('app.landing.features.1.body') }}</div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a class="btn btn-outline-secondary" href="{{ route('landing') }}">{{ __('app.nav.home') }}</a>
                        <a class="btn btn-outline-secondary" href="{{ route('contact') }}">{{ __('app.nav.contact') }}</a>
                    </div>
                </section>

                <section class="auth-card">
                    {{ $slot }}
                </section>
            </div>
        </main>
    </body>
</html>
