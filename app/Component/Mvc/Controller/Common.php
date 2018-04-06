<?php

namespace App\Component\Mvc\Controller;

use App\Services\PackageManager;

use App\Component\Exception\Loggable;

trait Common
{
    /**
     *
    */
    protected function getCurrentModule()
    {
        $packageManager = resolve(PackageManager::class);
        $controllersMap = $packageManager->getControllersMap();

        $moduleName = @$controllersMap[get_class($this)]['mod_name'];

        foreach ($packageManager->getModules() as $module) {
            if ($moduleName == $module->getName()) {
                return $module;
            }
        }

        throw new \RuntimeException(sprintf('Failed to retrieve current module'));
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
