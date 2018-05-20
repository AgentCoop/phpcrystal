<?php

namespace App\Component\Package;

use App\Component\Base\Creatable;

class ControllersMapItem
{
    use Creatable;

    private $moduleName;

    /**
     * @return string
     */
    public function getModuleName() : string
    {
        return $this->moduleName;
    }

    /**
     * @return $this
     */
    public function setModuleName($name) : self
    {
        $this->moduleName = $name;

        return $this;
    }
}