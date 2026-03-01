@extends('layouts.admin')

@section('title', __('app.admin.dashboard.title'))

@section('content')
<section class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-end gap-3 mb-4">
    <div>
        <span class="eyebrow">{{ __('app.admin.dashboard.title') }}</span>
        <h1 class="display-title mb-2">{{ __('app.admin.dashboard.title') }}</h1>
        <p class="section-copy mb-0">{{ __('app.admin.dashboard.subtitle') }}</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-accent" href="{{ route('admin.surveys.create') }}">{{ __('app.admin.dashboard.create_survey') }}</a>
        <a class="btn btn-outline-secondary" href="{{ route('admin.surveys.index') }}">{{ __('app.admin.dashboard.manage_surveys') }}</a>
    </div>
</section>

<section class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="surface-card metric-card">
            <div class="metric-label">{{ __('app.admin.dashboard.total_surveys') }}</div>
            <div class="metric-value">{{ $metrics['total_surveys'] }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="surface-card metric-card">
            <div class="metric-label">{{ __('app.admin.dashboard.published_surveys') }}</div>
            <div class="metric-value">{{ $metrics['published_surveys'] }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="surface-card metric-card">
            <div class="metric-label">{{ __('app.admin.dashboard.responses_today') }}</div>
            <div class="metric-value">{{ $metrics['responses_today'] }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="surface-card metric-card">
            <div class="metric-label">{{ __('app.admin.dashboard.responses_week') }}</div>
            <div class="metric-value">{{ $metrics['responses_week'] }}</div>
        </div>
    </div>
</section>

<section class="row g-4">
    <div class="col-xl-4">
        <div class="surface-card h-100">
            <div class="section-label">{{ __('app.admin.dashboard.top_survey') }}</div>
            @if($topSurvey)
                <h3 class="h4 mb-2">{{ $topSurvey->title }}</h3>
                <p class="text-muted mb-3">{{ $topSurvey->description ?: __('app.brand.tagline') }}</p>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge text-bg-dark">{{ __('app.admin.surveys.'.$topSurvey->status) }}</span>
                    <strong>{{ $topSurvey->responses_count }} {{ __('app.common.responses') }}</strong>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.surveys.edit', $topSurvey) }}">{{ __('app.common.edit') }}</a>
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.surveys.results.index', $topSurvey) }}">{{ __('app.admin.results.title') }}</a>
                </div>
            @else
                <p class="text-muted mb-0">{{ __('app.admin.dashboard.no_responses') }}</p>
            @endif
        </div>
    </div>

    <div class="col-xl-8">
        <div class="surface-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="section-label">{{ __('app.admin.dashboard.quick_actions') }}</div>
                    <h3 class="h4 mb-0">{{ __('app.nav.surveys') }}</h3>
                </div>
            </div>
            <div class="row g-3">
                @foreach($quickLinks as $survey)
                    <div class="col-md-6">
                        <div class="mini-panel">
                            <div class="d-flex justify-content-between gap-2">
                                <div>
                                    <strong>{{ $survey->title }}</strong>
                                    <div class="text-muted small">{{ $survey->published_at?->format('Y-m-d H:i') ?? '-' }}</div>
                                </div>
                                <span class="badge text-bg-success">{{ __('app.admin.surveys.published') }}</span>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.surveys.results.index', $survey) }}">{{ __('app.admin.results.title') }}</a>
                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('public.survey.show', $survey->public_token) }}" target="_blank">{{ __('app.common.open') }}</a>
                                <button class="btn btn-outline-secondary btn-sm copy-btn" type="button" data-copy="{{ route('public.survey.show', $survey->public_token) }}">
                                    {{ __('app.common.copy_link') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if($quickLinks->isEmpty())
                    <div class="col-12">
                        <div class="mini-panel text-muted">{{ __('app.admin.dashboard.no_responses') }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="surface-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="section-label">{{ __('app.admin.dashboard.latest_responses') }}</div>
                    <h3 class="h4 mb-0">{{ __('app.admin.dashboard.latest_responses') }}</h3>
                </div>
            </div>

            @if($latestResponses->isEmpty())
                <p class="text-muted mb-0">{{ __('app.admin.dashboard.no_responses') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('app.common.title') }}</th>
                                <th>{{ __('app.admin.results.respondent') }}</th>
                                <th>{{ __('app.admin.results.submitted_at') }}</th>
                                <th>{{ __('app.common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestResponses as $response)
                                <tr>
                                    <td>{{ $response->survey?->title }}</td>
                                    <td>{{ $response->user?->name ?? 'Guest' }}</td>
                                    <td>{{ $response->created_at?->diffForHumans() }}</td>
                                    <td>
                                        @if($response->survey)
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.surveys.results.index', $response->survey) }}">
                                                {{ __('app.admin.results.title') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</section>
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
