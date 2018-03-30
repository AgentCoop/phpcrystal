<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Component\Package\Manager as PackageManager;

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
        $filename = storage_path(PackageManager::SERVICE_PROVIDERS_DUMP_FILENAME);

        if (file_exists($filename)) {
            require $filename;
        }
    }
}
