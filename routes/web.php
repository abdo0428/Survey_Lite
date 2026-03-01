<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SurveyController;
use App\Http\Controllers\Admin\SurveyQuestionController;
use App\Http\Controllers\Admin\SurveyResultsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSurveyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'landing'])->name('landing');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'submitContact'])
    ->middleware('throttle:contact-submit')
    ->name('contact.submit');
Route::get('/locale/{locale}', [PageController::class, 'switchLocale'])->name('locale.switch');

Route::get('/s/{token}', [PublicSurveyController::class, 'show'])->name('public.survey.show');
Route::post('/s/{token}', [PublicSurveyController::class, 'submit'])
    ->middleware('throttle:survey-submit')
    ->name('public.survey.submit');
Route::get('/s/{token}/thanks', [PublicSurveyController::class, 'thankYou'])->name('public.survey.thankyou');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('surveys', SurveyController::class);

        Route::get('surveys/{survey}/questions', [SurveyQuestionController::class, 'index'])->name('surveys.questions.index');
        Route::post('surveys/{survey}/questions', [SurveyQuestionController::class, 'store'])->name('surveys.questions.store');
        Route::put('surveys/{survey}/questions/{question}', [SurveyQuestionController::class, 'update'])->name('surveys.questions.update');
        Route::delete('surveys/{survey}/questions/{question}', [SurveyQuestionController::class, 'destroy'])->name('surveys.questions.destroy');
        Route::post('surveys/{survey}/questions/reorder', [SurveyQuestionController::class, 'reorder'])->name('surveys.questions.reorder');

        Route::get('surveys/{survey}/results', [SurveyResultsController::class, 'index'])->name('surveys.results.index');
        Route::get('surveys/{survey}/results/export', [SurveyResultsController::class, 'exportCsv'])->name('surveys.results.export');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
