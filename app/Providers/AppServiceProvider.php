<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();

        RateLimiter::for('survey-submit', function (Request $request) {
            $token = (string) $request->route('token');
            $identity = (string) ($request->user()?->id ?? $request->cookie('survey_fingerprint') ?? $request->ip());

            return Limit::perMinute(10)->by($token.'|'.$identity);
        });

        RateLimiter::for('contact-submit', function (Request $request) {
            $identity = (string) ($request->ip().'|'.$request->input('email'));

            return Limit::perMinute(5)->by($identity);
        });
    }
}
