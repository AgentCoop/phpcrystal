<?php

namespace App\Component\Mvc\Controller;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\View\FileViewFinder;
use Illuminate\Filesystem\Filesystem;

class AbstractView extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    use Common;

    /**
     *
     */
    protected function i18View($viewName, $data = [])
    {
        $locale = app()->getLocale();

        switch ($locale) { // Add supported localizations here
            default:
                $viewName .= '.index';
                break;
        }

        // Set view base directory
        $currentModule = $this->getCurrentModule();

        $viewBaseDir = $currentModule->getBaseDir() . join(DIRECTORY_SEPARATOR, ['', 'resources', 'views']);

        $files = new Filesystem;
        $viewFinder = new FileViewFinder($files, [$viewBaseDir]);
        resolve(\Illuminate\View\Factory::class)
            ->setFinder($viewFinder);

        return view($viewName, $data);

    }
}
