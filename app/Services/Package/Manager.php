<?php

namespace App\Services\Package;

use App\Services\Filesystem\Finder;
use App\Services\Base\PhpParser;

class Manager
{
    const LOCAL_ENV = 'local';
    const PRODUCTION_ENV = 'prod';

    private $modules = [];

    /**
     * @return void
     */
    private function scanModules()
    {
        Finder::findByFilename(base_path() . '/modules', 'manifest.php', function($manifest) {
            $this->modules[] = new Module\Module(Module\Manifest::createFromFile($manifest), dirname($manifest));
        })
            ->setMaxDepth(1)
            ->run();
    }

    /**
     * @return void
     */
    private function dumpRoutingMap()
    {
        $timestamp = date('Y-m-d h:i:s');
        $mapContent = <<<DOC
<?php
//
//  Auto-generated on $timestamp, DO NOT modify this file     
//

DOC;
        foreach ($this->getModules() as $module) {
            $manifest = $module->getManifest();
            $subdomain = $manifest->getRouterSubDomain();
            $prefix = $manifest->getRouterUriPrefix();
            $record = sprintf('Route::middleware(%s)',
                PhpParser::toPhpArray($manifest->getRouterMiddlewares()));

            if ($subdomain) {
                $record .= "->domain('$subdomain')";
            }

            if ($prefix) {
                $record .= "->prefix('$prefix')";
            }

            $record .= sprintf('->group(function() { require %s; })',
                sprintf('base_path(\'routes/%s\')', basename($module->getRoutesDumpFilename())));

            $record .= ";\n";
            $mapContent .= $record;
        }

        file_put_contents(base_path('routes/map.php'), $mapContent);
    }

    /**
     *
    */
    public function __construct()
    {
        $this->scanModules();
    }

    /**
     * @return \App\Services\Package\Module\Module
     *
     * @throws \RuntimeException
    */
    public function getModuleByName($name)
    {
        foreach ($this->getModules() as $module) {
            if ($name == $module->getName()) {
                return $module;
            }
        }

        throw new \RuntimeException(sprintf('Failed to find package module %s', $name));
    }

    /**
     * @return void
     */
    public function build($env = self::LOCAL_ENV)
    {
        foreach ($this->getModules() as $module) {
            $module->build($env);
        }

        $this->dumpRoutingMap();
    }

    /**
     * @return array
     */
    public function getModules()
    {
        return $this->modules;
    }
}