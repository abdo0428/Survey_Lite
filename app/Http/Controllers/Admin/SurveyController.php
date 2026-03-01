<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Models\AuditLog;
use App\Models\Survey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SurveyController extends Controller
{
    public function index(): View
    {
        $surveys = Survey::ownedBy(auth()->id())
            ->withCount('responses')
            ->latest()
            ->paginate(10);

        return view('admin.surveys.index', compact('surveys'));
    }

    public function create(): View
    {
        return view('admin.surveys.create');
    }

    public function store(StoreSurveyRequest $request): RedirectResponse
    {
        $survey = Survey::create($this->surveyPayload($request) + [
            'user_id' => auth()->id(),
            'public_token' => Str::random(40),
        ]);

        AuditLog::record('survey.created', $survey, ['status' => $survey->status], auth()->id());

        return redirect()->route('admin.surveys.edit', $survey)->with('success', __('app.admin.surveys.created'));
    }

    public function show(Survey $survey): RedirectResponse
    {
        abort_unless($survey->user_id === auth()->id(), 403);

        return redirect()->route('admin.surveys.edit', $survey);
    }

    public function edit(Survey $survey): View
    {
        abort_unless($survey->user_id === auth()->id(), 403);

        return view('admin.surveys.edit', [
            'survey' => $survey,
            'publicUrl' => route('public.survey.show', $survey->public_token),
        ]);
    }

    public function update(UpdateSurveyRequest $request, Survey $survey): RedirectResponse
    {
        abort_unless($survey->user_id === auth()->id(), 403);

        $survey->update($this->surveyPayload($request, $survey));

        AuditLog::record('survey.updated', $survey, [
            'status' => $survey->status,
            'duplicate_policy' => $survey->duplicate_policy,
            'published_at' => $survey->published_at,
            'closed_at' => $survey->closed_at,
        ], auth()->id());

        return back()->with('success', __('app.admin.surveys.updated'));
    }

    public function destroy(Survey $survey): RedirectResponse
    {
        abort_unless($survey->user_id === auth()->id(), 403);

        AuditLog::record('survey.deleted', $survey, ['title' => $survey->title], auth()->id());
        $survey->delete();

        return redirect()->route('admin.surveys.index')->with('success', __('app.admin.surveys.deleted'));
    }

    private function surveyPayload(StoreSurveyRequest|UpdateSurveyRequest $request, ?Survey $survey = null): array
    {
        $status = $request->string('status')->toString();
        $publishedAt = $request->filled('published_at')
            ? Carbon::parse($request->string('published_at')->toString())
            : ($status === Survey::STATUS_PUBLISHED ? ($survey?->published_at ?? now()) : null);

        $closedAt = $request->filled('closed_at')
            ? Carbon::parse($request->string('closed_at')->toString())
            : null;

        return [
            'title' => $request->string('title')->toString(),
            'description' => $request->filled('description') ? $request->string('description')->toString() : null,
            'status' => $status,
            'published_at' => $publishedAt,
            'closed_at' => $closedAt,
            'duplicate_policy' => $request->string('duplicate_policy')->toString(),
            'is_active' => $status === Survey::STATUS_PUBLISHED,
        ];
    }
}
