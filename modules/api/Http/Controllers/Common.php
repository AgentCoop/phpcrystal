<?php

namespace App\Api\Http\Controllers;

use App\Component\Mvc\Controller\AbstractApi as Controller;

/**
 * @Middleware(group="web")
 * @Middleware(group="api")
*/
class Common extends Controller
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