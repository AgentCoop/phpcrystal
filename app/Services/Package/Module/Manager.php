<?php

namespace Lpb\Core\Services\Package\Module;

class Manager
{
    private $modules = [];

    /**
     * @return array
    */
    public function getModules()
    {
        return $this->modules;
    }
}