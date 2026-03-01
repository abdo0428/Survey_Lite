@extends('layouts.admin')

@section('title', $survey->title)

@section('content')
<section class="row g-4">
    <div class="col-xl-8">
        <div class="surface-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="eyebrow">{{ __('app.common.edit') }}</span>
                    <h1 class="display-title mb-0">{{ $survey->title }}</h1>
                </div>
                <span class="badge {{ $survey->status === 'published' ? 'text-bg-success' : ($survey->status === 'archived' ? 'text-bg-secondary' : 'text-bg-warning') }}">
                    {{ __('app.admin.surveys.'.$survey->status) }}
                </span>
            </div>

            <form method="POST" action="{{ route('admin.surveys.update', $survey) }}" class="vstack gap-4">
                @csrf
                @method('PUT')
                @include('admin.surveys._form', ['survey' => $survey])
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-accent">{{ __('app.common.save') }}</button>
                    <a href="{{ route('admin.surveys.questions.index', $survey) }}" class="btn btn-outline-secondary">{{ __('app.admin.questions.title') }}</a>
                    <a href="{{ route('admin.surveys.results.index', $survey) }}" class="btn btn-outline-secondary">{{ __('app.admin.results.title') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="surface-card mb-4">
            <div class="section-label">{{ __('app.common.public_link') }}</div>
            <h3 class="h5 mb-3">{{ __('app.common.public_link') }}</h3>
            <div class="vstack gap-3">
                <input class="form-control" value="{{ $publicUrl }}" readonly id="public-link-input">
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-outline-secondary" type="button" id="copy-public-link">{{ __('app.common.copy_link') }}</button>
                    <a class="btn btn-outline-secondary" href="{{ $publicUrl }}" target="_blank">{{ __('app.common.open') }}</a>
                </div>
            </div>
        </div>

        <div class="surface-card">
            <div class="section-label">{{ __('app.admin.dashboard.quick_actions') }}</div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.surveys.questions.index', $survey) }}">{{ __('app.admin.questions.title') }}</a>
                <a class="btn btn-outline-secondary" href="{{ route('admin.surveys.results.index', $survey) }}">{{ __('app.admin.results.title') }}</a>
                <form method="POST" action="{{ route('admin.surveys.destroy', $survey) }}" onsubmit="return confirm('Delete survey?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger">{{ __('app.common.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.getElementById('copy-public-link')?.addEventListener('click', async () => {
        await navigator.clipboard.writeText(document.getElementById('public-link-input').value);
        if (window.surveyLiteToast) {
            window.surveyLiteToast('success', '{{ __('app.common.copy') }}');
        }
    });
</script>
@endpush
