<?php

namespace App\TestModule\Http\Controllers;

use Illuminate\Http\Request;

use App\Component\Mvc\Controller\AbstractView as Controller;

use App\Frontoffice\Services\View as ViewService;

class Index extends Controller
{
    /**
     * @SecurityPolicy(roles="admin")
     * @Route("/", name="index")
     */
    public function indexPage(Request $request)
    {
        try {

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
