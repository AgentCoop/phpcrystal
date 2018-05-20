<?php

namespace App\Component\Mvc\Controller;

use App\Services\PackageManager;

use App\Component\Package\Module\Module;

use App\Component\Exception\Loggable;

trait Common
{
    protected function getCurrentModule() : Module
    {
        return app()->make(PackageManager::class)
            ->getModuleByClassName(get_class($this));
    }

    /**
     *
     */
    protected function handleException(\Exception $e, Request $request = null, $withRedirect = null)
    {
        Loggable::createFromException($e)
            ->save();

        if ($request) {
            $request
                ->session()
                ->flash('errMessage', $e->getMessage())
                ->flash('errCode', $e->getCode());
        }

        if ($withRedirect) {
            return redirect()
                ->route($withRedirect['route'], $withRedirect['params']);
        } else {
            return response()
                ->setStatusCode(500);
        }
    }
}
