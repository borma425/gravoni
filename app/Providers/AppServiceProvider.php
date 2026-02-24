<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\Plugins\Shipping\Mylerz\MylerzService::class, function () {
            return new \Plugins\Shipping\Mylerz\MylerzService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure asset/storage URLs use the correct base (fixes subdirectory: /gravoni/public)
        if ($root = config('app.url')) {
            URL::forceRootUrl($root);
        }
    }
}
