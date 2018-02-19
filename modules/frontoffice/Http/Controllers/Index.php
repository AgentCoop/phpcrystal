<?php

namespace App\Frontoffice\Http\Controllers;

use Illuminate\Http\Request;

use PhpCrystal\Core\Component\AbstractController;

use App\Frontoffice\Services\View as ViewService;

class Index extends AbstractController
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

            return $this->i18View('frontend.pages.welcome', $data);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
