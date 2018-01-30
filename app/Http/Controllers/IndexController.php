<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\View\Frontend as ViewService;

class IndexController extends AbstractController
{
    /**
     *
     */
    public function index(Request $request)
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
