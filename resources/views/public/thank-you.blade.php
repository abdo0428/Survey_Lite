@extends('layouts.public')

@section('title', __('app.public.thank_you_title'))

@section('content')
<section class="row justify-content-center">
    <div class="col-xl-7">
        <div class="surface-card text-center">
            <span class="eyebrow">{{ $survey->title }}</span>
            <h1 class="display-title mb-3">{{ __('app.public.thank_you_title') }}</h1>
            <p class="section-copy mb-4">{{ __('app.public.thank_you_message') }}</p>
            <div class="d-flex justify-content-center flex-wrap gap-2">
                <a class="btn btn-accent" href="{{ route('landing') }}">{{ __('app.public.back_home') }}</a>
                <a class="btn btn-outline-secondary" href="{{ route('public.survey.show', $survey->public_token) }}">{{ __('app.common.open') }}</a>
            </div>
        </div>
    </div>
</section>
@endsection
