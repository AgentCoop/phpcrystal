<?php

namespace App\Frontoffice\Http\Controllers;

use Illuminate\Http\Request;

use App\Component\Mvc\Controller\AbstractView as Controller;

use App\Frontoffice\Services\View as ViewService;
use App\Services\Package\Manager as PackageManager;

class Index extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexPage(Request $request, PackageManager $manager)
    {
        try {
            $data = [];

            $data = array_merge(
                ViewService\Index::create()->getData(),
                $data
            );

            $manager->build();

            return $this->i18View('frontend.pages.welcome', $data);
        } catch (\Exception $e) {
            var_dump($e); exit;
            return $this->handleException($e);
        }
    }
}
