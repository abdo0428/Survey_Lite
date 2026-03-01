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
    <title>@yield('title', config('app.name', __('app.brand.name')))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700|public-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/{{ $isRtl ? 'bootstrap.rtl.min.css' : 'bootstrap.min.css' }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="app-shell account-shell">

        <header class="site-header">
        <div class="container">    
             @include('layouts.navigation')

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
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
