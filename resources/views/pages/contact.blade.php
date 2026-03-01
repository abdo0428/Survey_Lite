@extends('layouts.public')

@section('title', __('app.contact.title'))

@section('content')
<section class="row g-4 align-items-start">
    <div class="col-lg-5">
        <div class="surface-card h-100">
            <span class="eyebrow">{{ __('app.nav.contact') }}</span>
            <h1 class="display-title mb-3">{{ __('app.contact.title') }}</h1>
            <p class="section-copy mb-0">{{ __('app.contact.lead') }}</p>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="surface-card">
            <form method="POST" action="{{ route('contact.submit') }}" class="row g-3">
                @csrf
                <input type="text" name="company_website" class="d-none" tabindex="-1" autocomplete="off">
                <div class="col-md-6">
                    <label class="form-label">{{ __('app.contact.name') }}</label>
                    <input class="form-control" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('app.contact.email') }}</label>
                    <input class="form-control" type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">{{ __('app.contact.message') }}</label>
                    <textarea class="form-control" name="message" rows="6" required>{{ old('message') }}</textarea>
                </div>
                <div class="col-12">
                    <button class="btn btn-accent">{{ __('app.contact.submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
