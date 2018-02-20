<?php

namespace App\Console\Commands;

use App\Services\Package\Manager as PackageManager;

class PackageBuilder extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:install {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build package';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(PackageManager $packageManager)
    {
        $moduleName = $this->option('module');
        $prodEnv = $this->option('production');

        if ($prodEnv) {
            $env = PackageManager::PRODUCTION_ENV;
        } else {
            $env = PackageManager::LOCAL_ENV;
        }

        if ( ! empty($moduleName)) {
            $module = $packageManager->getModuleByName($moduleName);
            $module->build($env);
        } else {
            $packageManager->build($env);
        }
    }
}
