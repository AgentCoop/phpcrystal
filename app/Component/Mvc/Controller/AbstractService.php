<?php

namespace App\Component\Mvc\Controller;

use App\Component\Package\Manager as PackageManager;

abstract class AbstractService
{
    const TYPE_SIMPLE = 'simple';
    const TYPE_SINGLETON = 'singleton';

    const TYPES = [self::TYPE_SIMPLE, self::TYPE_SINGLETON];
    const TYPES_CONTAINER_CALLS_MAP = [self::TYPE_SIMPLE => 'bind', self::TYPE_SINGLETON => 'singleton'];

    /** @var string */
    private $moduleName;

    private $packageManager;

    /** @var bool */
    private $lazyInit= false;

    /**
     *
    */
    final public function __construct()
    {
        $this->packageManager = app()->make(PackageManager::class);

        if ( ! $this->lazyInit) {
            $this->init();
        }
    }

    /**
     *  Service initialization routine
    */
    public function init() : void
    {

    }

    /**
     *
    */
    final public function getLazyInit() : bool
    {
        return $this->lazyInit;
    }

    /**
     *
     */
    final public function setLazyInit($val) : self
    {
        $this->lazyInit = boolval($val);

        return $this;
    }

    /**
     * @return string
    */
    final public function getModuleName() : string
    {
        return $this->moduleName;
    }

    /**
     * @return $this
    */
    final public function setModuleName($moduleName) : self
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    /**
     * @return PackageManager
     */
    final public function getPackageManager() : PackageManager
    {
        return $this->packageManager;
    }

    /**
     *
    */
    public function getConfig()
    {
        $module = $this->getPackageManager()->getModuleByName($this->getModuleName());

        return $module->getManifest()->pluck(static::class);
    }
}
