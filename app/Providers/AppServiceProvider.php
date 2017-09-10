<?php

namespace App\Providers;
use App\Http\Controllers\MatchListingController;
use App\OpenLiga\Clients\HttpClient;
use App\OpenLiga\SeasonService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(HttpClient::class, function ($app) {
            return new HttpClient();
        });

        $this->app->bind(SeasonService::class, function ($app) {
            return new SeasonService($app->make(HttpClient::class));
        });

        $this->app->bind(MatchListingController::class, function ($app) {
            return new MatchListingController($app->make(SeasonService::class));
        });

    }
}
