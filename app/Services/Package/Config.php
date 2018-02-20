<?php

namespace App\Services\Package;

use App\Component\Base\AbstractContainer;

class Config extends AbstractContainer
{
    /**
     * @return string|null
    */
    public function getEnv()
    {
        return $this->get('env', null);
    }

    /**
     * @return $this
     */
    public function setEnv($env)
    {
        $this->set('env', $env);

        return $this;
    }
}
