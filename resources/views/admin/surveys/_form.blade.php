@php
    $survey = $survey ?? null;
@endphp

<div class="row g-3">
    <div class="col-lg-8">
        <label class="form-label">{{ __('app.common.title') }}</label>
        <input class="form-control" name="title" value="{{ old('title', $survey?->title) }}" required>
    </div>
    <div class="col-lg-4">
        <label class="form-label">{{ __('app.common.status') }}</label>
        <select class="form-select" name="status" required>
            @foreach(['draft', 'published', 'archived'] as $status)
                <option value="{{ $status }}" @selected(old('status', $survey?->status ?? 'draft') === $status)>
                    {{ __('app.admin.surveys.'.$status) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('app.common.description') }}</label>
        <textarea class="form-control" name="description" rows="4">{{ old('description', $survey?->description) }}</textarea>
    </div>
    <div class="col-lg-6">
        <label class="form-label">{{ __('app.admin.surveys.published_at') }}</label>
        <input class="form-control" type="datetime-local" name="published_at" value="{{ old('published_at', $survey?->published_at?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-lg-6">
        <label class="form-label">{{ __('app.admin.surveys.closed_at') }}</label>
        <input class="form-control" type="datetime-local" name="closed_at" value="{{ old('closed_at', $survey?->closed_at?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-lg-6">
        <label class="form-label">{{ __('app.admin.surveys.duplicate_policy') }}</label>
        <select class="form-select" name="duplicate_policy" required>
            @foreach(['user_only', 'ip_only', 'cookie_only', 'none'] as $policy)
                <option value="{{ $policy }}" @selected(old('duplicate_policy', $survey?->duplicate_policy ?? 'cookie_only') === $policy)>
                    {{ __('app.admin.surveys.'.$policy) }}
                </option>
            @endforeach
        </select>
    </div>
</div>
