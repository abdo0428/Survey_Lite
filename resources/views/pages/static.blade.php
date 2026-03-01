@extends('layouts.public')

@section('title', $title)

@section('content')
<section class="surface-card page-hero mb-4">
    <span class="eyebrow">{{ __('app.nav.'.$pageKey) }}</span>
    <h1 class="display-title mb-3">{{ $title }}</h1>
    <p class="section-copy mb-0">{{ $lead }}</p>
</section>

<section class="row g-4">
    @foreach($sections as $section)
        <div class="col-lg-4">
            <div class="surface-card h-100">
                <h2 class="h4 mb-3">{{ $section['title'] }}</h2>
                <p class="section-copy mb-0">{{ $section['body'] }}</p>
            </div>
        </div>
    @endforeach
</section>
@endsection
