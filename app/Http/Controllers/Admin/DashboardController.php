<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Response;
use App\Models\Survey;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();
        $surveysQuery = Survey::ownedBy($userId);
        $surveyIds = (clone $surveysQuery)->pluck('id');
        $responsesQuery = Response::query()->whereIn('survey_id', $surveyIds);

        return view('dashboard', [
            'metrics' => [
                'total_surveys' => (clone $surveysQuery)->count(),
                'published_surveys' => (clone $surveysQuery)->where('status', Survey::STATUS_PUBLISHED)->count(),
                'responses_today' => (clone $responsesQuery)->whereDate('created_at', today())->count(),
                'responses_week' => (clone $responsesQuery)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            ],
            'topSurvey' => (clone $surveysQuery)
                ->withCount('responses')
                ->orderByDesc('responses_count')
                ->first(),
            'latestResponses' => Response::with(['survey:id,title,public_token', 'user:id,name'])
                ->whereIn('survey_id', $surveyIds)
                ->latest()
                ->take(8)
                ->get(),
            'quickLinks' => Survey::ownedBy($userId)
                ->publishedForPublic()
                ->latest('published_at')
                ->take(4)
                ->get(),
        ]);
    }
}
