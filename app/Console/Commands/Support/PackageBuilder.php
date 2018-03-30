<?php

namespace App\Console\Commands\Support;

use App\Component\Package\Manager as PackageManager;

use App\Console\Commands\AbstractCommand;

class PackageBuilder extends AbstractCommand
{
    const TARGET_SERVICES = 'services';
    const TARGET_CONTROLLERS = 'controllers';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:build {--target=} {--production}';

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
        $prodEnv = $this->option('production');

        if ($prodEnv) {
            $env = PackageManager::PRODUCTION_ENV;
        } else {
            $env = PackageManager::LOCAL_ENV;
        }

        $target = $this->option('target');
        $packageManager->build($env, $target);
    }
}
