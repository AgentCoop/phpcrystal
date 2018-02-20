<?php
namespace App\Services\Package\Module;

use App\Services\Package\Config;

class Manifest extends Config
{
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
    public function getRouterMiddlewares()
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