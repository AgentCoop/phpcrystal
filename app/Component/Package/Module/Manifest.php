<?php
namespace App\Component\Package\Module;

use App\Component\Package\Config;

class Manifest extends Config
{
    /**
     *
    */
    public function getBaseDir() : string
    {
        return dirname($this->filename);
    }

    /**
     * @return string|null
    */
    public function getRouterUriPrefix()
    {
        return $this->get('router.prefix', null);
    }

    /**
     * @return array
     */
    public function getRouterMiddlewares() : array
    {
        return $this->get('router.middlewares', []);
    }

    /**
     * @return string|null
     */
    public function getRouterSubDomain()
    {
        return $this->get('router.subdomain', null);
    }
}
