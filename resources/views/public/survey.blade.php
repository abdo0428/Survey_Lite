@extends('layouts.public')

@section('title', $survey->title)

@section('content')
<section class="row justify-content-center">
    <div class="col-xl-8">
        <div class="surface-card">
            <span class="eyebrow">{{ __('app.brand.name') }}</span>
            <h1 class="display-title mb-2">{{ $survey->title }}</h1>
            @if($survey->description)
                <p class="section-copy mb-4">{{ $survey->description }}</p>
            @endif

            @if($closed)
                <div class="alert alert-warning border-0 shadow-soft mb-0">{{ __('app.public.survey_closed') }}</div>
            @elseif($already)
                <div class="alert alert-info border-0 shadow-soft mb-0">{{ __('app.public.already_submitted') }}</div>
            @else
                <form method="POST" action="{{ route('public.survey.submit', $survey->public_token) }}" class="vstack gap-4">
                    @csrf
                    <input type="text" name="company_website" class="d-none" tabindex="-1" autocomplete="off">
                    @foreach($survey->questions as $question)
                        <div class="mini-panel">
                            <label class="form-label fw-semibold">
                                {{ $question->question_text }}
                                @if($question->is_required)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>

                            @if($question->type === 'mcq')
                                <select class="form-select" name="q_{{ $question->id }}">
                                    <option value="">{{ __('app.public.select_option') }}</option>
                                    @foreach($question->options ?? [] as $option)
                                        <option value="{{ $option }}" @selected(old('q_'.$question->id) === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            @else
                                <textarea class="form-control" name="q_{{ $question->id }}" rows="3">{{ old('q_'.$question->id) }}</textarea>
                            @endif
                        </div>
                    @endforeach

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-accent btn-lg">{{ __('app.public.submit') }}</button>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('landing') }}">{{ __('app.public.back_home') }}</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</section>
@endsection
