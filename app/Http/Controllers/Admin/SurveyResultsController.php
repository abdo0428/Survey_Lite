<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Response;
use App\Models\Survey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response as Resp;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SurveyResultsController extends Controller
{
    public function index(Request $request, Survey $survey): View
    {
        abort_unless($survey->user_id === auth()->id(), 403);

        $survey->load('questions');
        $filters = $this->validatedFilters($request, $survey);
        $filteredResponses = $this->filteredResponsesQuery($survey, $filters);
        $responsesCount = (clone $filteredResponses)->count();

        $responses = (clone $filteredResponses)
            ->with(['answers.question', 'user:id,name'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $mcqStats = Cache::remember(
            'survey-results-'.$survey->id.'-'.md5(json_encode($filters)),
            now()->addSeconds(60),
            fn () => $this->mcqStats($survey, $filters),
        );

        return view('admin.results.index', [
            'survey' => $survey,
            'responsesCount' => $responsesCount,
            'responses' => $responses,
            'mcqStats' => $mcqStats,
            'filters' => $filters,
        ]);
    }

    public function exportCsv(Request $request, Survey $survey)
    {
        abort_unless($survey->user_id === auth()->id(), 403);

        $survey->load('questions');
        $filters = $this->validatedFilters($request, $survey);
        $filename = 'survey_'.$survey->id.'_results.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($survey, $filters) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

            $header = ['response_id', 'submitted_at', 'user_id', 'ip_hash', 'fingerprint'];
            foreach ($survey->questions as $index => $question) {
                $header[] = 'Q'.($index + 1).' '.Str::limit($question->question_text, 42);
            }
            fputcsv($out, $header);

            $this->filteredResponsesQuery($survey, $filters)
                ->with('answers')
                ->orderBy('id')
                ->chunkById(200, function ($responses) use ($out, $survey) {
                    foreach ($responses as $response) {
                        $row = [
                            $response->id,
                            optional($response->created_at)->format('Y-m-d H:i:s'),
                            $response->user_id,
                            $response->ip_hash,
                            $response->respondent_fingerprint,
                        ];
                        $answerMap = $response->answers->pluck('answer_text', 'question_id')->toArray();

                        foreach ($survey->questions as $question) {
                            $row[] = $answerMap[$question->id] ?? '';
                        }

                        fputcsv($out, $row);
                    }
                });

            fclose($out);
        };

        return Resp::stream($callback, 200, $headers);
    }

    private function validatedFilters(Request $request, Survey $survey): array
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'question_id' => ['nullable', 'integer'],
            'answer' => ['nullable', 'string', 'max:255'],
            'keyword' => ['nullable', 'string', 'max:255'],
        ]);

        $questionId = (int) ($filters['question_id'] ?? 0);
        if ($questionId !== 0 && ! $survey->questions->contains('id', $questionId)) {
            abort(404);
        }

        return $filters;
    }

    private function filteredResponsesQuery(Survey $survey, array $filters): Builder
    {
        $query = Response::query()->where('survey_id', $survey->id);

        if (! empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        if (! empty($filters['question_id'])) {
            $questionId = (int) $filters['question_id'];
            $answer = trim((string) ($filters['answer'] ?? ''));

            $query->whereHas('answers', function (Builder $builder) use ($questionId, $answer) {
                $builder->where('question_id', $questionId);

                if ($answer !== '') {
                    $builder->where('answer_text', 'like', '%'.$answer.'%');
                }
            });
        }

        if (! empty($filters['keyword'])) {
            $keyword = trim((string) $filters['keyword']);

            $query->whereHas('answers', function (Builder $builder) use ($keyword) {
                $builder->where('answer_text', 'like', '%'.$keyword.'%')
                    ->whereHas('question', fn (Builder $questionQuery) => $questionQuery->where('type', 'text'));
            });
        }

        return $query;
    }

    private function mcqStats(Survey $survey, array $filters): array
    {
        $responseIds = $this->filteredResponsesQuery($survey, $filters)->select('id');
        $stats = [];

        foreach ($survey->questions->where('type', 'mcq') as $question) {
            $counts = Answer::query()
                ->where('question_id', $question->id)
                ->whereIn('response_id', $responseIds)
                ->selectRaw('answer_text as label, COUNT(*) as cnt')
                ->groupBy('answer_text')
                ->orderByDesc('cnt')
                ->pluck('cnt', 'label')
                ->toArray();

            $stats[$question->id] = [
                'question' => $question->question_text,
                'labels' => array_keys($counts),
                'data' => array_values($counts),
            ];
        }

        return $stats;
    }
}
