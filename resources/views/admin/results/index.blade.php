@extends('layouts.admin')

@section('title', __('app.admin.results.title'))

@section('content')
@php
    $exportUrl = route('admin.surveys.results.export', array_merge(['survey' => $survey], request()->query()));
    $questionMeta = $survey->questions->map(fn ($question) => [
        'id' => $question->id,
        'type' => $question->type,
        'options' => $question->options ?? [],
    ])->values();
@endphp

<section class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-end gap-3 mb-4">
    <div>
        <span class="eyebrow">{{ __('app.admin.results.title') }}</span>
        <h1 class="display-title mb-2">{{ $survey->title }}</h1>
        <p class="section-copy mb-0">{{ __('app.admin.results.responses_total') }}: <strong>{{ $responsesCount }}</strong></p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('admin.surveys.questions.index', $survey) }}">{{ __('app.common.back') }}</a>
        <a class="btn btn-outline-secondary" href="{{ $exportUrl }}">{{ __('app.common.export_csv') }}</a>
        <button class="btn btn-accent" type="button" id="download-all-charts">{{ __('app.common.download_all_charts') }}</button>
    </div>
</section>

<div class="surface-card mb-4">
    <form class="row g-3" method="GET">
        <div class="col-md-3">
            <label class="form-label">{{ __('app.common.from') }}</label>
            <input class="form-control" type="date" name="from" value="{{ $filters['from'] ?? '' }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('app.common.to') }}</label>
            <input class="form-control" type="date" name="to" value="{{ $filters['to'] ?? '' }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('app.common.question') }}</label>
            <select class="form-select" name="question_id" id="question-filter">
                <option value="">{{ __('app.admin.results.all_questions') }}</option>
                @foreach($survey->questions as $question)
                    <option value="{{ $question->id }}" @selected(($filters['question_id'] ?? null) == $question->id)>{{ $question->question_text }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('app.common.answer') }}</label>
            <div id="answer-filter-slot"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('app.common.keyword') }}</label>
            <input class="form-control" name="keyword" value="{{ $filters['keyword'] ?? '' }}">
        </div>
        <div class="col-12 d-flex flex-wrap gap-2">
            <button class="btn btn-accent">{{ __('app.common.filters') }}</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.surveys.results.index', $survey) }}">{{ __('app.common.clear_filters') }}</a>
        </div>
    </form>
</div>

@if(!empty($mcqStats))
    <section class="row g-4 mb-4">
        @foreach($mcqStats as $questionId => $stat)
            <div class="col-xl-6">
                <div class="surface-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="section-label">{{ __('app.common.responses') }}</div>
                            <h3 class="h5 mb-0">{{ $stat['question'] }}</h3>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary download-chart" type="button" data-target="chart-{{ $questionId }}">
                            {{ __('app.common.download_chart') }}
                        </button>
                    </div>
                    @if(count($stat['data']) === 0)
                        <p class="text-muted mb-0">{{ __('app.admin.results.no_results') }}</p>
                    @else
                        <div class="chart-frame">
                            <canvas id="chart-{{ $questionId }}" class="result-chart"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </section>
@endif

<section class="surface-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="section-label">{{ __('app.admin.results.response_log') }}</div>
            <h3 class="h4 mb-0">{{ __('app.admin.results.response_log') }}</h3>
        </div>
    </div>

    @if($responses->isEmpty())
        <p class="text-muted mb-0">{{ __('app.admin.results.no_results') }}</p>
    @else
        <div class="accordion survey-accordion" id="responseAccordion">
            @foreach($responses as $response)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#response-{{ $response->id }}">
                            <span class="me-3">#{{ $response->id }}</span>
                            <span class="me-3">{{ $response->created_at?->format('Y-m-d H:i') }}</span>
                            <span class="text-muted">{{ $response->user?->name ?? 'Guest' }}</span>
                        </button>
                    </h2>
                    <div id="response-{{ $response->id }}" class="accordion-collapse collapse"  style ="visibility: visible; "data-bs-parent="#responseAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                @foreach($response->answers as $answer)
                                    <div class="col-md-6">
                                        <div class="mini-panel h-100">
                                            <strong class="d-block mb-2">{{ $answer->question?->question_text }}</strong>
                                            <div class="text-muted">{{ $answer->answer_text ?: '-' }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $responses->links() }}
        </div>
    @endif
</section>
@endsection

@push('scripts')
<script>
    const stats = @json($mcqStats);
    const questions = @json($questionMeta);
    const selectedAnswer = @json($filters['answer'] ?? '');
    const selectOptionLabel = @json(__('app.public.select_option'));
    const chartRegistry = {};

    Object.entries(stats).forEach(([questionId, stat]) => {
        const canvas = document.getElementById(`chart-${questionId}`);
        if (!canvas || !stat.data.length) {
            return;
        }

        chartRegistry[questionId] = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: stat.labels,
                datasets: [{
                    label: '{{ __('app.common.responses') }}',
                    data: stat.data,
                    backgroundColor: ['#0f766e', '#f59e0b', '#fb7185', '#155e75', '#334155'],
                    borderRadius: 10,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    });

    function buildAnswerFilter() {
        const slot = document.getElementById('answer-filter-slot');
        const questionId = document.getElementById('question-filter').value;
        const question = questions.find((item) => String(item.id) === questionId);
        slot.innerHTML = '';

        if (question && question.type === 'mcq' && question.options.length) {
            const select = document.createElement('select');
            select.name = 'answer';
            select.className = 'form-select';
            select.innerHTML = `<option value="">${selectOptionLabel}</option>`;
            question.options.forEach((option) => {
                const optionEl = document.createElement('option');
                optionEl.value = option;
                optionEl.textContent = option;
                if (option === selectedAnswer) {
                    optionEl.selected = true;
                }
                select.appendChild(optionEl);
            });
            slot.appendChild(select);
            return;
        }

        const input = document.createElement('input');
        input.className = 'form-control';
        input.name = 'answer';
        input.value = selectedAnswer;
        slot.appendChild(input);
    }

    function downloadChart(chartId) {
        const chart = Object.values(chartRegistry).find((instance) => instance.canvas.id === chartId);
        if (!chart) {
            return;
        }

        const link = document.createElement('a');
        link.href = chart.toBase64Image('image/png', 1);
        link.download = `${chartId}.png`;
        link.click();
    }

    document.getElementById('question-filter').addEventListener('change', buildAnswerFilter);
    buildAnswerFilter();

    document.querySelectorAll('.download-chart').forEach((button) => {
        button.addEventListener('click', () => downloadChart(button.dataset.target));
    });

    document.getElementById('download-all-charts').addEventListener('click', () => {
        document.querySelectorAll('.download-chart').forEach((button) => downloadChart(button.dataset.target));
    });
</script>
@endpush
