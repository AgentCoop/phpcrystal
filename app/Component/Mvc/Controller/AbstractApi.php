<?php

namespace App\Component\Mvc\Controller;

use Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class AbstractApi extends BaseController
{
    const ERR_ACCESS_DENIED = 1;

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    use Common;

    /**
     * @return void
    */
    protected function checkIfAdmin()
    {
        if (Auth::check() && (Auth::user()->isAdmin())) {
            return;
        }

        throw new \RuntimeException('Access denied', self::ERR_ACCESS_DENIED);
    }

    /**
     *
    */
    protected function success($data = [])
    {
        $response = ['status' => 200];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response);
    }

    /**
     *
     */
    protected function forbidden()
    {
        return response()->json(null, 403);
    }

    /**
     *
     */
    protected function serverError($errMessage)
    {
        return response()->json(['error' => $errMessage], 500);
    }

    /**
     *
     */
    protected function badRequest($errMessage)
    {
        return response()->json(['error' => $errMessage], 400);
    }

    /**
     * The request requires user authentication
     */
    protected function unauthorized()
    {
        return response()->json(null, 401);
    }

    /**
     *
     */
    protected function seeOther()
    {
        return response()->json(null, 303);
    }
}
