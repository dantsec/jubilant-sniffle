<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

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
        Http::macro('punkapi', function() {
            /**
             * withHeaders([
             *  'accept' => 'application/json'
             * ])
             */
            return Http::acceptJson()
                ->baseUrl(config('punkapi.url'))
                ->retry(3, 100);
        });
    }
}
