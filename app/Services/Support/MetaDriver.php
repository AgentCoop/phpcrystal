<?php

namespace App\Services\Support;

use App\Services\Support\Filesystem\Scanner;
use App\Services\Support\Module\Manifest;
use App\Services\Support\Module\Module;

class MetaDriver
{
    private $modules = [];

    /**
     *
    */
    private function scanModules()
    {
        Scanner::findByFilename(base_path() . '/modules', 'manifest.php', function($manifest) {
            $this->modules[] = new Module(Manifest::createFromFile($manifest), dirname($manifest));
        })
            ->setMaxDepth(1)
            ->run();
    }

    /**
     *
    */
    public function run()
    {
        $this->scanModules();
    }

    /**
     * @return array
    */
    public function getModules()
    {
        return $this->modules;
    }
}