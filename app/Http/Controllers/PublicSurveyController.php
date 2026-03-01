<?php

namespace App\Http\Controllers;

use App\Models\Response;
use App\Models\Survey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicSurveyController extends Controller
{
    public function show(string $token, Request $request): View
    {
        $survey = Survey::where('public_token', $token)
            ->publishedForPublic()
            ->firstOrFail();
        $survey->load('questions');

        $fingerprint = $this->resolveFingerprint($request);
        $this->queueFingerprintCookie($fingerprint);

        return view('public.survey', [
            'survey' => $survey,
            'already' => $this->hasDuplicateSubmission($survey, $request, $fingerprint),
            'closed' => $survey->isClosed(),
        ]);
    }

    public function submit(string $token, Request $request): RedirectResponse
    {
        $survey = Survey::where('public_token', $token)
            ->publishedForPublic()
            ->firstOrFail();
        $survey->load('questions');

        $fingerprint = $this->resolveFingerprint($request);
        $this->queueFingerprintCookie($fingerprint);

        if ($request->filled('company_website')) {
            return redirect()->route('public.survey.thankyou', $survey->public_token);
        }

        if ($survey->isClosed()) {
            return redirect()->route('public.survey.show', $survey->public_token)
                ->with('error', __('app.public.survey_closed'));
        }

        if ($this->hasDuplicateSubmission($survey, $request, $fingerprint)) {
            return back()->with('error', __('app.public.already_submitted'));
        }

        $rules = [];
        foreach ($survey->questions as $question) {
            $fieldRules = [$question->is_required ? 'required' : 'nullable', 'string'];

            if ($question->type === 'mcq') {
                $fieldRules[] = Rule::in($question->options ?? []);
            }

            $rules['q_'.$question->id] = $fieldRules;
        }

        $validated = $request->validate($rules);
        $ipHash = hash('sha256', (string) $request->ip());
        $userId = auth()->id();

        DB::transaction(function () use ($survey, $validated, $ipHash, $userId, $request, $fingerprint) {
            $response = Response::create([
                'survey_id' => $survey->id,
                'user_id' => $userId,
                'ip_hash' => $ipHash,
                'respondent_fingerprint' => $fingerprint,
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]);

            foreach ($survey->questions as $question) {
                $value = $validated['q_'.$question->id] ?? null;
                $response->answers()->create([
                    'question_id' => $question->id,
                    'answer_text' => is_string($value) ? $value : null,
                ]);
            }
        });

        return redirect()->route('public.survey.thankyou', $token)
            ->with('success', __('app.public.thank_you_message'));
    }

    public function thankYou(string $token, Request $request): View
    {
        $survey = Survey::where('public_token', $token)
            ->publishedForPublic()
            ->firstOrFail();

        $this->queueFingerprintCookie($this->resolveFingerprint($request));

        return view('public.thank-you', ['survey' => $survey]);
    }

    private function hasDuplicateSubmission(Survey $survey, Request $request, string $fingerprint): bool
    {
        if ($survey->duplicate_policy === Survey::DUPLICATE_NONE) {
            return false;
        }

        $query = Response::query()->where('survey_id', $survey->id);

        return match ($survey->duplicate_policy) {
            Survey::DUPLICATE_USER_ONLY => auth()->check()
                ? (clone $query)->where('user_id', auth()->id())->exists()
                : false,
            Survey::DUPLICATE_IP_ONLY => (clone $query)
                ->where('ip_hash', hash('sha256', (string) $request->ip()))
                ->exists(),
            Survey::DUPLICATE_COOKIE_ONLY => (clone $query)
                ->where('respondent_fingerprint', $fingerprint)
                ->exists(),
            default => false,
        };
    }

    private function resolveFingerprint(Request $request): string
    {
        return (string) ($request->cookie('survey_fingerprint') ?: Str::uuid());
    }

    private function queueFingerprintCookie(string $fingerprint): void
    {
        Cookie::queue(cookie(
            'survey_fingerprint',
            $fingerprint,
            60 * 24 * 365,
            null,
            null,
            false,
            true,
            false,
            'lax'
        ));
    }
}
