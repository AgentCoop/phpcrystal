<?php

namespace App\Frontoffice\Http\Controllers;

use App\Services\PackageManager;
use Illuminate\Http\Request;

use App\Component\Mvc\Controller\AbstractView as Controller;

use App\Frontoffice\Services\View as ViewService;

/**
 * Class Index
 *
 * @SecurityPolicy(roles="admin")
 */
class Index extends Controller
{
    /**
     *
     * @Route("/", name="index", methods={"get", "post"})
     */
    public function indexPage(Request $request, PackageManager $packageManager)
    {

        try {
            $data = [];

            //$packageManager->build();

            $data = array_merge(
                ViewService\Index::create()->getData(),
                $data
            );

            return $this->i18View('pages.welcome', $data);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * @SecurityPolicy(disabled=true)
     *
     * @Route("/login", name="login_page", methods="get")
     */
    public function loginPage(Request $request, PackageManager $packageManager)
    {
        try {
            $data = [];

            $data = array_merge(
//                ViewService\Index::create()->getData(),
                $data
            );

            return $this->i18View('pages.login', $data);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
