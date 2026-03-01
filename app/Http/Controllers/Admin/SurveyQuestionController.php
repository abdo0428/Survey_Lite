<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Models\AuditLog;
use App\Models\Question;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SurveyQuestionController extends Controller
{
    public function index(Survey $survey): View
    {
        abort_unless($survey->user_id === auth()->id(), 403);

        return view('admin.questions.index', [
            'survey' => $survey,
            'questions' => $survey->questions()->get(),
        ]);
    }

    public function store(StoreQuestionRequest $request, Survey $survey): JsonResponse
    {
        abort_unless($survey->user_id === auth()->id(), 403);

        $question = $survey->questions()->create([
            'question_text' => $request->question_text,
            'type' => $request->type,
            'options' => $this->parseOptions($request->type, $request->input('options_raw')),
            'is_required' => (bool) ($request->is_required ?? true),
            'sort_order' => (int) ($request->sort_order ?? ($survey->questions()->max('sort_order') + 1)),
        ]);

        AuditLog::record('question.created', $question, ['survey_id' => $survey->id], auth()->id());

        return response()->json(['ok' => true, 'question' => $question]);
    }

    public function update(StoreQuestionRequest $request, Survey $survey, Question $question): JsonResponse
    {
        abort_unless($survey->user_id === auth()->id(), 403);
        abort_unless($question->survey_id === $survey->id, 404);

        $question->update([
            'question_text' => $request->question_text,
            'type' => $request->type,
            'options' => $this->parseOptions($request->type, $request->input('options_raw')),
            'is_required' => (bool) ($request->is_required ?? true),
            'sort_order' => (int) ($request->sort_order ?? $question->sort_order),
        ]);

        AuditLog::record('question.updated', $question, ['survey_id' => $survey->id], auth()->id());

        return response()->json(['ok' => true]);
    }

    public function destroy(Survey $survey, Question $question): JsonResponse
    {
        abort_unless($survey->user_id === auth()->id(), 403);
        abort_unless($question->survey_id === $survey->id, 404);

        AuditLog::record('question.deleted', $question, ['survey_id' => $survey->id], auth()->id());
        $question->delete();

        return response()->json(['ok' => true]);
    }

    public function reorder(Request $request, Survey $survey): JsonResponse
    {
        abort_unless($survey->user_id === auth()->id(), 403);

        $payload = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        DB::transaction(function () use ($payload, $survey) {
            foreach ($payload['ids'] as $index => $id) {
                Question::where('id', $id)
                    ->where('survey_id', $survey->id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        AuditLog::record('question.reordered', $survey, ['ids' => $payload['ids']], auth()->id());

        return response()->json(['ok' => true]);
    }

    private function parseOptions(string $type, ?string $raw): ?array
    {
        if ($type !== 'mcq') {
            return null;
        }

        return array_values(array_filter(array_map('trim', explode('|', trim((string) $raw)))));
    }
}
