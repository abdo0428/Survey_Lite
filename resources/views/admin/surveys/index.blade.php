@extends('layouts.admin')

@section('title', __('app.admin.surveys.index_title'))

@section('content')
<section class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-end gap-3 mb-4">
    <div>
        <span class="eyebrow">{{ __('app.nav.surveys') }}</span>
        <h1 class="display-title mb-2">{{ __('app.admin.surveys.index_title') }}</h1>
        <p class="section-copy mb-0">{{ __('app.admin.surveys.index_subtitle') }}</p>
    </div>
    <a href="{{ route('admin.surveys.create') }}" class="btn btn-accent">{{ __('app.admin.surveys.new') }}</a>
</section>

<div class="surface-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('app.common.title') }}</th>
                    <th>{{ __('app.common.status') }}</th>
                    <th>{{ __('app.common.responses') }}</th>
                    <th>{{ __('app.common.public_link') }}</th>
                    <th>{{ __('app.admin.surveys.schedule') }}</th>
                    <th>{{ __('app.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($surveys as $survey)
                    <tr>
                        <td>{{ $survey->id }}</td>
                        <td>
                            <strong>{{ $survey->title }}</strong>
                            <div class="text-muted small">{{ \Illuminate\Support\Str::limit($survey->description, 90) }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $survey->status === 'published' ? 'text-bg-success' : ($survey->status === 'archived' ? 'text-bg-secondary' : 'text-bg-warning') }}">
                                {{ __('app.admin.surveys.'.$survey->status) }}
                            </span>
                        </td>
                        <td>{{ $survey->responses_count }}</td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <a href="{{ route('public.survey.show', $survey->public_token) }}" target="_blank" class="small">
                                    /s/{{ $survey->public_token }}
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary align-self-start copy-btn" data-copy="{{ route('public.survey.show', $survey->public_token) }}">
                                    {{ __('app.common.copy_link') }}
                                </button>
                            </div>
                        </td>
                        <td class="small text-muted">
                            <div>{{ __('app.admin.surveys.published_at') }}: {{ $survey->published_at?->format('Y-m-d H:i') ?? '-' }}</div>
                            <div>{{ __('app.admin.surveys.closed_at') }}: {{ $survey->closed_at?->format('Y-m-d H:i') ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.surveys.edit', $survey) }}">{{ __('app.common.edit') }}</a>
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.surveys.questions.index', $survey) }}">{{ __('app.admin.questions.title') }}</a>
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.surveys.results.index', $survey) }}">{{ __('app.admin.results.title') }}</a>
                                <form method="POST" action="{{ route('admin.surveys.destroy', $survey) }}" onsubmit="return confirm('Delete survey?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ __('app.common.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">{{ __('app.admin.dashboard.no_responses') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $surveys->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.copy-btn').forEach((button) => {
        button.addEventListener('click', async () => {
            await navigator.clipboard.writeText(button.dataset.copy);
            if (window.surveyLiteToast) {
                window.surveyLiteToast('success', '{{ __('app.common.copy') }}');
            }
        });
    });
</script>
@endpush
