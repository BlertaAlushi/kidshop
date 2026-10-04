<?php

namespace App\Providers;

use App\Services\Products\PromotionPricingService;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // One instance per request so active promotions are only queried once.
        $this->app->scoped(PromotionPricingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::defaults(['locale' => Request::segment(1) ?: config('app.locale')]);
        Inertia::share(['locale' => fn () => app()->getLocale(),]);
    }
}
