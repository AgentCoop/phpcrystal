<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PackageManager;

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

        // Load service providers
        $filename = storage_path(sprintf(PackageManager::SERVICE_PROVIDERS_DUMP_FILENAME, env('APP_ENV')));

        if (file_exists($filename)) {
            require $filename;
        }
    }
}
