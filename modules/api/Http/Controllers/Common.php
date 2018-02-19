<?php

namespace App\Api\Http\Controllers;

/**
 * @Middleware(group="web")
 * @Middleware(group="api")
*/
class Common
{
    /**
     * @Route("/ping", name="api_ping")
    */
    public function pingAction()
    {
        return $this->success();
    }

    /**
     * @Route("/", name="api_index", methods={"get", "post"})
     */
    public function indexAction()
    {
        return $this->success();


    }
}