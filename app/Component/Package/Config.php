<?php

namespace App\Component\Package;

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

    /**
     * Start section with conf variables for a service
    */
    final public function service($serviceName)
    {
        $this->setKeyPrefix($serviceName);
    }

    /**
     *
    */
    final public function close()
    {
        $this->setKeyPrefix(null);
    }
}
