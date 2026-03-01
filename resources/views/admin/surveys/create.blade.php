@extends('layouts.admin')

@section('title', __('app.admin.surveys.new'))

@section('content')
<div class="surface-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="eyebrow">{{ __('app.admin.surveys.new') }}</span>
            <h1 class="display-title mb-0">{{ __('app.admin.surveys.new') }}</h1>
        </div>
        <a href="{{ route('admin.surveys.index') }}" class="btn btn-outline-secondary">{{ __('app.common.back') }}</a>
    </div>

    <form method="POST" action="{{ route('admin.surveys.store') }}" class="vstack gap-4">
        @csrf
        @include('admin.surveys._form')
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-accent">{{ __('app.common.create') }}</button>
            <a href="{{ route('admin.surveys.index') }}" class="btn btn-outline-secondary">{{ __('app.common.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
