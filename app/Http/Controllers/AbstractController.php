<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Exceptions\Loggable;

class AbstractController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     *
     */
    protected function handleException(\Exception $e, Request $request = null, $withRedirect = null)
    {
        Loggable::create($e->getMessage(), $e->getCode())
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
