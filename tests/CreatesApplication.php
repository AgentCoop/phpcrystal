<?php

namespace Tests;

use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Console\Kernel;

use App\Component\Package\Module\Module;
use App\Component\Package\Module\Manifest;

use App\Services\PackageManager;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        Hash::setRounds(4);

        $packageManager = resolve(PackageManager::class);
        $packageManager
            ->addModule(new Module(Manifest::createFromFile(base_path('tests/Fixture/testmod/manifest.php'))))
            ->build(PackageManager::PHPUNIT_ENV)
        ;

        return $app;
    }
}
