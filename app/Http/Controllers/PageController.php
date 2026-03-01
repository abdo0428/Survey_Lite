<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Response as SurveyResponse;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class PageController extends Controller
{
    public function landing(): View
    {
        $stats = [
            'published_surveys' => 0,
            'responses' => 0,
            'teams' => 0,
        ];

        try {
            DB::connection()->getPdo();

            $stats = [
                'published_surveys' => Survey::publishedForPublic()->count(),
                'responses' => SurveyResponse::count(),
                'teams' => User::count(),
            ];
        } catch (Throwable) {
            //
        }

        return view('pages.landing', [
            'stats' => $stats,
        ]);
    }

    public function about(): View
    {
        return $this->staticPage('about');
    }

    public function privacy(): View
    {
        return $this->staticPage('privacy');
    }

    public function terms(): View
    {
        return $this->staticPage('terms');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'message' => ['required', 'string', 'max:3000'],
            'company_website' => ['nullable', 'string', 'max:255'],
        ]);

        if (! empty($validated['company_website'])) {
            return back()->with('success', __('app.contact.success'));
        }

        unset($validated['company_website']);

        Log::info('contact_form_submission', $validated);
        AuditLog::record('contact.submitted', null, $validated, auth()->id());

        return back()->with('success', __('app.contact.success'));
    }

    public function switchLocale(string $locale, Request $request): RedirectResponse
    {
        if (in_array($locale, ['en', 'ar', 'tr'], true)) {
            $request->session()->put('locale', $locale);
        }

        return back();
    }

    private function staticPage(string $page): View
    {
        return view('pages.static', [
            'pageKey' => $page,
            'title' => trans("app.pages.$page.title"),
            'lead' => trans("app.pages.$page.lead"),
            'sections' => trans("app.pages.$page.sections"),
        ]);
    }
}
