<?php

namespace App\TestModule\Http\Controllers;

use Illuminate\Http\Request;

use App\Component\Mvc\Controller\AbstractView as Controller;

use App\Frontoffice\Services\View as ViewService;

/**
 * Class Admin
 *
 * @SecurityPolicy(roles="admin")
 */
class Admin extends Controller
{
    /**
     * @Route("/admin", name="admin_index")
     */
    public function indexPage(Request $request)
    {
        try {
            return response()->json();
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * @SecurityPolicy(disabled=true)
     *
     * @Route("/admin/login", name="admin_login")
     */
    public function loginPage(Request $request)
    {
        try {
            return response()->json();
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
