@php
    $isRtl = app()->getLocale() === 'ar';
    $locales = ['en', 'ar', 'tr'];
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" data-theme="light" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('app.brand.name'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700|public-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/{{ $isRtl ? 'bootstrap.rtl.min.css' : 'bootstrap.min.css' }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="app-shell marketing-shell">
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


                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" style="visibility: visible;" id="publicNav">
                    <div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                        <a class="nav-link" href="{{ route('landing') }}">{{ __('app.nav.home') }}</a>
                        <a class="nav-link" href="{{ route('about') }}">{{ __('app.nav.about') }}</a>
                        <a class="nav-link" href="{{ route('privacy') }}">{{ __('app.nav.privacy') }}</a>
                        <a class="nav-link" href="{{ route('terms') }}">{{ __('app.nav.terms') }}</a>
                        <a class="nav-link" href="{{ route('contact') }}">{{ __('app.nav.contact') }}</a>

                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                {{ strtoupper(app()->getLocale()) }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                @foreach($locales as $locale)
                                    <a class="dropdown-item {{ app()->getLocale() === $locale ? 'active' : '' }}" href="{{ route('locale.switch', $locale) }}">
                                        {{ __('app.locale.'.$locale) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <button class="btn btn-sm btn-outline-secondary" type="button" data-theme-toggle aria-label="{{ __('app.nav.theme_toggle') }}">
                            {{ __('app.nav.theme') }}
                        </button>

                        @auth
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard') }}">{{ __('app.nav.dashboard') }}</a>
                        @else
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('login') }}">{{ __('app.nav.login') }}</a>
                            <a class="btn btn-sm btn-accent" href="{{ route('register') }}">{{ __('app.nav.register') }}</a>
                        @endauth
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main class="container py-4 py-lg-5">
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-soft mb-4">
                {{ $errors->first() }}
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <strong>{{ __('app.brand.name') }}</strong>
                <div class="text-muted small">{{ __('app.brand.tagline') }}</div>
            </div>
            <div class="d-flex flex-wrap gap-3 small">
                <a href="{{ route('about') }}">{{ __('app.nav.about') }}</a>
                <a href="{{ route('privacy') }}">{{ __('app.nav.privacy') }}</a>
                <a href="{{ route('terms') }}">{{ __('app.nav.terms') }}</a>
                <a href="{{ route('contact') }}">{{ __('app.nav.contact') }}</a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const successMessage = @json(session('success'));
            const errorMessage = @json(session('error'));

            if (successMessage && window.surveyLiteToast) {
                window.surveyLiteToast('success', successMessage);
            }

            if (errorMessage && window.surveyLiteToast) {
                window.surveyLiteToast('error', errorMessage);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
