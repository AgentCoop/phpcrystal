<?php

namespace PhpCrystal\Core\Services\Package;

use App\Services\Support\Filesystem\Scanner;
use PhpCrystal\Core\Services\Base\PhpParser;

class Manager
{
    private $modules = [];

    private $routesMapContent;

    /**
     * @return void
     */
    private function scanModules()
    {
        Scanner::findByFilename(base_path() . '/modules', 'manifest.php', function($manifest) {
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
     * @return \PhpCrystal\Core\Services\Package\Module\Module
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
    public function build()
    {
        foreach ($this->getModules() as $module) {
            $module->build();
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