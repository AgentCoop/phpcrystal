<?php

namespace App\Component\Mvc\Controller;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AbstractView extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    use CommonTrait;

    /**
     *
     */
    protected function i18View($dirpath, $data = [])
    {
        $locale = app()->getLocale();

        switch ($locale) { // Add supported localizations here
            default:
                $viewName = 'index';
                break;
        }

        $viewPath = ltrim($dirpath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $viewName;

        return view($viewPath, $data);
    }
}
