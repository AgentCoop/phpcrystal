<?php

namespace App\Component\Mvc\Controller;

use App\Component\Exception\Loggable;

trait Common
{
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
