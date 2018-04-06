<?php

namespace App\Frontoffice\Http\Controllers;

use Illuminate\Http\Request;

use App\Component\Mvc\Controller\AbstractView as Controller;

use App\Frontoffice\Services\View as ViewService;

class Index extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexPage(Request $request)
    {

        try {
            $data = [];

            $data = array_merge(
                ViewService\Index::create()->getData(),
                $data
            );

            return $this->i18View('pages.welcome', $data);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
