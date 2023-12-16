<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\RankingService;
use App\Services\GetDataService;

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
        $this->app->singleton(GetDataService::class, function ($app) {
            return new GetDataService();
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
