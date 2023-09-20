<?php

namespace App\AzureAd;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AzureAdProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AzureAdAuthClient::class);

        $this->app->singleton(AzureAdGraphClient::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::extend('azure_ad', function (Application $app) {
            return new AzureAdGuard(
                $app->get(AzureAdGraphClient::class),
                $app->get('request'),
            );
        });
    }
}
