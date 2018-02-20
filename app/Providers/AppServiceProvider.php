<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Package\Manager as PackageManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PackageManager::class, function($app) {
            return new PackageManager();
        });
    }
}
