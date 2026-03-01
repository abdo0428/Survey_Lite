@extends('layouts.admin')

@section('title', __('app.admin.questions.title'))

@section('content')
<section class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-end gap-3 mb-4">
    <div>
        <span class="eyebrow">{{ __('app.admin.questions.title') }}</span>
        <h1 class="display-title mb-2">{{ $survey->title }}</h1>
        <p class="section-copy mb-0">{{ __('app.admin.questions.drag_hint') }}</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.surveys.edit', $survey) }}" class="btn btn-outline-secondary">{{ __('app.common.back') }}</a>
        <button class="btn btn-accent" type="button" id="add-question-button" data-bs-toggle="modal" data-bs-target="#questionModal">
            {{ __('app.admin.questions.add') }}
        </button>
    </div>
</section>

<div class="surface-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th></th>
                    <th>#</th>
                    <th>{{ __('app.admin.questions.text') }}</th>
                    <th>{{ __('app.admin.questions.type') }}</th>
                    <th>{{ __('app.common.required') }}</th>
                    <th>{{ __('app.admin.questions.order') }}</th>
                    <th>{{ __('app.common.actions') }}</th>
                </tr>
            </thead>
            <tbody id="questions-list">
                @forelse($questions as $question)
                    <tr data-id="{{ $question->id }}"
                        data-question="{{ e(json_encode([
                            'id' => $question->id,
                            'question_text' => $question->question_text,
                            'type' => $question->type,
                            'is_required' => $question->is_required,
                            'options_raw' => implode('|', $question->options ?? []),
                            'sort_order' => $question->sort_order,
                        ])) }}">
                        <td class="drag-handle text-muted">::</td>
                        <td>{{ $question->id }}</td>
                        <td>{{ $question->question_text }}</td>
                        <td>{{ __('app.admin.questions.'.($question->type === 'mcq' ? 'mcq_type' : 'text_type')) }}</td>
                        <td>{{ $question->is_required ? __('app.common.yes') : __('app.common.no') }}</td>
                        <td class="order-cell">{{ $question->sort_order }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-secondary edit-question" type="button">{{ __('app.common.edit') }}</button>
                                <button class="btn btn-sm btn-outline-danger delete-question" type="button">{{ __('app.common.delete') }}</button>
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
</div>

<div class="modal fade" id="questionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <div>
                    <div class="section-label" id="question-modal-label">{{ __('app.admin.questions.add') }}</div>
                    <h2 class="h4 mb-0">{{ __('app.admin.questions.title') }}</h2>
                </div>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="{{ __('app.common.close') }}"></button>
            </div>
            <div class="modal-body pt-0">
                <form id="question-form" class="row g-3">
                    <input type="hidden" id="question-id">
                    <div class="col-12">
                        <label class="form-label">{{ __('app.admin.questions.text') }}</label>
                        <input class="form-control" id="question-text">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('app.admin.questions.type') }}</label>
                        <select class="form-select" id="question-type">
                            <option value="text">{{ __('app.admin.questions.text_type') }}</option>
                            <option value="mcq">{{ __('app.admin.questions.mcq_type') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('app.common.required') }}</label>
                        <select class="form-select" id="question-required">
                            <option value="1">{{ __('app.common.yes') }}</option>
                            <option value="0">{{ __('app.common.no') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('app.admin.questions.order') }}</label>
                        <input class="form-control" type="number" min="1" id="question-order">
                    </div>
                    <div class="col-12" id="options-wrap">
                        <label class="form-label">{{ __('app.admin.questions.options') }}</label>
                        <input class="form-control" id="question-options" placeholder="Yes|No|Maybe">
                        <div class="form-text">{{ __('app.admin.questions.options_hint') }}</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">{{ __('app.common.cancel') }}</button>
                <button class="btn btn-accent" type="button" id="save-question">{{ __('app.common.save') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const surveyId = {{ $survey->id }};
    const questionModalEl = document.getElementById('questionModal');
    const questionModal = new bootstrap.Modal(questionModalEl);
    const autosaveKey = `survey-question-draft-${surveyId}`;

    const endpoints = {
        store: @json(route('admin.surveys.questions.store', $survey)),
        reorder: @json(route('admin.surveys.questions.reorder', $survey)),
        base: @json(url('admin/surveys/'.$survey->id.'/questions')),
    };

    const fields = {
        id: document.getElementById('question-id'),
        text: document.getElementById('question-text'),
        type: document.getElementById('question-type'),
        required: document.getElementById('question-required'),
        order: document.getElementById('question-order'),
        options: document.getElementById('question-options'),
        optionsWrap: document.getElementById('options-wrap'),
        label: document.getElementById('question-modal-label'),
    };

    function toggleOptions() {
        fields.optionsWrap.style.display = fields.type.value === 'mcq' ? 'block' : 'none';
    }

    function fillForm(question = null) {
        fields.id.value = question?.id ?? '';
        fields.text.value = question?.question_text ?? '';
        fields.type.value = question?.type ?? 'text';
        fields.required.value = String(question?.is_required ? 1 : 0);
        fields.order.value = question?.sort_order ?? document.querySelectorAll('#questions-list tr[data-id]').length + 1;
        fields.options.value = question?.options_raw ?? '';
        fields.label.textContent = question ? '{{ __('app.admin.questions.edit') }}' : '{{ __('app.admin.questions.add') }}';
        toggleOptions();
    }

    function persistDraft() {
        if (fields.id.value) {
            return;
        }

        localStorage.setItem(autosaveKey, JSON.stringify({
            question_text: fields.text.value,
            type: fields.type.value,
            is_required: fields.required.value,
            sort_order: fields.order.value,
            options_raw: fields.options.value,
        }));
    }

    function restoreDraft() {
        const draft = localStorage.getItem(autosaveKey);
        if (!draft) {
            fillForm();
            return;
        }

        fillForm(JSON.parse(draft));
        if (window.surveyLiteToast) {
            window.surveyLiteToast('info', '{{ __('app.admin.questions.autosave_restored') }}');
        }
    }

    async function saveQuestion() {
        const questionId = fields.id.value;
        const payload = new URLSearchParams({
            _token: csrfToken,
            question_text: fields.text.value,
            type: fields.type.value,
            is_required: fields.required.value,
            sort_order: fields.order.value,
            options_raw: fields.options.value,
        });

        if (questionId) {
            payload.append('_method', 'PUT');
        }

        const response = await fetch(questionId ? `${endpoints.base}/${questionId}` : endpoints.store, {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: payload,
        });

        if (!response.ok) {
            const data = await response.json();
            throw new Error(data.message || Object.values(data.errors || {})[0]?.[0] || 'Validation error');
        }

        localStorage.removeItem(autosaveKey);
        location.reload();
    }

    async function deleteQuestion(questionId) {
        const payload = new URLSearchParams({
            _token: csrfToken,
            _method: 'DELETE',
        });

        await fetch(`${endpoints.base}/${questionId}`, {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: payload,
        });

        location.reload();
    }

    async function saveOrder() {
        const payload = new URLSearchParams({ _token: csrfToken });
        document.querySelectorAll('#questions-list tr[data-id]').forEach((row) => payload.append('ids[]', row.dataset.id));

        const response = await fetch(endpoints.reorder, {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: payload,
        });

        if (response.ok) {
            document.querySelectorAll('#questions-list .order-cell').forEach((cell, index) => {
                cell.textContent = index + 1;
            });
            if (window.surveyLiteToast) {
                window.surveyLiteToast('success', '{{ __('app.admin.questions.reordered') }}');
            }
        }
    }

    document.getElementById('add-question-button').addEventListener('click', restoreDraft);
    document.getElementById('save-question').addEventListener('click', async () => {
        try {
            await saveQuestion();
        } catch (error) {
            if (window.surveyLiteToast) {
                window.surveyLiteToast('error', error.message);
            }
        }
    });

    ['input', 'change'].forEach((eventName) => {
        questionModalEl.querySelectorAll('input, select').forEach((element) => {
            element.addEventListener(eventName, persistDraft);
        });
    });

    fields.type.addEventListener('change', toggleOptions);

    document.querySelectorAll('.edit-question').forEach((button) => {
        button.addEventListener('click', () => {
            const question = JSON.parse(button.closest('tr').dataset.question);
            fillForm(question);
            questionModal.show();
        });
    });

    document.querySelectorAll('.delete-question').forEach((button) => {
        button.addEventListener('click', async () => {
            if (!confirm('Delete question?')) {
                return;
            }

            await deleteQuestion(button.closest('tr').dataset.id);
        });
    });

    new Sortable(document.getElementById('questions-list'), {
        handle: '.drag-handle',
        animation: 150,
        onEnd: saveOrder,
    });

    fillForm();
</script>
@endpush
