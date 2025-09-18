<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Service\VnpayService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('VnpayService', function ($app) {
            return new VnpayService();
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
