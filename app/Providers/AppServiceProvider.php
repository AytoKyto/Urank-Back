<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\RankingService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RankingService::class, function ($app) {
            return new RankingService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
