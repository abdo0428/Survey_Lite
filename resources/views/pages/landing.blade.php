@extends('layouts.public')

@section('title', __('app.brand.name'))

@section('content')
<section class="hero-panel mb-5">
    <div class="row g-4 align-items-center">
        <div class="col-lg-7">
            <span class="eyebrow">{{ __('app.landing.eyebrow') }}</span>
            <h1 class="hero-title">{{ __('app.landing.title') }}</h1>
            <p class="hero-copy">{{ __('app.landing.subtitle') }}</p>
            <div class="d-flex flex-wrap gap-3 mt-4">
                <a class="btn btn-accent btn-lg" href="{{ auth()->check() ? route('dashboard') : route('register') }}">{{ __('app.landing.cta_primary') }}</a>
                <a class="btn btn-outline-secondary btn-lg" href="{{ auth()->check() ? route('dashboard') : route('login') }}">{{ __('app.landing.cta_secondary') }}</a>
            </div>

            
        </div>
        <div class="col-lg-5">
            <div class="hero-mockup">
                <div class="mockup-window">
                    <div class="mockup-dots">
                        <span></span><span></span><span></span>
                    </div>
                    <div class="mockup-chart-bars">
                        <div class="bar" style="height: 68%"></div>
                        <div class="bar" style="height: 40%"></div>
                        <div class="bar" style="height: 82%"></div>
                        <div class="bar" style="height: 58%"></div>
                    </div>
                    <div class="mockup-cards">
                        <div class="mockup-card">
                            <strong>{{ $stats['published_surveys'] }}</strong>
                            <span>{{ __('app.landing.stats.published_surveys') }}</span>
                        </div>
                        <div class="mockup-card">
                            <strong>{{ $stats['responses'] }}</strong>
                            <span>{{ __('app.landing.stats.responses') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="row g-3 mb-5">
    <div class="col-md-4">
        <div class="surface-card metric-card">
            <div class="metric-label">{{ __('app.landing.stats.published_surveys') }}</div>
            <div class="metric-value">{{ $stats['published_surveys'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="surface-card metric-card">
            <div class="metric-label">{{ __('app.landing.stats.responses') }}</div>
            <div class="metric-value">{{ $stats['responses'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="surface-card metric-card">
            <div class="metric-label">{{ __('app.landing.stats.teams') }}</div>
            <div class="metric-value">{{ $stats['teams'] }}</div>
        </div>
    </div>
</section>

<section class="mb-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <span class="eyebrow">{{ __('app.brand.name') }}</span>
            <h2 class="display-title mb-0">{{ __('app.landing.features_title') }}</h2>
        </div>
    </div>
    <div class="row g-4">
        @foreach(trans('app.landing.features') as $feature)
            <div class="col-md-6">
                <div class="surface-card h-100">
                    <h3 class="h4 mb-3">{{ $feature['title'] }}</h3>
                    <p class="section-copy mb-0">{{ $feature['body'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

<section class="surface-card preview-panel">
    <div class="row g-4 align-items-center">
        <div class="col-lg-6">
            <span class="eyebrow">{{ __('app.brand.name') }}</span>
            <h2 class="display-title mb-3">{{ __('app.landing.preview_title') }}</h2>
            <p class="section-copy mb-0">{{ __('app.landing.preview_body') }}</p>
        </div>
        <div class="col-lg-6">
            <div class="mini-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <strong>Launch Board</strong>
                    <span class="badge text-bg-success">{{ __('app.admin.surveys.published') }}</span>
                </div>
                <div class="vstack gap-2">
                    <div class="mock-line"></div>
                    <div class="mock-line short"></div>
                    <div class="mock-line"></div>
                    <div class="mock-line medium"></div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
